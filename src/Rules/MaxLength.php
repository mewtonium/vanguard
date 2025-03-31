<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MaxLength extends Rule
{
    public function __construct(
        protected int $length,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        if (is_string($this->value)) {
            return mb_strlen($this->value) <= $this->length;
        }

        if (is_array($this->value)) {
            return count($this->value) <= $this->length;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be a maximum of %s %s %s.',
            $this->field,
            $this->length,
            is_array($this->value) ? 'items' : 'characters',
            is_array($this->value) ? 'in size' : 'long',
        );
    }
}
