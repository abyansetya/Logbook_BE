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

class UserController extends Controller
{
    /**
     * Get all users with their roles
     */
    public function index(Request $request)
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
                    'roles' => $user->roles->pluck('nama'), // Send array of role names
                    'created_at' => $user->created_at,
                ];
            }),
        ]);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, $id)
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

        // Prevent admin from removing their own admin role IF they are the only admin (optional safety)
        // For simplicity, we just allow it but log it.

        DB::beginTransaction();
        try {
            $role = Role::where('nama', $newRoleName)->firstOrFail();

            // Sync roles (replaces existing roles with the new one)
            // Assuming a user only has one primary role for this system context
            $user->roles()->sync([$role->id]);

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
     * Delete user
     */
    public function destroy(Request $request, $id)
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
    public function searchUser(Request $request): JsonResponse
    {
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
}
