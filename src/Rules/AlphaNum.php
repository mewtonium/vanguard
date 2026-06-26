<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class AlphaNum extends BaseRule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        return is_string($this->value) && ctype_alnum($this->value);
    }

    public function message(): string
    {
        return "The {$this->field} field must only contain letters and numbers.";
    }
}
