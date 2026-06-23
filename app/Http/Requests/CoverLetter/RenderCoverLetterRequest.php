<?php

namespace App\Http\Requests\CoverLetter;

use Illuminate\Foundation\Http\FormRequest;

class RenderCoverLetterRequest extends FormRequest
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
            'template' => ['nullable', 'string', 'max:64'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'hiring_manager_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:2000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'greeting' => ['nullable', 'string', 'max:255'],
            'introduction' => ['nullable', 'string', 'max:5000'],
            'body' => ['nullable', 'string', 'max:20000'],
            'closing_paragraph' => ['nullable', 'string', 'max:2000'],
            'signature_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function overrides(): array
    {
        return collect($this->validated())
            ->except('template')
            ->all();
    }
}
