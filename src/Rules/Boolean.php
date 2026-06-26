<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Boolean extends BaseRule
{
    public function __construct(
        protected bool $strict = false,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        if ($this->strict) {
            return is_bool($this->value);
        }

        return in_array($this->value, [true, false, 1, 0, '1', '0'], strict: true);
    }

    public function message(): string
    {
        return "The {$this->field} field must be a boolean.";
    }
}
