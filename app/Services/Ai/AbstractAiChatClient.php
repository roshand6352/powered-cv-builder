<?php

namespace App\Services\Ai;

use App\Contracts\Ai\AiClientInterface;
use App\DTOs\Ai\AiCompletionResult;
use App\Exceptions\Ai\AiGenerationException;
use App\Services\BaseService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

abstract class AbstractAiChatClient extends BaseService implements AiClientInterface
{
    abstract protected function providerKey(): string;

    abstract protected function providerLabel(): string;

    /**
     * @return array<string, string>
     */
    protected function extraHeaders(): array
    {
        return [];
    }

    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function chat(array $messages, array $options = []): AiCompletionResult
    {
        /** @var array<string, mixed> $config */
        $config = config('ai.'.$this->providerKey(), []);
        $apiKey = $config['api_key'] ?? null;

        if (blank($apiKey)) {
            throw new AiGenerationException("{$this->providerLabel()} API key is not configured.");
        }

        $model = $options['model'] ?? ($config['model'] ?? null);
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? ($config['temperature'] ?? 0.7),
            'max_tokens' => $options['max_tokens'] ?? ($config['max_tokens'] ?? 1500),
        ];

        if (isset($options['response_format'])) {
            $payload['response_format'] = $options['response_format'];
        }

        try {
            $response = Http::baseUrl(rtrim((string) ($config['base_url'] ?? ''), '/'))
                ->withToken((string) $apiKey)
                ->withHeaders($this->extraHeaders())
                ->acceptJson()
                ->timeout((int) ($config['timeout'] ?? 60))
                ->post('/chat/completions', $payload)
                ->throw();
        } catch (RequestException $exception) {
            $status = $exception->response?->status();
            $body = $exception->response?->json();
            $message = data_get($body, 'error.message')
                ?? $exception->getMessage();

            throw new AiGenerationException(
                message: "{$this->providerLabel()} request failed: {$message}",
                statusCode: $status,
                context: is_array($body) ? $body : null,
                previous: $exception,
            );
        }

        $data = $response->json();
        $content = trim((string) data_get($data, 'choices.0.message.content', ''));

        if ($content === '') {
            throw new AiGenerationException("{$this->providerLabel()} returned an empty response.");
        }

        return new AiCompletionResult(
            content: $content,
            model: (string) data_get($data, 'model', $model),
            promptTokens: (int) data_get($data, 'usage.prompt_tokens', 0),
            completionTokens: (int) data_get($data, 'usage.completion_tokens', 0),
            totalTokens: (int) data_get($data, 'usage.total_tokens', 0),
            raw: $data,
        );
    }
}
