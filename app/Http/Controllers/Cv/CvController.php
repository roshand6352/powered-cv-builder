<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cv\StoreCvRequest;
use App\Services\Cv\CvService;
use Illuminate\Http\RedirectResponse;

class CvController extends Controller
{
    public function __construct(
        protected CvService $cvService,
    ) {}

    public function store(StoreCvRequest $request): RedirectResponse
    {
        $cv = $this->cvService->createDraft(
            $request->user(),
            $request->validated('title'),
        );

        return redirect()
            ->route('cvs.templates', $cv)
            ->with('status', 'cv-created');
    }
}
