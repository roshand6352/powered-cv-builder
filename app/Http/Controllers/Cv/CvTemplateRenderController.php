<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cv\RenderCvPreviewRequest;
use App\Http\Requests\Cv\SwitchCvTemplateRequest;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Services\Cv\CvDocumentNormalizer;
use App\Services\Cv\CvDocumentUpdateService;
use App\Services\Cv\CvTemplateApplicationService;
use App\Services\Cv\Templates\CvTemplateRenderer;
use App\Services\Cv\Templates\TemplateRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CvTemplateRenderController extends Controller
{
    public function __construct(
        protected CvTemplateRenderer $templateRenderer,
        protected CvDocumentNormalizer $normalizer,
        protected TemplateRegistry $templateRegistry,
        protected CvDocumentUpdateService $updateService,
        protected CvTemplateApplicationService $templateApplicationService,
    ) {}

    public function show(Request $request, Cv $cv, ?string $template = null): Response
    {
        $this->authorize('view', $cv);

        $document = $this->normalizer->normalize($cv->cv_data ?? $cv->json_data ?? []);
        $html = $this->templateRenderer->render($document, $template, [
            'mode' => $request->query('mode', 'preview'),
            'user' => $request->user(),
            'template_id' => $cv->template_id,
            'watermark' => $this->templateRenderer->shouldWatermark($request->user()),
        ]);

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function live(RenderCvPreviewRequest $request, Cv $cv): Response
    {
        $document = $this->normalizer->normalize($request->validated('json_data'));
        $templateKey = $request->validated('template') ?? $document->template->key ?? $document->template->slug;

        $applied = $this->templateApplicationService->applyToDocument($document, (string) $templateKey);

        $html = $this->templateRenderer->render($applied['document'], $applied['template_key'], [
            'mode' => 'preview',
            'user' => $request->user(),
            'template_id' => $applied['document']->template->id
                ?? $applied['definition']['id']
                ?? $cv->template_id,
            'watermark' => $this->templateRenderer->shouldWatermark($request->user()),
        ]);

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function switchTemplate(SwitchCvTemplateRequest $request, Cv $cv): JsonResponse
    {
        $validated = $request->validated();
        $templateKey = $validated['template'];

        $document = isset($validated['json_data'])
            ? $this->normalizer->normalize($validated['json_data'])
            : $this->normalizer->normalize($cv->cv_data ?? $cv->json_data ?? []);

        $applied = $this->templateApplicationService->applyToDocument($document, $templateKey, mergeSettings: true);
        $dbTemplate = CvTemplate::query()->where('slug', $templateKey)->first();

        $cv = $this->updateService->update($cv, [
            'title' => $cv->title,
            'json_data' => $applied['document']->toArray(),
            'template_id' => $dbTemplate?->id ?? $cv->template_id,
        ]);

        $html = $this->templateRenderer->render($applied['document'], $applied['template_key'], [
            'mode' => 'preview',
            'user' => $request->user(),
            'template_id' => $dbTemplate?->id ?? $cv->template_id,
            'watermark' => $this->templateRenderer->shouldWatermark($request->user()),
        ]);

        return response()->json([
            'message' => 'Template switched.',
            'template' => $templateKey,
            'html' => $html,
            'cv' => [
                'id' => $cv->id,
                'template_id' => $cv->template_id,
            ],
        ]);
    }

    public function catalog(): JsonResponse
    {
        return response()->json([
            'templates' => $this->templateRegistry->all(),
        ]);
    }
}
