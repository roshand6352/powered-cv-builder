<?php

namespace App\Http\Requests\CoverLetter;

use Illuminate\Foundation\Http\FormRequest;

class GenerateCoverLetterContentRequest extends FormRequest
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
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'job_description' => ['nullable', 'string', 'max:10000'],
            'tone' => ['nullable', 'string', 'max:100'],
        ];
    }
}
