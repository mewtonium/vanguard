<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Uppercase extends Rule
{
    public function passes(mixed $value): bool
    {
        return is_string($value) && mb_strtoupper($value) === $value;
    }

    public function message(): string
    {
        return 'The :field field must be uppercase.';
    }
}
