<?php

namespace App\Http\Requests\CoverLetter;

use App\Models\CvTemplate;
use App\Services\CoverLetter\CoverLetterTemplateRegistry;
use Illuminate\Foundation\Http\FormRequest;

class StoreCoverLetterFromCvRequest extends FormRequest
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
            'template' => ['required', 'string', 'max:64'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function coverLetterTemplate(): CvTemplate
    {
        $template = app(CoverLetterTemplateRegistry::class)
            ->findActiveBySlug((string) $this->input('template'));

        abort_if($template === null, 404, 'Cover letter template not found.');

        return $template;
    }
}
