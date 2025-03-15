<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Min implements Rule
{
    public function __construct(protected int $length)
    {
        //
    }

    public function passes(mixed $value): bool
    {
        if (is_string($value)) {
            return mb_strlen($value) >= $this->length;
        }

        if (is_array($value)) {
            return count($value) >= $this->length;
        }

        return false;
    }

    public function message(string $field, mixed $value): string
    {
        return sprintf(
            'The %s field must be a minimum of %s %s %s.',
            $field,
            $this->length,
            is_array($value) ? 'items' : 'characters',
            is_array($value) ? 'in size' : 'long',
        );
    }
}
