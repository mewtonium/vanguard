<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Rules\Rule;
use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class LessOrEqual extends Rule implements ValidatesDates
{
    public function __construct(
        protected float|int|string $value,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value)) {
            return $value <= $this->value;
        }

        if (is_string($value) || $value instanceof \DateTimeInterface) {
            return $this->validateDate();
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be less than or equal to %s.',
            $this->ruleField,
            $this->value,
        );
    }

    public function validateDate(): bool
    {
        try {
            $date = new \DateTimeImmutable($this->value);
        } catch (\DateException $e) {
            throw new RuleException('The value set on the [' . class_basename($this) . '] rule must be a valid date string.');
        }

        if (is_string($value = $this->ruleValue)) {
            try {
                $value = new \DateTimeImmutable($value);
            } catch (\DateException $e) {
                throw new RuleException('The value passed into the [' . class_basename($this) . '] rule to validate is not a valid date string.');
            }
        }

        return $value <= $date;
    }
}
