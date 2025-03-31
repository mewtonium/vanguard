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

    public function passes(): bool
    {
        if (is_string($this->value)) {
            return trim($this->value) !== '';
        }

        if (is_array($this->value)) {
            return count($this->value) > 0;
        }

        return $this->value !== null;
    }

    public function message(): string
    {
        return "The {$this->field} field is required.";
    }
}
