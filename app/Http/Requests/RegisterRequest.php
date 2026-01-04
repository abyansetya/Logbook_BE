<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nim_nip' => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ];
    }

    /**
     * (Opsional tapi direkomendasikan)
     * Custom pesan error (lebih enak dibaca frontend)
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sama',
            'nim_nip.required' => 'NIM / NIP wajib diisi',
            'nim_nip.unique' => 'NIM / NIP sudah terdaftar',
        ];
    }
}
