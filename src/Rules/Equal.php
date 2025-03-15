<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal implements Rule
{
    public function __construct(protected float|int|string $value)
    {
        //
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value) || is_string($value)) {
            return $value === $this->value;
        }

        return false;
    }

    public function message(string $field, mixed $value): string
    {
        return sprintf(
            'The %s field must be equal to %s.',
            $field,
            is_string($value) ? "'{$this->value}'" : $this->value,
        );
    }
}
