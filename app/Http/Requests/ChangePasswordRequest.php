<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            //  password lama salah
            if (!Hash::check($this->current_password, $user->password)) {
                $validator->errors()->add(
                    'current_password',
                    'Password saat ini salah'
                );
            }

            //  password baru sama dengan password lama
            if (Hash::check($this->new_password, $user->password)) {
                $validator->errors()->add(
                    'new_password',
                    'Password baru tidak boleh sama dengan password lama'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sama',
        ];
    }
}
