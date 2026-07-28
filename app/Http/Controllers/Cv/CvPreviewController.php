<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Services\Cv\CvBuilderService;
use App\Services\Pdf\PdfWatermarkService;
use Illuminate\View\View;

class CvPreviewController extends Controller
{
    public function __construct(
        protected CvBuilderService $cvBuilderService,
        protected PdfWatermarkService $watermarkService,
    ) {}

    public function show(Cv $cv): View
    {
        $this->authorize('view', $cv);

        $builder = $this->cvBuilderService->getBuilderData($cv);
        $user = auth()->user() ?? $cv->user;
        $showWatermark = $this->watermarkService->shouldApply($user);

        return view('web.cv.preview.index', [
            'cv' => $cv,
            'previewPayload' => array_merge($builder->toArray(), [
                'show_watermark' => $showWatermark,
                'watermark' => [
                    'logo_url' => url(config('cv-templates.watermark.logo', 'images/WATERMARK-LOGO.png')),
                    'opacity' => (float) config('cv-templates.watermark.logo_opacity', 0.20),
                    'angle' => (float) config('cv-templates.watermark.angle', -30),
                    'width_px' => (int) config('cv-templates.watermark.logo_display_width_px', 900),
                ],
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
