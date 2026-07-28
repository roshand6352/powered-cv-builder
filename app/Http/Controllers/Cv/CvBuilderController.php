<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Enums\CvStatus;
use App\Http\Requests\Cv\AutosaveCvBuilderRequest;
use App\Http\Requests\Cv\SaveCvBuilderRequest;
use App\Models\Cv;
use App\Services\Cv\CvBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CvBuilderController extends Controller
{
    public function __construct(
        protected CvBuilderService $cvBuilderService,
    ) {}

    public function edit(Cv $cv): View|RedirectResponse
    {
        $this->authorize('update', $cv);

        if ($cv->template_id === null) {
            return redirect()->route('cvs.templates', $cv);
        }

        return view('web.cv.builder.index', [
            'cv' => $cv,
            'builder' => $this->cvBuilderService->getBuilderData($cv),
        ]);
    }

    public function update(SaveCvBuilderRequest $request, Cv $cv): JsonResponse
    {
        $publish = $request->boolean('publish');

        $cv = $this->cvBuilderService->save(
            $cv,
            $request->validated(),
            $publish,
        );

        return response()->json([
            'message' => $publish ? 'CV saved successfully.' : 'CV saved as draft.',
            'cv' => $this->cvResponsePayload($cv),
        ]);
    }

    public function autosave(AutosaveCvBuilderRequest $request, Cv $cv): JsonResponse
    {
        $cv = $this->cvBuilderService->autosave($cv, $request->validated());

        return response()->json([
            'message' => 'Draft autosaved.',
            'cv' => $this->cvResponsePayload($cv),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function cvResponsePayload(Cv $cv): array
    {
        return [
            'id' => $cv->id,
            'title' => $cv->title,
            'slug' => (string) $cv->id,
            'status' => CvStatus::toValue($cv->status),
            'updated_at' => $cv->updated_at?->toIso8601String(),
            // LIVE PREVIEW FIX: keep builder URLs in sync when slug changes after autosave
            'urls' => [
                'autosave' => route('cvs.builder.autosave', $cv),
                'update' => route('cvs.builder.update', $cv),
                'preview' => route('cvs.render.live', $cv),
                'pdfDownload' => route('pdf-exports.cv.download', $cv),
            ],
        ];
    }
}
