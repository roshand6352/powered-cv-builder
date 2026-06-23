<?php

namespace App\Support\Html;

final class CoverLetterRichTextCleaner
{
    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    public static function cleanFields(array $fields): array
    {
        foreach (['company_address', 'introduction', 'body'] as $key) {
            if (array_key_exists($key, $fields)) {
                $fields[$key] = RichTextSanitizer::sanitize($fields[$key] ?? null);
            }
        }

        return $fields;
    }
}
