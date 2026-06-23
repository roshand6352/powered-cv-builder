<?php

namespace App\Services\Ai;

use App\Contracts\Ai\AiClientInterface;
use App\DTOs\Ai\AiCompletionResult;
use App\Exceptions\Ai\AiGenerationException;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiProviderManager extends BaseService implements AiClientInterface
{
    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function chat(array $messages, array $options = []): AiCompletionResult
    {
        $provider = $this->resolveProvider();

        Log::info('AI completion request dispatched.', [
            'provider' => $provider,
        ]);

        try {
            $client = $this->resolveClient($provider);

            return $client->chat($messages, $options);
        } catch (AiGenerationException $exception) {
            Log::error('AI completion failed.', [
                'provider' => $provider,
                'message' => $exception->getMessage(),
                'status_code' => $exception->statusCode,
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('AI completion failed with unexpected error.', [
                'provider' => $provider,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function activeProvider(): string
    {
        return $this->resolveProvider();
    }

    protected function resolveProvider(): string
    {
        return strtolower(trim((string) config('ai.provider', 'openai')));
    }

    protected function resolveClient(string $provider): AiClientInterface
    {
        /** @var array<string, class-string<AiClientInterface>> $providers */
        $providers = config('ai.providers', []);

        $clientClass = $providers[$provider] ?? null;

        if ($clientClass === null || ! is_subclass_of($clientClass, AiClientInterface::class)) {
            $supported = implode(', ', array_keys($providers));

            throw new AiGenerationException(
                "Unsupported AI provider [{$provider}]. Supported values: {$supported}.",
            );
        }

        return app($clientClass);
    }
}
