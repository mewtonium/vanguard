<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MultipleOf extends Rule
{
    public function __construct(
        private int $factor,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value): bool
    {
        return is_int($value) && $value % $this->factor === 0;
    }

    public function message(): string
    {
        return 'The :field field must be a multiple of :factor.';
    }
}
