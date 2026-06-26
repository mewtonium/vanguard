<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Url extends BaseRule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        return is_string($this->value)
            && filter_var($this->value, FILTER_VALIDATE_URL) !== false;
    }

    public function message(): string
    {
        return "The {$this->field} field must be a valid URL.";
    }
}
