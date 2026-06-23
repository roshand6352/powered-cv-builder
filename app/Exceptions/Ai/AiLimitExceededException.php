<?php

namespace App\Exceptions\Ai;

use Exception;

class AiLimitExceededException extends Exception
{
    public function __construct(
        public readonly int $used,
        public readonly int $limit,
    ) {
        parent::__construct("AI credit limit reached ({$used}/{$limit}). Upgrade your plan for more credits.");
    }
}
