<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\CvTemplate;
use App\Services\Cv\CvDocumentNormalizer;
use App\Services\Cv\Templates\CvTemplateRenderer;
use App\Support\Cv\CvSampleDocument;
use Illuminate\Http\Response;

class CvTemplatePreviewController extends Controller
{
    public function __construct(
        protected CvTemplateRenderer $renderer,
        protected CvDocumentNormalizer $normalizer,
    ) {}

    /**
     * Render an accurate mini-preview of a CV template using dummy data.
     */
    public function show(CvTemplate $cvTemplate): Response
    {
        abort_unless($cvTemplate->isCv(), 404);

        $document = $this->normalizer->normalize(CvSampleDocument::data());

        $html = $this->renderer->render($document, $cvTemplate->slug, [
            'mode' => 'preview',
            'user' => null,
            'template_id' => $cvTemplate->id,
        ]);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
