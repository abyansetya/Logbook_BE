<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    /**
     * Mengambil semua daftar status dari database
     */
    public function getStatus(): JsonResponse
    {
        try {
            // Mengambil semua data status
            $status = Status::all();

            // Mengembalikan response sukses
            return response()->json([
                'success' => true,
                'message' => 'Daftar status berhasil diambil',
                'data'    => $status
            ], 200);

        } catch (\Exception $e) {
            // Mengembalikan response error jika terjadi masalah pada database
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data status',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}