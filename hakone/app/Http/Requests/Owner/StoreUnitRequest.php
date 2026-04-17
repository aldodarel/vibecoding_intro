<?php

namespace App\Http\Requests\Owner;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user?->isOwner()) {
            return false;
        }

        $property = $this->route('property');

        if ($property instanceof Property) {
            return $property->owner_id === $user->id;
        }

        return Property::where('id', $property)
            ->where('owner_id', $user->id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'floor' => ['nullable', 'integer', 'min:1', 'max:99'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:50'],
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
            'name.required' => 'Nama unit wajib diisi.',
            'name.string' => 'Nama unit harus berupa teks.',
            'name.max' => 'Nama unit tidak boleh lebih dari 50 karakter.',

            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 500 karakter.',

            'price.required' => 'Harga sewa wajib diisi.',
            'price.integer' => 'Harga sewa harus berupa angka.',
            'price.min' => 'Harga sewa tidak boleh negatif.',

            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus bernilai available, occupied, atau maintenance.',

            'floor.integer' => 'Lantai harus berupa angka.',
            'floor.min' => 'Lantai harus bernilai minimal 1.',
            'floor.max' => 'Lantai harus bernilai maksimal 99.',

            'facilities.array' => 'Fasilitas harus berupa daftar.',
            'facilities.*.string' => 'Setiap fasilitas harus berupa teks.',
            'facilities.*.max' => 'Setiap fasilitas tidak boleh lebih dari 50 karakter.',
        ];
    }
}
