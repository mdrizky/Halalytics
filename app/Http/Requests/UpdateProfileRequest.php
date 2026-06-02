<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'allergy' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:150',
            'height' => 'nullable|numeric|min:1|max:300',
            'weight' => 'nullable|numeric|min:1|max:500',
            'gender' => 'nullable|string|in:male,female,other',
            'birth_date' => 'nullable|date',
            'diet_preference' => 'nullable|string|max:255',
            'goal' => 'nullable|string|max:255',
            'activity_level' => 'nullable|string|in:sedentary,light,moderate,active,very_active',
            'language' => 'nullable|string|max:10',
            'bio' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'blood_type.in' => 'Golongan darah tidak valid. Gunakan: A+, A-, B+, B-, AB+, AB-, O+, O-',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'activity_level.in' => 'Tingkat aktivitas tidak valid.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
        ];
    }
}
