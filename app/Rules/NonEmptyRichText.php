<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NonEmptyRichText implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plain = trim(strip_tags(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        if ($plain === '') {
            $fail('Add some text before clicking Improve.');
        }
    }
}
