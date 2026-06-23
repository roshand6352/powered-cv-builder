<?php

namespace App\Http\Controllers\Ai;

use App\Exceptions\Ai\AiGenerationException;
use App\Exceptions\Ai\AiLimitExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ai\GenerateCoverLetterRequest;
use App\Http\Requests\Ai\GenerateSummaryRequest;
use App\Http\Requests\Ai\GrammarFixRequest;
use App\Http\Requests\Ai\ImproveExperienceRequest;
use App\Http\Requests\Ai\ImproveSkillsRequest;
use App\Models\AiLog;
use App\Models\User;
use App\Services\Ai\AiService;
use App\Services\Ai\AiUsageService;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Throwable;

class AiController extends Controller
{
    public function __construct(
        protected AiService $aiService,
        protected AiUsageService $usageService,
        protected SubscriptionService $subscriptionService,
    ) {}

    public function usage(): JsonResponse
    {
        $user = request()->user();
        $plan = $this->subscriptionService->resolveActivePlan($user);

        return response()->json([
            'usage' => $this->usageService->snapshot($user),
            'has_ai_access' => ! $plan->isFree(),
        ]);
    }

    public function show(AiLog $aiLog): JsonResponse
    {
        $this->authorize('view', $aiLog);

        return response()->json([
            'log' => $this->aiService->logPayload($aiLog),
            'usage' => $this->usageService->snapshot(request()->user()),
        ]);
    }

    public function generateSummary(GenerateSummaryRequest $request): JsonResponse
    {
        return $this->handle($request, fn () => $this->aiService->generateSummary(
            $request->user(),
            $request->validated(),
        ));
    }

    public function improveExperience(ImproveExperienceRequest $request): JsonResponse
    {
        return $this->handle($request, fn () => $this->aiService->improveExperience(
            $request->user(),
            $request->validated(),
        ));
    }

    public function improveSkills(ImproveSkillsRequest $request): JsonResponse
    {
        return $this->handle($request, fn () => $this->aiService->improveSkills(
            $request->user(),
            $request->validated(),
        ));
    }

    public function grammarFix(GrammarFixRequest $request): JsonResponse
    {
        return $this->handle($request, fn () => $this->aiService->grammarFix(
            $request->user(),
            $request->validated(),
        ));
    }

    public function generateCoverLetter(GenerateCoverLetterRequest $request): JsonResponse
    {
        return $this->handle($request, fn () => $this->aiService->generateCoverLetter(
            $request->user(),
            $request->validated(),
        ), queued: true);
    }

    protected function handle(mixed $request, callable $callback, bool $queued = false): JsonResponse
    {
        try {
            $this->ensureAiAccess($request->user());

            $log = $callback();

            return response()->json([
                'message' => $queued ? 'AI request queued.' : 'AI request completed.',
                'log' => $this->aiService->logPayload($log),
                'usage' => $this->usageService->snapshot($request->user()),
            ], $queued ? 202 : 200);
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
                'error' => config('app.debug') ? $exception->context : null,
            ], $exception->statusCode ?? 502);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'AI request failed.',
                'error' => config('app.debug') ? $exception->getMessage() : null,
            ], 500);
        }
    }

    protected function ensureAiAccess(User $user): void
    {
        $plan = $this->subscriptionService->resolveActivePlan($user);

        if ($plan->isFree()) {
            throw new AiGenerationException(
                'AI writing assistance is available on Pro plans. Upgrade to unlock Improve.',
                403,
            );
        }
    }
}
