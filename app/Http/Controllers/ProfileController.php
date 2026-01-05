<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfile(ProfileRequest $request)
    {
        DB::beginTransaction();

        try{

        $user = $request->user();

        $updatedData = $request->only(['nama', 'email']);

        if($request->filled('nim_nip')) {
            $updatedData['nim_nip'] = $request->input('nim_nip');
        }

        $user->update($updatedData);

        //reload user with roles
        $user->load('roles');

        DB::commit();

         return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'nim_nip' => $user->nim_nip,
                    'roles' => $user->roles->pluck('nama')->toArray(),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);
        } 
        
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ], 200);
    }
}
