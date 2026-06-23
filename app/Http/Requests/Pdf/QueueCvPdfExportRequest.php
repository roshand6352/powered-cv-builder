<?php

namespace App\Http\Requests\Pdf;

use Illuminate\Foundation\Http\FormRequest;

class QueueCvPdfExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view', $this->route('cv')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'template' => ['nullable', 'string', 'max:64'],
        ];
    }
}
