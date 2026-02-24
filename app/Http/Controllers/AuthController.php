<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Handles user authentication including registration, login, logout, and token management.
 */
class AuthController extends Controller
{
    /**
     * Token expiration in minutes (24 hours)
     */
    private const TOKEN_EXPIRATION_MINUTES = 60 * 24;

    /**
     * Register a new user in the system.
     *
     * @param RegisterRequest $request Validated registration data.
     * @return JsonResponse Response containing the new user data and authentication token.
     * @throws \RuntimeException If the default viewer role is missing.
     */
    public function register(RegisterRequest $request): JsonResponse
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
            ]);

            // Attach default role: viewer
            $viewerRole = Role::where('nama', 'viewer')->first();

            if (!$viewerRole) {
                throw new \RuntimeException('Default role "viewer" not found in database');
            }

            $user->roles()->attach($viewerRole->id);

            // Load roles for response
            $user->load('roles');

            // Create token with abilities and expiration
            $token = $user->createToken(
                'auth_token',
                ['*'], // abilities
                now()->addMinutes(self::TOKEN_EXPIRATION_MINUTES)
            )->plainTextToken;

            DB::commit();

            Log::info('User registered successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => $this->formatUserResponse($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => self::TOKEN_EXPIRATION_MINUTES * 60, // in seconds
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
     * Authenticate a user and issue a new API token.
     *
     * @param LoginRequest $request Validated login credentials.
     * @return JsonResponse Response containing user data and authentication token or error message.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Find user by email
        $user = User::where('email', strtolower($validated['email']))->first();

        // Check credentials (use same error message to prevent user enumeration)
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            Log::warning('Failed login attempt', ['email' => $validated['email']]);

            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Revoke all previous tokens for this user (single session)
        $user->tokens()->delete();

        // Load roles
        $user->load('roles');

        // Create new token with expiration
        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addMinutes(self::TOKEN_EXPIRATION_MINUTES)
        )->plainTextToken;

        Log::info('User logged in', ['user_id' => $user->id]);

        return response()->json([
            'message' => 'Login berhasil',
            'data' => [
                'user' => $this->formatUserResponse($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => self::TOKEN_EXPIRATION_MINUTES * 60,
            ],
        ]);
    }

    /**
     * Revoke the current authenticated user's access token.
     *
     * @param Request $request
     * @return JsonResponse Success or failure message.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke only the current token
            $request->user()->currentAccessToken()->delete();

            Log::info('User logged out', ['user_id' => $request->user()->id]);

            return response()->json([
                'message' => 'Logout berhasil',
            ]);

        } catch (\Throwable $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Logout gagal',
            ], 500);
        }
    }

    /**
     * Revoke all access tokens for the authenticated user across all devices.
     *
     * @param Request $request
     * @return JsonResponse Success message with count of revoked tokens.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tokenCount = $user->tokens()->count();

            // Revoke all tokens
            $user->tokens()->delete();

            Log::info('User logged out from all devices', [
                'user_id' => $user->id,
                'tokens_revoked' => $tokenCount,
            ]);

            return response()->json([
                'message' => 'Berhasil logout dari semua perangkat',
                'data' => [
                    'tokens_revoked' => $tokenCount,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Logout all failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Logout gagal',
            ], 500);
        }
    }

    /**
     * Get the profile data for the currently authenticated user.
     *
     * @param Request $request
     * @return JsonResponse Current user data including roles.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Cache data profil user (termasuk role) selama 1 jam
        $userData = Cache::remember("user_profile_{$user->id}", 3600, function () use ($user) {
            $user->load('roles');
            return $this->formatUserResponse($user);
        });

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'data' => [
                'user' => $userData,
            ],
        ]);
    }

    /**
     * Revoke the current token and issue a fresh one with a new expiration date.
     *
     * @param Request $request
     * @return JsonResponse New authentication token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Revoke current token
            $user->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addMinutes(self::TOKEN_EXPIRATION_MINUTES)
            )->plainTextToken;

            Log::info('Token refreshed', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Token berhasil diperbarui',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => self::TOKEN_EXPIRATION_MINUTES * 60,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Token refresh failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Gagal memperbarui token',
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
            'roles' => $user->roles->pluck('nama')->toArray(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }
}