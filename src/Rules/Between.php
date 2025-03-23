<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Between extends Rule
{
    public function __construct(
        protected int|float $min,
        protected int|float $max,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value)) {
            return $value >= $this->min && $value <= $this->max;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be between %s and %s.',
            $this->ruleField,
            $this->min,
            $this->max,
        );
    }
}
