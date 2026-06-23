<?php

namespace App\Contracts\Ai;

use App\DTOs\Ai\AiCompletionResult;

interface AiClientInterface
{
    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function chat(array $messages, array $options = []): AiCompletionResult;
}
