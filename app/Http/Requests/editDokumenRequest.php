<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class editDokumenRequest extends FormRequest
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
            'nomor_dokumen_mitra' => ($this->nomor_dokumen_mitra === '-' || $this->nomor_dokumen_mitra === '') ? null : $this->nomor_dokumen_mitra,
            'nomor_dokumen_undip' => ($this->nomor_dokumen_undip === '-' || $this->nomor_dokumen_undip === '') ? null : $this->nomor_dokumen_undip,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Mencoba berbagai cara untuk mendapatkan ID dokumen dari route
        $id = $this->route('id') ?: $this->id ?: request()->route('id');

        // Jika $id adalah instance model (karena route model binding), ambil ID-nya
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'mitra_id' => [
                'required',
                function ($attribute, $value, $fail) use ($id) {
                    $isCurrentValue = DB::table('dokumen')
                        ->where('id', $id)
                        ->where('mitra_id', $value)
                        ->exists();

                    if ($isCurrentValue) {
                        return;
                    }

                    $isActiveMitra = DB::table('mitra')
                        ->where('id', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if (! $isActiveMitra) {
                        $fail('Mitra yang dipilih tidak valid.');
                    }
                },
            ],
            'jenis_dokumen_id' => ['required', 'exists:jenis_dokumen,id'],
            'status_id' => [
                'required',
                function ($attribute, $value, $fail) use ($id) {
                    $isCurrentValue = DB::table('dokumen')
                        ->where('id', $id)
                        ->where('status_id', $value)
                        ->exists();

                    if ($isCurrentValue) {
                        return;
                    }

                    $isActiveStatus = DB::table('status')
                        ->where('id', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if (! $isActiveStatus) {
                        $fail('Status tidak valid.');
                    }
                },
            ],
            'nomor_dokumen_mitra' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = DB::table('dokumen')
                        ->where('nomor_dokumen_mitra', $value)
                        ->where('id', '<>', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Nomor dokumen mitra sudah digunakan.');
                    }
                },
            ],
            'nomor_dokumen_undip' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = DB::table('dokumen')
                        ->where('nomor_dokumen_undip', $value)
                        ->where('id', '<>', $id)
                        ->exists();
                    if ($exists) {
                        $fail("Nomor dokumen UNDIP sudah digunakan (ID: $id, Val: $value).");
                    }
                },
            ],
            'judul_dokumen' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'tanggal_dokumen' => ['nullable', 'date'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_terbit' => ['nullable', 'date'],
            'draft_dokumen' => $this->hasFile('draft_dokumen') ? ['file', 'mimes:pdf', 'max:2048'] : ['nullable', 'string'],
            'final_dokumen' => $this->hasFile('final_dokumen') ? ['file', 'mimes:pdf', 'max:2048'] : ['nullable', 'string'],
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $id = $this->route('id') ?: $this->id ?: request()->route('id');
        if (is_object($id)) {
            $id = $id->id;
        }

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
            'nomor_dokumen_mitra.unique' => 'Nomor dokumen mitra sudah digunakan.',
            'nomor_dokumen_undip.max' => 'Nomor dokumen UNDIP maksimal 255 karakter.',
            'nomor_dokumen_undip.unique' => 'Nomor dokumen UNDIP sudah digunakan (ID: '.($id ?? 'NULL').').',
            'draft_dokumen.file' => 'Draft dokumen harus berupa file.',
            'draft_dokumen.mimes' => 'Draft dokumen harus berformat PDF.',
            'draft_dokumen.max' => 'Ukuran draft dokumen maksimal 2MB.',
            'final_dokumen.file' => 'Dokumen final harus berupa file.',
            'final_dokumen.mimes' => 'Dokumen final harus berformat PDF.',
            'final_dokumen.max' => 'Ukuran dokumen final maksimal 2MB.',
        ];
    }
}
