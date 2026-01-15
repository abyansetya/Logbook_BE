<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'user_id' => 'required',
            'mitra_id' => 'required',
            'dokumen_id' => 'required',
            'keterangan'     => 'required|string|min:5',
            'contact_person' => 'required|string|max:255',
            'tanggal_log'    => 'required|date',
        ];
    }

    /**
     * Custom message dalam Bahasa Indonesia agar sesuai dengan UI.
     */
    public function messages(): array
    {
        return [
            'tanggal_log.required'    => 'Tanggal aktivitas wajib diisi.',
            'tanggal_log.date'        => 'Format tanggal tidak valid.',
            'keterangan.required'     => 'Keterangan aktivitas tidak boleh kosong.',
            'keterangan.min'          => 'Keterangan minimal berisi 5 karakter.',
            'contact_person.required' => 'Contact person wajib dicantumkan.',
        ];
    }
}