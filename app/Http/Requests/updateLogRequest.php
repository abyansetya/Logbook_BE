<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateLogRequest extends FormRequest
{
    /**
     * Pastikan ini TRUE agar request diizinkan masuk
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id'        => 'required',
            'keterangan'     => 'required|string|min:5',
            'contact_person' => 'required|string|max:255',
            'tanggal_log'    => 'required|date',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'user_id.required'        => 'Admin penginput harus diisi.',
            'keterangan.required'     => 'Kolom keterangan wajib diisi.',
            'keterangan.min'          => 'Keterangan terlalu singkat, minimal 5 karakter.',
            'contact_person.required' => 'Nama contact person wajib diisi.',
            'contact_person.max'      => 'Nama contact person maksimal 255 karakter.',
            'tanggal_log.required'    => 'Tanggal log wajib diisi.',
            'tanggal_log.date'        => 'Format tanggal tidak valid.',
        ];
    }
}