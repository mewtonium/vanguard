<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Integer extends Rule
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
            return is_int($value);
        }

        if (is_bool($value) || is_float($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function message(): string
    {
        return 'The :field field must be an integer.';
    }
}
