<?php

namespace App\Http\Requests\Cv;

class RenderCvPreviewRequest extends AutosaveCvBuilderRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'template' => ['nullable', 'string', 'in:'.implode(',', app(\App\Services\Cv\Templates\TemplateRegistry::class)->keys())],
            'preview_html' => ['nullable', 'string', 'max:3145728'],
        ];
    }
}
