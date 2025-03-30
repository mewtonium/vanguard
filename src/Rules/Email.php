<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Email extends Rule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function message(): string
    {
        return "The {$this->ruleField} field must be a valid email.";
    }
}
