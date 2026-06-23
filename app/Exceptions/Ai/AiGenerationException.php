<?php

namespace App\Exceptions\Ai;

use Exception;

class AiGenerationException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?array $context = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
