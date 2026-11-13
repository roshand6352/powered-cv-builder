<?php

namespace App\Services\Pdf;

use App\Models\Cv;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Cv\CvDocumentNormalizer;
use App\Services\Cv\CvTemplateApplicationService;
use Illuminate\Support\Str;

class CvPdfExportService extends BaseService
{
    public function __construct(
        protected CvPdfGenerator $cvPdfGenerator,
        protected PdfGenerationService $pdfGenerationService,
        protected CvDocumentNormalizer $normalizer,
        protected CvTemplateApplicationService $templateApplicationService,
    ) {}

    /**
     * Generate a CV PDF using the same WYSIWYG pipeline as the builder download.
     *
     * @param  array<string, mixed>  $validated  Expects json_data; optional template and preview_html
     */
    public function generate(Cv $cv, User $user, array $validated): string
    {
        $document = $this->normalizer->normalize($validated['json_data']);
        $templateKey = $validated['template']
            ?? $document->template->key
            ?? $document->template->slug;

        $user->loadMissing('plan');
        $applyWatermark = $this->cvPdfGenerator->shouldWatermarkForUserId((int) $user->id);

        $previewHtml = trim((string) ($validated['preview_html'] ?? ''));

        $jsonData = $validated['json_data'];

        if ($previewHtml === '' && is_string($templateKey) && $templateKey !== '') {
            ['document' => $document] = $this->templateApplicationService->applyToDocument($document, $templateKey);
            $jsonData = $document->toArray();
        }

        $html = $previewHtml !== ''
            ? $previewHtml
            : $this->cvPdfGenerator->renderHtmlFromData(
                $jsonData,
                $templateKey,
                $user,
                $document->template->id ?? $cv->template_id,
                $applyWatermark,
            );

        $html = $this->cvPdfGenerator->prepareLivePreviewHtml($html, $applyWatermark);

        return $this->pdfGenerationService->generate(
            $html,
            $this->cvPdfGenerator->buildPreviewMatchedOptions(
                $cv,
                $templateKey,
                $user,
                $applyWatermark,
                fromLivePreview: $previewHtml !== '',
            ),
        );
    }

    /**
     * Download filename such as "Shane-Louw-CV.pdf" (ASCII-safe) from the candidate's
     * full name in the submitted document, otherwise the saved CV. Falls back to "My-CV.pdf".
     * Mirrors buildCvPdfFilename() in resources/js/cv/pdf-download.js.
     *
     * @param  array<string, mixed>|string|null  $document
     */
    public function filename(Cv $cv, array|string|null $document = null): string
    {
        if (is_string($document)) {
            $document = json_decode($document, true);
        }

        $fullName = data_get(is_array($document) ? $document : [], 'personal_details.full_name')
            ?: data_get($cv->cv_data ?? [], 'personal_details.full_name');

        $base = $this->filenameSegment(is_string($fullName) ? $fullName : '');

        return $base !== '' ? $base.'-CV.pdf' : 'My-CV.pdf';
    }

    protected function filenameSegment(string $value): string
    {
        $ascii = Str::ascii($value);
        $words = preg_split('/[^A-Za-z0-9]+/', $ascii, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $words = array_filter($words, fn (string $word) => strcasecmp($word, 'cv') !== 0);

        return rtrim(Str::limit(implode('-', array_map(fn (string $word) => ucfirst($word), $words)), 80, ''), '-');
    }
}
