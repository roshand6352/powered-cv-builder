<?php

namespace App\Http\Requests\Cv;

use App\Services\Cv\Templates\TemplateRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SwitchCvTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('cv')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'template' => [
                'required',
                'string',
                Rule::in(app(TemplateRegistry::class)->keys()),
            ],
            'json_data' => ['nullable', 'array'],
        ];
    }
}
