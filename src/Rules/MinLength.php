<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MinLength extends Rule
{
    public function __construct(
        protected int $length,
        public ?string $message = null,
    ) {
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

    public function message(): string
    {
        return is_array($this->value)
            ? 'The :field field must have at least :length {item|items}.'
            : 'The :field field must be at least :length {character|characters}.';
    }
}
