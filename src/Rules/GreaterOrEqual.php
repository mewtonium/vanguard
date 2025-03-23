<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class GreaterOrEqual extends Rule
{
    public function __construct(
        protected float|int $value,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value)) {
            return $value >= $this->value;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be greater than or equal to %s.',
            $this->ruleField,
            $this->value,
        );
    }
}
