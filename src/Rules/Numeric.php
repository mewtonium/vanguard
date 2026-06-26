<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Numeric extends BaseRule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        return is_numeric($this->value);
    }

    public function message(): string
    {
        return "The {$this->field} field must be numeric.";
    }
}
