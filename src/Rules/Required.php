<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Required implements Rule
{
    public function passes(mixed $value): bool
    {
        if (is_string($value)) {
            return $value !== '';
        }

        if (is_array($value)) {
            return count($value) > 0;
        }

        return $value !== null;
    }

    public function message(string $field, mixed $value): string
    {
        return "The {$field} field is required.";
    }
}
