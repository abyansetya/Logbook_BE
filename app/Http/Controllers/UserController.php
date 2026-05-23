<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Manages administrative user tasks, specifically role management and account deletion.
 */
class UserController extends Controller
{
    /**
     * List all users with their assigned roles.
     *
     * @param Request $request
     * @return JsonResponse List of users with IDs, names, emails, and roles.
     */
    public function getUsers(Request $request)
    {
        // Ensure only admin can access (Double check, besides route middleware)
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::with('roles')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Data users berhasil diambil',
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'nim_nip' => $user->nim_nip,
                    'account_status' => $user->account_status,
                    'roles' => $user->roles->pluck('nama'), // Send array of role names
                    'created_at' => $user->created_at,
                ];
            }),
        ]);
    }

    /**
     * Change a user's role and invalidate their profile cache.
     *
     * @param Request $request Validated role name.
     * @param int|string $id Target user ID.
     * @return JsonResponse Updated user and roles.
     */
    public function updateUserRole(Request $request, $id)
    {
        // Ensure only admin can access
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,nama',
        ]);

        $user = User::findOrFail($id);
        $newRoleName = $request->role; // 'admin' or 'viewer'

        if ($user->account_status !== 'approved') {
            return response()->json([
                'message' => 'Role hanya dapat diubah untuk akun yang sudah disetujui',
            ], 422);
        }

        // Prevent admin from removing their own admin role IF they are the only admin (optional safety)
        // For simplicity, we just allow it but log it.

        DB::beginTransaction();
        try {
            $role = Role::where('nama', $newRoleName)->firstOrFail();

            // Sync roles (replaces existing roles with the new one)
            // Assuming a user only has one primary role for this system context
            $user->roles()->sync([$role->id]);

            // Invalidate cache user yang rolenya diubah
            Cache::forget("user_profile_{$id}");

            DB::commit();

            Log::info('User role updated', [
                'admin_id' => $request->user()->id,
                'target_user_id' => $user->id,
                'new_role' => $newRoleName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role user berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'roles' => $user->roles->pluck('nama'),
                ]   
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user role', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui role'], 500);
        }
    }

    /**
     * Approve a pending user account so they can login.
     *
     * @param Request $request Source admin request.
     * @param int|string $id Target user ID.
     * @return JsonResponse Updated account status.
     */
    public function approveUser(Request $request, $id): JsonResponse
    {
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::with('roles')->findOrFail($id);

        $user->update(['account_status' => 'approved']);
        Cache::forget("user_profile_{$id}");

        Log::info('User account approved', [
            'admin_id' => $request->user()->id,
            'target_user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun pengguna berhasil disetujui',
            'data' => $this->formatUser($user->fresh('roles')),
        ]);
    }

    /**
     * Reject a user account and revoke existing tokens.
     *
     * @param Request $request Source admin request.
     * @param int|string $id Target user ID.
     * @return JsonResponse Updated account status.
     */
    public function rejectUser(Request $request, $id): JsonResponse
    {
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::with('roles')->findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak dapat menolak akun sendiri'], 400);
        }

        $user->update(['account_status' => 'rejected']);
        $user->tokens()->delete();
        Cache::forget("user_profile_{$id}");

        Log::info('User account rejected', [
            'admin_id' => $request->user()->id,
            'target_user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun pengguna berhasil ditolak',
            'data' => $this->formatUser($user->fresh('roles')),
        ]);
    }

    /**
     * Permanently remove a user account and detach their roles.
     *
     * @param Request $request Source admin request.
     * @param int|string $id Target user ID.
     * @return JsonResponse Success or failure message.
     */
    public function deleteUser(Request $request, $id)
    {
        // Ensure only admin can access
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri'], 400);
        }

        DB::beginTransaction();
        try {
            // Delete related data if needed (e.g. detach roles)
            $user->roles()->detach();
            $user->tokens()->delete();
            
            // Invalidate cache user yang dihapus
            Cache::forget("user_profile_{$id}");
            
            // Delete user
            $user->delete();

            DB::commit();

            Log::info('User deleted', [
                'admin_id' => $request->user()->id,
                'deleted_user_id' => $id,
                'deleted_user_email' => $user->email
            ]);

            return response()->json([
                'message' => 'User berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus user'], 500);
        }
    }

    // Search User
    /**
     * Search for users by name for selection or administration.
     *
     * @param Request $request Search query 'q'.
     * @return JsonResponse List of matching user resources.
     */
    public function searchUser(Request $request): JsonResponse
    {
        // Ensure only admin can access
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $query = $request->query('q');

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $users = User::with('roles')
                ->where('nama', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('nim_nip', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' =>   UserResource::collection($users)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'nama' => $user->nama,
            'email' => $user->email,
            'nim_nip' => $user->nim_nip,
            'account_status' => $user->account_status,
            'roles' => $user->roles->pluck('nama'),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
