<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends Rule
{
    public function __construct(
        protected float|int|string $value,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value) || is_string($value)) {
            return $value === $this->value;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be equal to %s.',
            $this->ruleField,
            is_string($this->value) ? "'{$this->value}'" : $this->value,
        );
    }
}
