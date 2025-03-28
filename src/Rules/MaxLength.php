<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MaxLength extends Rule
{
    public function __construct(
        protected int $length,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_string($value)) {
            return mb_strlen($value) <= $this->length;
        }

        if (is_array($value)) {
            return count($value) <= $this->length;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be a maximum of %s %s %s.',
            $this->ruleField,
            $this->length,
            is_array($this->ruleValue) ? 'items' : 'characters',
            is_array($this->ruleValue) ? 'in size' : 'long',
        );
    }
}
