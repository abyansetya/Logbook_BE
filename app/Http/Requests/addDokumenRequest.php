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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nomor_dokumen_mitra' => $this->nomor_dokumen_mitra === '-' ? null : $this->nomor_dokumen_mitra,
            'nomor_dokumen_undip' => $this->nomor_dokumen_undip === '-' ? null : $this->nomor_dokumen_undip,
        ]);
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
                'unique:dokumen,nomor_dokumen_undip',
            ],
            'judul_dokumen' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'tanggal_dokumen' => ['nullable', 'date'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_terbit' => ['nullable', 'date'],
            'draft_dokumen' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
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
            'contact_person.max' => 'Nama contact person maksimal 255 karakter.',
            'tanggal_dokumen.date' => 'Format tanggal dokumen tidak valid.',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid.',
            'tanggal_terbit.date' => 'Format tanggal terbit tidak valid.',
            'nomor_dokumen_mitra.max' => 'Nomor dokumen mitra maksimal 255 karakter.',
            'nomor_dokumen_undip.max' => 'Nomor dokumen UNDIP maksimal 255 karakter.',
            'nomor_dokumen_undip.unique' => 'Nomor dokumen UNDIP sudah digunakan.',
            'draft_dokumen.file' => 'Draft dokumen harus berupa file.',
            'draft_dokumen.mimes' => 'Draft dokumen harus berformat PDF.',
            'draft_dokumen.max' => 'Ukuran draft dokumen maksimal 2MB.',
        ];
    }
}
