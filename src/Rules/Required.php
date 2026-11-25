<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Required extends Rule
{
    public function passes(mixed $value): bool
    {
        if (is_string($value)) {
            return mb_trim($value) !== '';
        }

        if (is_array($value)) {
            return count($value) > 0;
        }

        return $value !== null;
    }

    public function message(): string
    {
        return 'The :field field is required.';
    }
}
