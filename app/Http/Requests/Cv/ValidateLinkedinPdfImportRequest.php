<?php

namespace App\Http\Requests\Cv;

use App\Services\Subscription\SubscriptionService;
use Illuminate\Foundation\Http\FormRequest;

class ValidateLinkedinPdfImportRequest extends FormRequest
{
    protected $errorBag = 'linkedinImport';

    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        return ! app(SubscriptionService::class)->resolveActivePlan($user)->isFree();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'linkedin_pdf' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'linkedin_pdf.required' => 'Please choose a LinkedIn profile PDF to upload.',
            'linkedin_pdf.file' => 'The selected file is invalid.',
            'linkedin_pdf.mimes' => 'Only PDF files are accepted.',
            'linkedin_pdf.max' => 'The PDF may not be larger than 5 MB.',
        ];
    }
}
