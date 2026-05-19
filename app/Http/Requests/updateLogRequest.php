<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
        $id = $this->route('id') ?: $this->id ?: request()->route('id');

        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'user_id' => 'required|exists:users,id',
            'unit_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $isCurrentValue = DB::table('log')
                        ->where('id', $id)
                        ->where('unit_id', $value)
                        ->exists();

                    if ($isCurrentValue) {
                        return;
                    }

                    $isActiveUnit = DB::table('unit')
                        ->where('id', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if (! $isActiveUnit) {
                        $fail('Unit yang dipilih tidak valid.');
                    }
                },
            ],
            'keterangan' => 'required|string|min:5',
            'tanggal_log' => 'required|date',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Admin penginput harus diisi.',
            'user_id.exists' => 'Admin yang dipilih tidak valid.',
            'keterangan.required' => 'Kolom keterangan wajib diisi.',
            'keterangan.min' => 'Keterangan terlalu singkat, minimal 5 karakter.',
            'tanggal_log.required' => 'Tanggal log wajib diisi.',
            'tanggal_log.date' => 'Format tanggal tidak valid.',
            'unit_id.exists' => 'Unit yang dipilih tidak valid.',
        ];
    }
}
