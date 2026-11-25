<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Url extends Rule
{
    public function passes(mixed $value): bool
    {
        return is_string($value)
            && filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function message(): string
    {
        return 'The :field field must be a valid URL.';
    }
}
