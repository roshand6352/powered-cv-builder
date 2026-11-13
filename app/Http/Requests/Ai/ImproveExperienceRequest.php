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
            'description' => ['required', 'string', 'max:5000', new NonEmptyRichText],
        ];
    }
}
