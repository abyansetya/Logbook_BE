<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addDokumenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'mitra_id' => ['required', 'exists:mitra,id'],
            'jenis_dokumen_id' => ['required', 'exists:jenis_dokumen,id'],
            'status_id' => ['required', 'exists:status,id'],
            'nomor_dokumen_mitra' => ['nullable', 'string', 'max:255'],
            'nomor_dokumen_undip' => [
            'nullable', 
            'string', 
            'max:255', 
            'unique:dokumen,nomor_dokumen_undip'
            ],
            'judul_dokumen' => ['required', 'string', 'max:255'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_terbit' => ['nullable', 'date'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'mitra_id.required' => 'Mitra harus dipilih.',
            'mitra_id.exists' => 'Mitra yang dipilih tidak valid.',
            'jenis_dokumen_id.required' => 'Jenis dokumen harus dipilih.',
            'jenis_dokumen_id.exists' => 'Jenis dokumen tidak ditemukan.',
            'status_id.required' => 'Status dokumen harus diisi.',
            'status_id.exists' => 'Status tidak valid.',
            'judul_dokumen.required' => 'Judul dokumen wajib diisi.',
            'judul_dokumen.max' => 'Judul dokumen maksimal 255 karakter.',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid.',
            'tanggal_terbit.date' => 'Format tanggal terbit tidak valid.',
            'nomor_dokumen_mitra.max' => 'Nomor dokumen mitra maksimal 255 karakter.',
            'nomor_dokumen_undip.max' => 'Nomor dokumen UNDIP maksimal 255 karakter.',
            'nomor_dokumen_undip.unique' => 'Nomor dokumen UNDIP sudah digunakan.',
        ];
    }
}