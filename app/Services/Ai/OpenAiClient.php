<?php

namespace App\Services\Ai;

use App\Contracts\Ai\OpenAiClientInterface;

class OpenAiClient extends AbstractAiChatClient implements OpenAiClientInterface
{
    protected function providerKey(): string
    {
        return 'openai';
    }

    protected function providerLabel(): string
    {
        return 'OpenAI';
    }

    /**
     * @return array<string, string>
     */
    protected function extraHeaders(): array
    {
        $organization = config('ai.openai.organization');

        if (blank($organization)) {
            return [];
        }

        return [
            'OpenAI-Organization' => (string) $organization,
        ];
    }

    /**
     * @return array<string, mixed>|null
     *
     * @deprecated Use AiResponseParser::decodeJsonContent() instead.
     */
    public function decodeJsonContent(string $content): ?array
    {
        return app(AiResponseParser::class)->decodeJsonContent($content);
    }
}
