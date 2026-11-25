<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Alpha extends Rule
{
    public function passes(mixed $value): bool
    {
        return is_string($value) && ctype_alpha($value);
    }

    public function message(): string
    {
        return 'The :field field must only contain letters.';
    }
}
