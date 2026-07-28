<?php

namespace App\Http\Controllers\CoverLetter;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoverLetter\RenderCoverLetterRequest;
use App\Http\Requests\CoverLetter\SaveCoverLetterRequest;
use App\Models\CoverLetter;
use App\Services\CoverLetter\CoverLetterRenderer;
use App\Services\CoverLetter\CoverLetterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CoverLetterBuilderController extends Controller
{
    public function __construct(
        protected CoverLetterService $coverLetterService,
        protected CoverLetterRenderer $renderer,
    ) {}

    public function edit(CoverLetter $coverLetter): View
    {
        $this->authorize('update', $coverLetter);

        return view('web.cover-letter.builder.index', [
            'coverLetter' => $coverLetter,
            'builder' => $this->coverLetterService->builderPayload($coverLetter),
        ]);
    }

    public function update(SaveCoverLetterRequest $request, CoverLetter $coverLetter): JsonResponse
    {
        $this->authorize('update', $coverLetter);

        $coverLetter = $this->coverLetterService->update($coverLetter, $request->validated());

        return response()->json([
            'message' => 'Cover letter saved.',
            'coverLetter' => $this->payload($coverLetter),
        ]);
    }

    public function autosave(SaveCoverLetterRequest $request, CoverLetter $coverLetter): JsonResponse
    {
        $this->authorize('update', $coverLetter);

        $data = $request->validated();
        $data['status'] = 'draft';

        $coverLetter = $this->coverLetterService->update($coverLetter, $data);

        return response()->json([
            'message' => 'Draft autosaved.',
            'coverLetter' => $this->payload($coverLetter),
        ]);
    }

    public function render(RenderCoverLetterRequest $request, CoverLetter $coverLetter): Response
    {
        $this->authorize('update', $coverLetter);

        $coverLetter->loadMissing(['cv', 'template']);

        $html = $this->renderer->render($coverLetter, $request->overrides(), [
            'template' => $request->input('template'),
            'watermark' => $this->renderer->shouldWatermarkForUser($request->user()),
        ]);

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * @return array<string, mixed>
     */
    protected function payload(CoverLetter $coverLetter): array
    {
        return [
            'id' => $coverLetter->id,
            'slug' => $coverLetter->slug,
            'title' => $coverLetter->title,
            'status' => $coverLetter->status,
            'updated_at' => $coverLetter->updated_at?->toIso8601String(),
        ];
    }
}
