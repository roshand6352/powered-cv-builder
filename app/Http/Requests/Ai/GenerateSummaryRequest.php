<?php

namespace App\Http\Requests\Ai;

use App\Rules\NonEmptyRichText;
use Illuminate\Foundation\Http\FormRequest;

class GenerateSummaryRequest extends FormRequest
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
            'personal_details' => ['nullable', 'array'],
            'personal_details.summary' => ['required', 'string', 'max:5000', new NonEmptyRichText],
            'summary' => ['sometimes', 'string', 'max:5000', new NonEmptyRichText],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('summary') && ! $this->filled('personal_details.summary')) {
            $this->merge([
                'personal_details' => array_merge($this->input('personal_details', []), [
                    'summary' => $this->input('summary'),
                ]),
            ]);
        }
    }
}
