<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class addLogRequest extends FormRequest
{
    /**
     * Izinkan request ini (ubah menjadi true jika tidak menggunakan logic gate khusus).
     */
    public function authorize(): bool
    {
        // Ubah ke true agar request bisa diproses
        return true;
    }

    /**
     * Aturan validasi yang sesuai dengan field di React (TambahLog).
     */
    public function rules(): array
    {
        return [
            'mitra_id' => ['nullable', 'integer'],
            'dokumen_id' => 'required|exists:dokumen,id',
            'unit_id' => ['required', Rule::exists('unit', 'id')->whereNull('deleted_at')],
            'keterangan' => 'required|string|min:5',
            'tanggal_log' => 'required|date',
        ];
    }

    /**
     * Custom message dalam Bahasa Indonesia agar sesuai dengan UI.
     */
    public function messages(): array
    {
        return [
            'tanggal_log.required' => 'Tanggal aktivitas wajib diisi.',
            'tanggal_log.date' => 'Format tanggal tidak valid.',
            'keterangan.required' => 'Keterangan aktivitas tidak boleh kosong.',
            'keterangan.min' => 'Keterangan minimal berisi 5 karakter.',
            'unit_id.required' => 'Unit penginput harus diisi.',
            'unit_id.exists' => 'Unit yang dipilih tidak valid.',
            'dokumen_id.exists' => 'Dokumen yang dipilih tidak valid.',
        ];
    }
}
