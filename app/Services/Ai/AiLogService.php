<?php

namespace App\Services\Ai;

use App\Enums\AiFeature;
use App\Enums\AiLogStatus;
use App\Models\AiLog;
use App\Models\User;
use App\Services\BaseService;

class AiLogService extends BaseService
{
    public function createPending(User $user, AiFeature $feature, array $request, int $creditsUsed = 1): AiLog
    {
        return AiLog::query()->create([
            'user_id' => $user->id,
            'feature' => $feature->value,
            'status' => AiLogStatus::Pending,
            'credits_used' => $creditsUsed,
            'request' => $request,
        ]);
    }

    public function markProcessing(AiLog $log): AiLog
    {
        $log->update(['status' => AiLogStatus::Processing]);

        return $log->fresh();
    }

    public function markCompleted(
        AiLog $log,
        array $response,
        int $tokensUsed,
        ?string $result = null,
        ?string $model = null,
    ): AiLog {
        $log->update([
            'status' => AiLogStatus::Completed,
            'response' => $response,
            'result' => $result,
            'tokens_used' => $tokensUsed,
            'model' => $model,
            'error_message' => null,
            'completed_at' => now(),
        ]);

        return $log->fresh();
    }

    public function markFailed(AiLog $log, string $errorMessage): AiLog
    {
        $log->update([
            'status' => AiLogStatus::Failed,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);

        return $log->fresh();
    }
}
