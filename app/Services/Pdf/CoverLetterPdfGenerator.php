<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Models\CoverLetter;
use App\Models\User;
use App\Services\BaseService;
use App\Services\CoverLetter\CoverLetterRenderer;
use App\Support\FontAwesome\FontAwesomeAssets;

class CoverLetterPdfGenerator extends BaseService
{
    public function __construct(
        protected CoverLetterRenderer $renderer,
        protected PdfWatermarkService $watermarkService,
    ) {}

    public function shouldWatermarkForUserId(int $userId): bool
    {
        return $this->watermarkService->shouldApplyForUserId($userId);
    }

    public function renderHtml(CoverLetter $coverLetter, ?User $user = null, ?bool $applyWatermark = null): string
    {
        $user ??= $coverLetter->user;

        if ($applyWatermark === null) {
            $applyWatermark = $this->renderer->shouldWatermarkForUser($user);
        }

        return FontAwesomeAssets::ensureInHtml(
            $this->renderer->render($coverLetter, [], [
                'watermark' => $applyWatermark,
            ]),
            absolute: true,
        );
    }

    public function buildOptions(CoverLetter $coverLetter, ?User $user = null, ?bool $applyWatermark = null): PdfGenerationOptions
    {
        $user ??= $coverLetter->user;

        if ($applyWatermark === null) {
            $applyWatermark = $this->renderer->shouldWatermarkForUser($user);
        }

        $meta = [
            'title' => $coverLetter->title,
            'type' => 'cover_letter',
        ];

        return new PdfGenerationOptions(
            watermark: $applyWatermark,
            headerHtml: null,
            footerHtml: null,
            title: $coverLetter->title,
            meta: $meta,
            margins: ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
            emulatePrint: true,
            fromLivePreview: true,
            preferBrowsershot: true,
        );
    }
}
