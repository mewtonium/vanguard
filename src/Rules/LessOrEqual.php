<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class LessOrEqual extends Rule
{
    use PreparesDate;

    public function __construct(
        protected int|float|string|\DateTimeInterface $max,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        return $value <= $this->max;
    }

    public function message(): string
    {
        return 'The :field field must be less than or equal to :max.';
    }
}
