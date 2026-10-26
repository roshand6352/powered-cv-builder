<?php

namespace App\Http\Requests\JobMatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobMatchRequest extends FormRequest
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
        $userId = $this->user()?->id;

        return [
            'job_description' => ['required', 'string'],
            'cv_id' => [
                'required',
                'integer',
                Rule::exists('cvs', 'id')->where('user_id', $userId),
            ],
            'cover_letter_id' => [
                'nullable',
                'integer',
                Rule::exists('cover_letters', 'id')->where('user_id', $userId),
            ],
        ];
    }
}
