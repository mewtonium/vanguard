<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Email implements Rule
{
    public function passes(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function message(string $field, mixed $value): string
    {
        return "The {$field} field must be a valid email.";
    }
}
