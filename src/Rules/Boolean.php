<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Boolean extends Rule
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
            return is_bool($value);
        }

        return in_array($value, [true, false, 1, 0, '1', '0'], strict: true);
    }

    public function message(): string
    {
        return 'The :field field must be a boolean.';
    }
}
