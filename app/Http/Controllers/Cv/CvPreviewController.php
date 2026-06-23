<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Services\Cv\CvBuilderService;
use Illuminate\View\View;

class CvPreviewController extends Controller
{
    public function __construct(
        protected CvBuilderService $cvBuilderService,
    ) {}

    public function show(Cv $cv): View
    {
        $this->authorize('view', $cv);

        $builder = $this->cvBuilderService->getBuilderData($cv);

        return view('web.cv.preview.index', [
            'cv' => $cv,
            'previewPayload' => array_merge($builder->toArray(), [
                'urls' => [
                    'builder' => route('cvs.builder', $cv),
                    'render' => route('cvs.render.show', $cv),
                    'pdfDownload' => route('pdf-exports.cv.download', $cv),
                ],
                'poll_interval_ms' => 5000,
            ]),
        ]);
    }
}
