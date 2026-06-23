<?php

namespace App\Services\CoverLetter;

use App\Models\CoverLetter;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Pdf\PdfWatermarkService;
use App\Support\CoverLetter\CoverLetterTemplateLayout;
use App\Support\Html\CoverLetterRichTextCleaner;
use Illuminate\Support\Facades\View;

/**
 * Single source of truth for cover letter HTML. Used by both the live preview
 * endpoint and the PDF generator so the exported document matches the preview.
 */
class CoverLetterRenderer extends BaseService
{
    public function __construct(
        protected CoverLetterCvExtractor $cvExtractor,
        protected CoverLetterTemplateRegistry $templateRegistry,
        protected PdfWatermarkService $watermarkService,
    ) {}

    /**
     * @param  array<string, mixed>  $overrides
     * @param  array<string, mixed>  $options
     */
    public function render(CoverLetter $coverLetter, array $overrides = [], array $options = []): string
    {
        $applyWatermark = $options['watermark'] ?? false;

        return View::make('web.cover-letter.document', [
            'data' => $this->buildViewData($coverLetter, $overrides, $options['template'] ?? null),
            'watermark' => (bool) $applyWatermark,
            'watermarkText' => config('cv-templates.watermark.primary_text', 'FREE VERSION'),
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function buildViewData(CoverLetter $coverLetter, array $overrides = [], ?string $templateKey = null): array
    {
        $layout = $this->resolveLayout($coverLetter, $templateKey);
        $settings = $layout['settings'] ?? CoverLetterTemplateLayout::defaultSettings();

        $cv = $coverLetter->cv;
        $sender = $cv ? $this->cvExtractor->senderBlock($cv) : $this->emptySender();

        $content = $this->resolveContent($coverLetter, $overrides);

        $signature = trim((string) $content['signature_name']);
        if ($signature === '') {
            $signature = $sender['full_name'];
        }

        return [
            'layout' => $layout['layout'] ?? 'professional',
            'settings' => $settings,
            'sections' => [
                'order' => $layout['section_order'] ?? CoverLetterTemplateLayout::sectionKeys(),
                'enabled' => $layout['enabled_sections'] ?? CoverLetterTemplateLayout::sectionKeys(),
            ],
            'sender' => $sender,
            'date' => now()->format('F j, Y'),
            'recipient' => [
                'name' => $content['hiring_manager_name'],
                'company' => $content['company_name'],
                'address' => $content['company_address'],
            ],
            'job_title' => $content['job_title'],
            'subject' => $content['subject'],
            'greeting' => $content['greeting'],
            'introduction' => $content['introduction'],
            'body' => $content['body'],
            'closing' => $content['closing_paragraph'],
            'signature' => $signature,
        ];
    }

    /**
     * Render a template preview using realistic dummy content (template selection page).
     */
    public function renderSample(CvTemplate $template): string
    {
        $layout = CoverLetterTemplateLayout::normalize($template->layout_json);

        $data = [
            'layout' => $layout['layout'],
            'settings' => $layout['settings'],
            'sections' => [
                'order' => $layout['section_order'],
                'enabled' => $layout['enabled_sections'],
            ],
            'sender' => [
                'full_name' => 'John Smith',
                'job_title' => 'Senior Laravel Developer',
                'email' => 'john.smith@example.com',
                'phone' => '+1 (555) 010-2030',
                'location' => 'San Francisco, CA',
                'website' => 'johnsmith.dev',
                'linkedin' => 'linkedin.com/in/johnsmith',
            ],
            'date' => now()->format('F j, Y'),
            'recipient' => [
                'name' => 'Hiring Manager',
                'company' => 'Acme Corporation',
                'address' => "123 Market Street\nSan Francisco, CA 94105",
            ],
            'job_title' => 'Senior Laravel Developer',
            'subject' => 'Application for Laravel Developer Position',
            'greeting' => 'Dear Hiring Manager,',
            'introduction' => 'I am excited to apply for the Laravel Developer position at Acme Corporation. With over five years of experience building scalable web applications, I am confident I can make an immediate impact on your team.',
            'body' => "In my current role at ABC Technologies, I led the development of a multi-tenant SaaS platform serving more than 50,000 users, where I architected REST APIs and improved response times by 40%.\n\nI thrive in collaborative environments and have a strong track record of mentoring developers, establishing best practices, and shipping reliable, well-tested code. Thank you for your consideration — I look forward to discussing how I can contribute to your team.",
            'closing' => 'Sincerely,',
            'signature' => 'John Smith',
        ];

        return View::make('web.cover-letter.document', [
            'data' => $data,
            'watermark' => false,
            'watermarkText' => '',
        ])->render();
    }

    public function shouldWatermarkForUserId(int $userId): bool
    {
        return $this->watermarkService->shouldApplyForUserId($userId);
    }

    public function shouldWatermarkForUser(?User $user): bool
    {
        return $user?->id ? $this->watermarkService->shouldApplyForUserId((int) $user->id) : false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveLayout(CoverLetter $coverLetter, ?string $templateKey): array
    {
        if ($templateKey) {
            $resolved = $this->templateRegistry->findActiveBySlug($templateKey)
                ?? $this->templateRegistry->resolveModel($coverLetter->template_id);

            if ($resolved) {
                return CoverLetterTemplateLayout::normalize($resolved->layout_json);
            }
        }

        $template = $coverLetter->relationLoaded('template')
            ? $coverLetter->template
            : ($coverLetter->template_id ? CvTemplate::find($coverLetter->template_id) : null);

        if ($template instanceof CvTemplate) {
            return CoverLetterTemplateLayout::normalize($template->layout_json);
        }

        return CoverLetterTemplateLayout::defaults();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, string>
     */
    protected function resolveContent(CoverLetter $coverLetter, array $overrides): array
    {
        $fields = $coverLetter->contentFields();
        $resolved = [];

        foreach ($fields as $key => $value) {
            $resolved[$key] = array_key_exists($key, $overrides)
                ? (string) $overrides[$key]
                : (string) ($value ?? '');
        }

        return CoverLetterRichTextCleaner::cleanFields($resolved);
    }

    /**
     * @return array<string, string>
     */
    protected function emptySender(): array
    {
        return [
            'full_name' => '',
            'job_title' => '',
            'email' => '',
            'phone' => '',
            'location' => '',
            'website' => '',
            'linkedin' => '',
        ];
    }
}
