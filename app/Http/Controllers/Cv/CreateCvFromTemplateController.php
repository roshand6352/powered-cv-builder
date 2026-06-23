<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\CvTemplate;
use App\Services\Cv\CvService;
use Illuminate\Http\RedirectResponse;

class CreateCvFromTemplateController extends Controller
{
    public function __construct(
        protected CvService $cvService,
    ) {}

    public function __invoke(CvTemplate $cvTemplate): RedirectResponse
    {
        abort_unless($cvTemplate->status === 1, 404);

        $cv = $this->cvService->createFromTemplate(
            auth()->user(),
            $cvTemplate,
        );

        return redirect()
            ->route('cvs.builder', $cv)
            ->with('status', 'template-selected');
    }
}
