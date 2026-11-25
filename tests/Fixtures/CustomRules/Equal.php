<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Rules\Rule;

/**
 * A custom rule that deliberately reuses the name of the built-in
 * {@see \Mewtonium\Vanguard\Rules\Equal} rule to exercise the collision guard.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends Rule
{
    public function passes(mixed $value): bool
    {
        return true;
    }

    public function message(): string
    {
        return '';
    }
}
