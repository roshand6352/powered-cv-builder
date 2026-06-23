<?php

namespace App\Services\Ai;

class GroqClient extends AbstractAiChatClient
{
    protected function providerKey(): string
    {
        return 'groq';
    }

    protected function providerLabel(): string
    {
        return 'Groq';
    }
}
