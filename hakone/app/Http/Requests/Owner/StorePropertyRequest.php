<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama properti wajib diisi.',
            'name.string' => 'Nama properti harus berupa teks.',
            'name.max' => 'Nama properti tidak boleh lebih dari 150 karakter.',

            'property_type_id.exists' => 'Tipe properti tidak valid.',

            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat tidak boleh lebih dari 500 karakter.',

            'city.required' => 'Kota wajib diisi.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota tidak boleh lebih dari 100 karakter.',

            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 2000 karakter.',

            'cover_image.file' => 'Gambar sampul harus berupa file.',
            'cover_image.mimes' => 'Gambar sampul harus berformat jpeg, jpg, png, atau webp.',
            'cover_image.max' => 'Gambar sampul maksimal berukuran 2MB.',

            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus bernilai active atau inactive.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama properti',
            'property_type_id' => 'tipe properti',
            'address' => 'alamat',
            'city' => 'kota',
            'description' => 'deskripsi',
            'cover_image' => 'gambar sampul',
            'status' => 'status',
        ];
    }
}
