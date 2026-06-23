<?php

namespace App\Jobs;

use App\Models\AiLog;
use App\Services\Ai\AiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessAiRequestJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public AiLog $aiLog,
    ) {}

    public function handle(AiService $aiService): void
    {
        $aiService->process($this->aiLog);
    }

    public function failed(Throwable $exception): void
    {
        if ($this->aiLog->status->isTerminal()) {
            return;
        }

        app(\App\Services\Ai\AiLogService::class)->markFailed(
            $this->aiLog,
            $exception->getMessage(),
        );
    }
}
