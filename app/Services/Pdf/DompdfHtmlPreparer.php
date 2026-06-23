<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Services\BaseService;

/**
 * DomPDF HTML preparation — separate from the Browsershot pipeline.
 *
 * Uses shared asset inlining (fonts, images, sidebar tables) then applies
 * DompdfHtmlNormalizer for engine-specific CSS/markup fixes.
 */
class DompdfHtmlPreparer extends BaseService
{
    public function __construct(
        protected PdfHtmlPreparer $sharedPreparer,
        protected DompdfHtmlNormalizer $normalizer,
    ) {}

    public function prepare(string $html, ?PdfGenerationOptions $options = null): string
    {
        $html = $this->sharedPreparer->prepare(
            $html,
            $options,
            PdfEngineResolver::ENGINE_DOMPDF,
        );

        return $this->normalizer->normalize($html);
    }
}
