<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
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
     * Authenticate a user and issue a new API token.
     *
     * @param LoginRequest $request Validated login credentials.
     * @return JsonResponse Response containing user data and authentication token or error message.
     */
    public function submitLogin(LoginRequest $request): JsonResponse
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

        if ($user->account_status !== 'approved') {
            Log::warning('Blocked login for unapproved account', [
                'user_id' => $user->id,
                'account_status' => $user->account_status,
            ]);

            $message = match ($user->account_status) {
                'pending' => 'Akun Anda masih menunggu persetujuan admin',
                'rejected' => 'Registrasi akun Anda ditolak admin',
                default => 'Akun Anda belum aktif',
            };

            return response()->json([
                'message' => $message,
            ], 403);
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
    public function submitLogout(Request $request): JsonResponse
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
    public function submitLogoutAll(Request $request): JsonResponse
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
    public function getMe(Request $request): JsonResponse
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
    public function refreshToken(Request $request): JsonResponse
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
            'account_status' => $user->account_status,
            'roles' => $user->roles->pluck('nama')->toArray(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }
}
