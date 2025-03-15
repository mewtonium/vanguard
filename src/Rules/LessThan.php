<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class LessThan implements Rule
{
    public function __construct(protected float|int $number)
    {
        //
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value)) {
            return $value < $this->number;
        }

        return false;
    }

    public function message(string $field, mixed $value): string
    {
        return sprintf(
            'The %s field must be less than %s.',
            $field,
            $this->number,
        );
    }
}
