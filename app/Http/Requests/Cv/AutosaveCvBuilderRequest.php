<?php

namespace App\Http\Requests\Cv;

use App\Support\Cv\CvDocumentMigrator;
use App\Support\Cv\CvDocumentValidationRules;

class AutosaveCvBuilderRequest extends SaveCvBuilderRequest
{
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
        return [
            'title' => ['nullable', 'string', 'max:255'],
            ...CvDocumentValidationRules::rules(strict: false),
        ];
    }
}
