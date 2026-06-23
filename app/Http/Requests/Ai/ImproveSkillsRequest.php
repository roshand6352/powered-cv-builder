<?php

namespace App\Http\Requests\Ai;

use Illuminate\Foundation\Http\FormRequest;

class ImproveSkillsRequest extends FormRequest
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
            'skills' => ['required', 'array', 'min:1'],
            'skills.*.name' => ['required', 'string', 'max:255'],
            'skills.*.level' => ['nullable', 'string', 'max:100'],
        ];
    }
}
