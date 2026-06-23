<?php

namespace App\Services\Ai;

use App\Services\BaseService;

class AiResponseParser extends BaseService
{
    /**
     * @return array<string, mixed>|null
     */
    public function decodeJsonContent(string $content): ?array
    {
        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $content, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($content, $start, $end - $start + 1), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
