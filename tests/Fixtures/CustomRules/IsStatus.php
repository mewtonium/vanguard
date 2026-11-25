<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Rules\Rule;
use Mewtonium\Vanguard\Tests\Fixtures\Enums\Status;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class IsStatus extends Rule
{
    protected bool $lenient = true;

    #[\Override]
    public function prepare(mixed $value): mixed
    {
        return is_string($value) ? Status::from($value) : $value;
    }

    public function passes(mixed $value): bool
    {
        return $value instanceof Status;
    }

    public function message(): string
    {
        return 'The :field field must be a valid status.';
    }
}
