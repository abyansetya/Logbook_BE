<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Manages partner (mitra) data, including registration, approval, and classification.
 */
class MitraController extends Controller
{
    /**
     * List all partners with filtering, search, and pagination.
     *
     * @param Request $request Filter parameters (q, klasifikasi, status, per_page).
     * @return JsonResponse Paginated list of partners.
     */
    public function getMitra(Request $request)
    {
        try {
            $query = Mitra::with('klasifikasiMitra'); // Eager load klasifikasi

            // Search logic
            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('alamat', 'LIKE', "%{$search}%")
                      ->orWhere('contact_person', 'LIKE', "%{$search}%");
                });
            }
            
            // Classification filter
            if ($request->has('klasifikasi') && $request->query('klasifikasi') !== 'all') {
                $klasifikasiId = $request->query('klasifikasi');
                $query->where('klasifikasi_mitra_id', $klasifikasiId);
            }

            // Status filter (Default to approved if not specified, or show all if 'all')
            if ($request->has('status') && $request->query('status') !== 'all') {
                 $status = $request->query('status');
                 if ($status === 'approved') {
                     $query->where(function($q) {
                         $q->where('status', 'approved')->orWhereNull('status');
                     });
                 } else {
                     $query->where('status', $status);
                 }
            }

            // Pagination (default 10, max 100)
            $perPage = min((int)$request->input('per_page', 10), 100);
            $mitras = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'List Data Mitra',
                'data' => $mitras
            ]);
        } catch (\Exception $e) {
            Log::error("Get Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mitra',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Register a new partner. Status is set to 'approved' for Admins, 'pending' otherwise.
     *
     * @param Request $request Partner details.
     * @return JsonResponse Created partner data.
     */
    public function addMitra(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mitra,nama',
            'klasifikasi_mitra_id' => 'required|exists:klasifikasi_mitra,id',
            'alamat' => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        try {
            // Set status based on user role
            $data = $request->all();
            if ($request->user() && $request->user()->hasRole('Admin')) {
                $data['status'] = 'approved';
                $message = 'Mitra berhasil ditambahkan';
            } else {
                $data['status'] = 'pending';
                $message = 'Mitra berhasil ditambahkan dan menunggu persetujuan';
            }

            $mitra = Mitra::create($data);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $mitra
            ], 201);
        } catch (\Exception $e) {
            Log::error("Create Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mitra',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Update an existing partner's information.
     *
     * @param Request $request Updated partner details.
     * @param int|string $id Partner ID.
     * @return JsonResponse Updated partner data.
     */
    public function updateMitra(Request $request, $id)
    {
        $request->validate([
            'nama' => 'string|max:255',
            'klasifikasi_mitra_id' => 'exists:klasifikasi_mitra,id',
            'alamat' => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitra tidak ditemukan'
                ], 404);
            }

            $data = $request->only(['nama', 'klasifikasi_mitra_id', 'alamat', 'contact_person']);

            if (
                $request->user()
                && $request->user()->hasRole('Operator')
                && !$request->user()->hasRole('Admin')
            ) {
                $data['status'] = 'pending';
            }

            $mitra->update($data);

            return response()->json([
                'success' => true,
                'message' => $mitra->status === 'pending'
                    ? 'Mitra berhasil diperbarui dan menunggu persetujuan admin'
                    : 'Mitra berhasil diperbarui',
                'data' => $mitra
            ]);
        } catch (\Exception $e) {
            Log::error("Update Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mitra',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Remove a partner from the system.
     *
     * @param int|string $id Partner ID.
     * @return JsonResponse Success or failure message.
     */
    public function deleteMitra($id)
    {
        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitra tidak ditemukan'
                ], 404);
            }

            $mitra->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error("Delete Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mitra',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Approve a pending partner registration.
     *
     * @param int|string $id Partner ID.
     * @return JsonResponse Updated partner status.
     */
    public function approveMitra($id)
    {
        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return response()->json(['message' => 'Mitra not found'], 404);
            }

            $mitra->status = 'approved';
            $mitra->save();

            return response()->json([
                'success' => true,
                'message' => 'Mitra approved successfully',
                'data' => $mitra
            ]);
        } catch (\Exception $e) {
            Log::error("Approve Mitra Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to approve mitra'], 500);
        }
    }

    /**
     * Reject and delete a pending partner registration.
     *
     * @param int|string $id Partner ID.
     * @return JsonResponse Success message.
     */
    public function rejectMitra($id)
    {
        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return response()->json(['message' => 'Mitra not found'], 404);
            }

            $mitra->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil ditolak dan dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error("Reject Mitra Error: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menolak mitra'], 500);
        }
    }

    /**
     * Search for partners by name for autocomplete or selection.
     *
     * @param Request $request Search query 'q'.
     * @return JsonResponse List of matching partners.
     */
     public function searchMitra(Request $request)
    {
        try {
            // Ambil nilai dari ?q=bank
            $query = $request->query('q');

            // Jika q kosong, kembalikan array kosong agar frontend tidak error
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Lakukan pencarian di database
            // Pastikan kolom di tabel mitra memang bernama 'nama'
            $mitras = Mitra::where('nama', 'LIKE', "%{$query}%")
                ->limit(10) // Opsional: batasi hasil agar cepat
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mitras
            ]);
            
        } catch (\Exception $e) {
            // Log error agar bisa dicek di storage/logs/laravel.log
            Log::error("Search Mitra Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Fast-track partner registration with a default classification.
     *
     * @param Request $request Partner name.
     * @return JsonResponse Created partner data.
     */
    public function addMitraQuick(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mitra,nama',
            'klasifikasi_mitra_id' => 'nullable|exists:klasifikasi_mitra,id',
        ]);

        try {
            $status = ($request->user() && $request->user()->hasRole('Admin')) ? 'approved' : 'pending';

            $mitra = Mitra::create([
                'nama' => $request->input('nama'),
                'klasifikasi_mitra_id' => $request->input('klasifikasi_mitra_id', 16),
                'status' => $status
            ]);

            $message = ($status === 'approved') 
                ? 'Mitra berhasil ditambahkan' 
                : 'Mitra berhasil ditambahkan dan menunggu persetujuan';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $mitra
            ], 201);
        } catch (\Exception $e) {
            Log::error("Add Mitra Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mitra',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}
