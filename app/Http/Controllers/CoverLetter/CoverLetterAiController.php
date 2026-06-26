<?php

namespace App\Http\Controllers\CoverLetter;

use App\Enums\AiLogStatus;
use App\Exceptions\Ai\AiGenerationException;
use App\Exceptions\Ai\AiLimitExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CoverLetter\GenerateCoverLetterContentRequest;
use App\Models\CoverLetter;
use App\Services\Ai\AiService;
use App\Services\Ai\AiUsageService;
use App\Services\CoverLetter\CoverLetterCvExtractor;
use Illuminate\Http\JsonResponse;
use Throwable;

class CoverLetterAiController extends Controller
{
    public function __construct(
        protected AiService $aiService,
        protected AiUsageService $usageService,
        protected CoverLetterCvExtractor $cvExtractor,
    ) {}

    /**
     * Generate cover letter content from the linked CV only.
     */
    public function generate(GenerateCoverLetterContentRequest $request, CoverLetter $coverLetter): JsonResponse
    {
        $this->authorize('update', $coverLetter);

        $cv = $coverLetter->cv;

        if (! $cv) {
            return response()->json([
                'message' => 'This cover letter is not linked to a CV.',
            ], 422);
        }

        $validated = $request->validated();

        $payload = array_merge(
            $this->cvExtractor->toAiCandidatePayload($cv),
            [
                'company' => $validated['company_name'],
                'job_title' => $validated['job_title'],
                'job_description' => $validated['job_description'] ?? '',
                'tone' => $validated['tone'] ?? 'professional',
            ],
        );

        try {
            $log = $this->aiService->generateCoverLetter($request->user(), $payload);

            // Cover letter builder expects an immediate result — process queued jobs inline.
            if ($log->status === AiLogStatus::Pending) {
                $log = $this->aiService->process($log->fresh());
            }

            $statusCode = $log->status === AiLogStatus::Completed ? 200 : 202;

            return response()->json([
                'message' => $log->status === AiLogStatus::Completed
                    ? 'Cover letter generated.'
                    : 'AI request queued.',
                'log' => $this->aiService->logPayload($log->fresh()),
                'usage' => $this->usageService->snapshot($request->user()),
            ], $statusCode);
        } catch (AiLimitExceededException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'usage' => [
                    'used' => $exception->used,
                    'limit' => $exception->limit,
                    'remaining' => 0,
                ],
            ], 429);
        } catch (AiGenerationException $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 502);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'AI request failed.',
                'error' => config('app.debug') ? $exception->getMessage() : null,
            ], 500);
        }
    }
}
