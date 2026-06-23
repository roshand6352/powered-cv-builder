<?php

namespace App\Http\Requests\Ai;

use Illuminate\Foundation\Http\FormRequest;

class GenerateCoverLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'job_description' => ['required', 'string', 'max:10000'],
            'tone' => ['nullable', 'string', 'max:100'],
            'personal_details' => ['nullable', 'array'],
            'experience' => ['nullable', 'array'],
            'skills' => ['nullable', 'array'],
        ];
    }
}
