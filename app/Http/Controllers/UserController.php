<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Manages administrative user tasks, specifically role management and account deletion.
 */
class UserController extends Controller
{
    /**
     * List all users with their assigned roles.
     *
     * @return JsonResponse List of users with IDs, names, emails, and roles.
     */
    public function getUsers(Request $request)
    {
        // Ensure only admin can access (Double check, besides route middleware)
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::with('role')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Data users berhasil diambil',
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'nim_nip' => $user->nim_nip,
                    'role' => $user->role?->nama,
                    'created_at' => $user->created_at,
                ];
            }),
        ]);
    }

    /**
     * Change a user's role and invalidate their profile cache.
     *
     * @param  Request  $request  Validated role name.
     * @param  int|string  $id  Target user ID.
     * @return JsonResponse Updated user and roles.
     */
    public function updateUserRole(Request $request, $id)
    {
        // Ensure only admin can access
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,nama',
        ]);

        $user = User::findOrFail($id);
        $newRoleName = $request->role; // 'admin' or 'viewer'

        // Prevent admin from removing their own admin role IF they are the only admin (optional safety)
        // For simplicity, we just allow it but log it.

        DB::beginTransaction();
        try {
            $role = Role::where('nama', $newRoleName)->firstOrFail();

            $user->update(['role_id' => $role->id]);
            $user->tokens()->delete();

            // Invalidate cache user yang rolenya diubah
            Cache::forget("user_profile_{$id}");

            DB::commit();

            Log::info('User role updated', [
                'admin_id' => $request->user()->id,
                'target_user_id' => $user->id,
                'new_role' => $newRoleName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role user berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'role' => $role->nama,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user role', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Gagal memperbarui role'], 500);
        }
    }

    /**
     * Permanently remove a user account and detach their roles.
     *
     * @param  Request  $request  Source admin request.
     * @param  int|string  $id  Target user ID.
     * @return JsonResponse Success or failure message.
     */
    public function deleteUser(Request $request, $id)
    {
        // Ensure only admin can access
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri'], 400);
        }

        DB::beginTransaction();
        try {
            $user->tokens()->delete();

            // Invalidate cache user yang dihapus
            Cache::forget("user_profile_{$id}");

            // Delete user
            $user->delete();

            DB::commit();

            Log::info('User deleted', [
                'admin_id' => $request->user()->id,
                'deleted_user_id' => $id,
                'deleted_user_email' => $user->email,
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
     * @param  Request  $request  Search query 'q'.
     * @return JsonResponse List of matching user resources.
     */
    public function searchUser(Request $request): JsonResponse
    {
        // Ensure only admin can access
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $query = $request->query('q');

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            $users = User::with('role')
                ->where('nama', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => UserResource::collection($users),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }
}
