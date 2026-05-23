<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Handles user registration.
 */
class RegisterController extends Controller
{
    /**
     * Register a new user in the system.
     *
     * @param RegisterRequest $request Validated registration data.
     * @return JsonResponse Response containing the new pending user data.
     * @throws \RuntimeException If the default viewer role is missing.
     */
    public function submitRegister(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Create user (password will be hashed automatically via cast)
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => $validated['password'], // Will be hashed by 'hashed' cast
                'nim_nip' => $validated['nim_nip'],
                'account_status' => 'pending',
            ]);

            // Attach default role: viewer
            $viewerRole = Role::where('nama', 'Viewer')->first();

            if (!$viewerRole) {
                throw new \RuntimeException('Default role "Viewer" not found in database');
            }

            $user->roles()->attach($viewerRole->id);

            // Load roles for response
            $user->load('roles');

            DB::commit();

            Log::info('User registered successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'message' => 'Registrasi berhasil. Akun Anda menunggu persetujuan admin.',
                'data' => [
                    'user' => $this->formatUserResponse($user),
                ],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Registration failed', [
                'email' => $validated['email'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            // Don't expose internal errors in production
            $message = app()->environment('production')
                ? 'Registrasi gagal, silakan coba lagi'
                : $e->getMessage();

            return response()->json([
                'message' => 'Registrasi gagal',
                'error' => $message,
            ], 500);
        }
    }

    /**
     * Format the user object for a consistent API response.
     *
     * @param User $user The user model to format.
     * @return array Formatted user data.
     */
    private function formatUserResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'nama' => $user->nama,
            'email' => $user->email,
            'nim_nip' => $user->nim_nip,
            'account_status' => $user->account_status,
            'roles' => $user->roles->pluck('nama')->toArray(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }
}
