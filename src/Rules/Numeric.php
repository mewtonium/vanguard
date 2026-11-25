<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Numeric extends Rule
{
    public function __construct(
        protected bool $strict = false,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        if ($this->strict) {
            return is_int($value) || is_float($value);
        }

        return is_numeric($value);
    }

    public function message(): string
    {
        return 'The :field field must be numeric.';
    }
}
