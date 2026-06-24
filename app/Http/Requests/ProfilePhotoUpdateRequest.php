<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePhotoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Please choose a profile photo to upload.',
            'photo.image' => 'The file must be a valid image.',
            'photo.mimes' => 'Profile photos must be a JPG, JPEG, PNG, or WebP file.',
            'photo.max' => 'Profile photos may not be larger than 2MB.',
        ];
    }
}
