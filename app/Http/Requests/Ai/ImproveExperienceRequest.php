<?php

namespace App\Http\Requests\Ai;

use App\Rules\NonEmptyRichText;
use Illuminate\Foundation\Http\FormRequest;

class ImproveExperienceRequest extends FormRequest
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
            'company' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'string', 'max:50'],
            'end_date' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:5000', new NonEmptyRichText],
        ];
    }
}
