<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cv\ValidateLinkedinPdfImportRequest;
use App\Services\Cv\LinkedInPdfImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LinkedinPdfImportController extends Controller
{
    public function __construct(
        protected LinkedInPdfImportService $linkedInPdfImportService,
    ) {}

    public function store(ValidateLinkedinPdfImportRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $temporaryPath = $this->linkedInPdfImportService->queueImport(
            $request->user(),
            $validated['linkedin_pdf'],
        );

        \Illuminate\Support\Facades\Log::info('LinkedIn PDF import queued', [
            'user_id' => $request->user()->id,
            'uploaded_pdf_path' => $temporaryPath,
        ]);

        return back()->with([
            'status' => 'linkedin-pdf-import-queued',
            'linkedin_pdf_import_state' => 'processing',
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'import' => $this->linkedInPdfImportService->statusForClient($request->user()),
        ]);
    }

    public function consume(Request $request): JsonResponse
    {
        $this->linkedInPdfImportService->forgetStatus($request->user());

        return response()->json(['ok' => true]);
    }

    public function continueToTemplates(Request $request): JsonResponse
    {
        try {
            $cv = $this->linkedInPdfImportService->continueToTemplateSelection($request->user());
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $request->session()->flash('status', 'linkedin-import-ready');

        return response()->json([
            'redirect' => route('cvs.templates', $cv),
        ]);
    }
}
