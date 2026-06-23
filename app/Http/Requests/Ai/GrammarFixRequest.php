<?php

namespace App\Http\Requests\Ai;

use App\Rules\NonEmptyRichText;
use Illuminate\Foundation\Http\FormRequest;

class GrammarFixRequest extends FormRequest
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
            'text' => ['required', 'string', 'max:10000', new NonEmptyRichText],
        ];
    }
}
