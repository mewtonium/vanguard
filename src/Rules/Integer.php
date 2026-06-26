<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Integer extends BaseRule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        if (is_bool($this->value)) {
            return false;
        }

        return filter_var($this->value, FILTER_VALIDATE_INT) !== false;
    }

    public function message(): string
    {
        return "The {$this->field} field must be an integer.";
    }
}
