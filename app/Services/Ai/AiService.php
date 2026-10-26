<?php

namespace App\Services\Ai;

use App\Contracts\Ai\AiClientInterface;
use App\Enums\AiFeature;
use App\Jobs\ProcessAiRequestJob;
use App\Models\AiLog;
use App\Models\User;
use App\Services\BaseService;
use App\Support\Cv\CvDocumentMigrator;
use App\Support\Cv\CvDocumentValidationRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class AiService extends BaseService
{
    public function __construct(
        protected AiClientInterface $aiClient,
        protected AiPromptBuilder $promptBuilder,
        protected AiUsageService $usageService,
        protected AiLogService $logService,
        protected AiResponseParser $responseParser,
    ) {}

    public function generateSummary(User $user, array $payload): AiLog
    {
        return $this->dispatch(AiFeature::GenerateSummary, $user, $payload);
    }

    public function improveExperience(User $user, array $payload): AiLog
    {
        return $this->dispatch(AiFeature::ImproveExperience, $user, $payload);
    }

    public function improveSkills(User $user, array $payload): AiLog
    {
        return $this->dispatch(AiFeature::ImproveSkills, $user, $payload);
    }

    public function grammarFix(User $user, array $payload): AiLog
    {
        return $this->dispatch(AiFeature::GrammarFix, $user, $payload);
    }

    public function generateCoverLetter(User $user, array $payload): AiLog
    {
        return $this->dispatch(AiFeature::CoverLetterGeneration, $user, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function analyzeJobMatch(array $payload): array
    {
        $messages = $this->promptBuilder->buildJobMatch($payload);
        $completion = $this->aiClient->chat($messages, [
            'response_format' => ['type' => 'json_object'],
        ]);

        $decoded = $this->responseParser->decodeJsonContent($completion->content);

        if ($decoded === null) {
            throw new \RuntimeException('AI returned invalid JSON.');
        }

        return [
            'score' => isset($decoded['score']) ? (int) $decoded['score'] : 0,
            'matched_keywords' => array_values(array_map('strval', $decoded['matched_keywords'] ?? [])),
            'missing_keywords' => array_values(array_map('strval', $decoded['missing_keywords'] ?? [])),
            'strengths' => array_values(array_map('strval', $decoded['strengths'] ?? [])),
            'improvements' => array_values(array_map('strval', $decoded['improvements'] ?? [])),
            'summary' => (string) ($decoded['summary'] ?? ''),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapLinkedinPdfImport(array $payload): array
    {
        $messages = $this->promptBuilder->buildLinkedinPdfImport($payload);
        $completion = $this->aiClient->chat($messages, [
            'response_format' => ['type' => 'json_object'],
        ]);

        $decoded = $this->responseParser->decodeJsonContent($completion->content);

        if ($decoded === null) {
            throw new \RuntimeException('AI returned invalid JSON.');
        }

        $document = $decoded['json_data'] ?? $decoded;

        if (! is_array($document)) {
            throw new \RuntimeException('AI returned an invalid CV document.');
        }

        $document = CvDocumentMigrator::migrate($document);

        $validator = Validator::make(
            ['json_data' => $document],
            CvDocumentValidationRules::rules(),
        );

        if ($validator->fails()) {
            throw new \RuntimeException('AI returned CV JSON that does not match the builder schema.');
        }

        return $document;
    }

    public function process(AiLog $log): AiLog
    {
        $log->loadMissing('user.plan');
        $feature = AiFeature::from($log->feature);
        $this->usageService->ensureCredits($log->user, $feature->creditCost());

        $this->logService->markProcessing($log);

        try {
            $messages = $this->promptBuilder->build($feature, $log->request ?? []);
            $options = $this->optionsForFeature($feature);

            $completion = $this->aiClient->chat($messages, $options);
            [$result, $responsePayload] = $this->parseFeatureResult($feature, $completion->content);

            $this->usageService->consume($log->user, $feature->creditCost());

            return $this->logService->markCompleted(
                log: $log,
                response: $responsePayload,
                tokensUsed: $completion->totalTokens,
                result: is_string($result) ? $result : null,
                model: $completion->model,
            );
        } catch (Throwable $exception) {
            $this->logService->markFailed($log, Str::limit($exception->getMessage(), 1000));

            throw $exception;
        }
    }

    protected function dispatch(AiFeature $feature, User $user, array $payload): AiLog
    {
        $this->usageService->ensureCredits($user, $feature->creditCost());

        $log = $this->logService->createPending(
            user: $user,
            feature: $feature,
            request: $payload,
            creditsUsed: $feature->creditCost(),
        );

        if ($feature->isQueued()) {
            ProcessAiRequestJob::dispatch($log)
                ->onQueue(config('ai.queue', 'default'));

            return $log;
        }

        return $this->process($log);
    }

    /**
     * @return array{0: string|array<string, mixed>, 1: array<string, mixed>}
     */
    protected function parseFeatureResult(AiFeature $feature, string $content): array
    {
        return match ($feature) {
            AiFeature::ImproveSkills => $this->parseJsonFeature($content, 'skills'),
            AiFeature::CoverLetterGeneration => $this->parseJsonFeature($content, 'cover_letter'),
            default => [$content, ['text' => $content]],
        };
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    protected function parseJsonFeature(string $content, string $key): array
    {
        $decoded = $this->responseParser->decodeJsonContent($content);

        if ($decoded === null) {
            throw new \RuntimeException('AI returned invalid JSON.');
        }

        return [$decoded, [$key => $decoded]];
    }

    /**
     * @return array<string, mixed>
     */
    protected function optionsForFeature(AiFeature $feature): array
    {
        if (in_array($feature, [AiFeature::ImproveSkills, AiFeature::CoverLetterGeneration], true)) {
            return [
                'response_format' => ['type' => 'json_object'],
            ];
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function logPayload(AiLog $log): array
    {
        return [
            'id' => $log->id,
            'feature' => $log->feature,
            'feature_label' => AiFeature::from($log->feature)->label(),
            'status' => $log->status->value,
            'status_label' => $log->status->label(),
            'result' => $log->result,
            'response' => $log->response,
            'error_message' => $log->error_message,
            'tokens_used' => $log->tokens_used,
            'credits_used' => $log->credits_used,
            'completed_at' => $log->completed_at?->toIso8601String(),
            'created_at' => $log->created_at?->toIso8601String(),
        ];
    }
}
