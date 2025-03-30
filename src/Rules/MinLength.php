<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MinLength extends Rule
{
    public function __construct(
        protected int $length,
        ?string $message = null,
    ) {
        parent::__construct($message);
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
        return sprintf(
            'The %s field must be a minimum of %s %s %s.',
            $this->field,
            $this->length,
            is_array($this->value) ? 'items' : 'characters',
            is_array($this->value) ? 'in size' : 'long',
        );
    }
}
