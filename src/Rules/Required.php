<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Required extends Rule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return count($value) > 0;
        }

        return $value !== null;
    }

    public function message(): string
    {
        return "The {$this->ruleField} field is required.";
    }
}
