<?php

namespace App\Http\Controllers\CoverLetter;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoverLetter\StoreCoverLetterFromCvRequest;
use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Services\CoverLetter\CoverLetterRenderer;
use App\Services\CoverLetter\CoverLetterService;
use App\Services\CoverLetter\CoverLetterTemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CoverLetterController extends Controller
{
    public function __construct(
        protected CoverLetterService $coverLetterService,
        protected CoverLetterTemplateRegistry $templateRegistry,
        protected CoverLetterRenderer $renderer,
    ) {}

    /**
     * Step: choose a cover letter template for the selected CV.
     */
    public function create(Cv $cv): View
    {
        $this->authorize('view', $cv);

        return view('web.cover-letter.templates.index', [
            'cv' => $cv,
            'templates' => $this->templateRegistry->all(),
        ]);
    }

    /**
     * Render an accurate mini-preview of a cover letter template using dummy data.
     */
    public function preview(CvTemplate $cvTemplate): Response
    {
        abort_unless($cvTemplate->isCoverLetter(), 404);

        return response($this->renderer->renderSample($cvTemplate), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function store(StoreCoverLetterFromCvRequest $request, Cv $cv): RedirectResponse
    {
        $this->authorize('view', $cv);

        $coverLetter = $this->coverLetterService->createForCv(
            $request->user(),
            $cv,
            $request->coverLetterTemplate(),
            $request->validated(),
        );

        return redirect()
            ->route('cover-letters.edit', $coverLetter)
            ->with('status', 'cover-letter-created');
    }

    public function duplicate(CoverLetter $coverLetter): RedirectResponse
    {
        $this->authorize('update', $coverLetter);

        $copy = $this->coverLetterService->duplicate($coverLetter);

        return redirect()
            ->route('cover-letters.edit', $copy)
            ->with('status', 'cover-letter-duplicated');
    }

    public function destroy(CoverLetter $coverLetter): RedirectResponse
    {
        $this->authorize('delete', $coverLetter);

        $coverLetter->delete();

        return redirect()
            ->route('dashboard.cover-letters')
            ->with('status', 'cover-letter-deleted');
    }
}
