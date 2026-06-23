<?php

namespace App\Http\Requests\Cv;

use App\Support\Cv\CvDocumentMigrator;
use App\Support\Cv\CvDocumentValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class SaveCvBuilderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('cv')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $jsonData = $this->input('json_data');

        if (is_array($jsonData)) {
            $this->merge([
                'json_data' => CvDocumentMigrator::migrate($jsonData),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $publish = $this->boolean('publish');

        return [
            'title' => [$publish ? 'required' : 'nullable', 'string', 'max:255'],
            'publish' => ['sometimes', 'boolean'],
            ...CvDocumentValidationRules::rules($publish),
        ];
    }
}
