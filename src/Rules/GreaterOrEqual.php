<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class GreaterOrEqual extends Rule
{
    use PreparesDate;

    public function __construct(
        protected int|float|string|\DateTimeInterface $min,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        return $value >= $this->min;
    }

    public function message(): string
    {
        return 'The :field field must be greater than or equal to :min.';
    }
}
