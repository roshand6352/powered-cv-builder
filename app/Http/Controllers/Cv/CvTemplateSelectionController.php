<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cv\SelectCvTemplateRequest;
use App\Models\Cv;
use App\Services\Cv\CvService;
use App\Services\Cv\Templates\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CvTemplateSelectionController extends Controller
{
    public function __construct(
        protected CvService $cvService,
        protected TemplateRegistry $templateRegistry,
    ) {}

    public function index(Cv $cv): View
    {
        $this->authorize('update', $cv);

        return view('web.cv.templates.index', [
            'cv' => $cv,
            'templates' => $this->templateRegistry->allForBuilder(),
            'selectedTemplateId' => $cv->template_id,
        ]);
    }

    public function store(SelectCvTemplateRequest $request, Cv $cv): RedirectResponse
    {
        $this->cvService->assignTemplate($cv, $request->cvTemplate());

        return redirect()
            ->route('cvs.builder', $cv)
            ->with('status', 'template-selected');
    }
}
