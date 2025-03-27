<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Rules\Rule;
use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Between extends Rule implements ValidatesDates
{
    public function __construct(
        protected int|float|string $min,
        protected int|float|string $max,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value) && is_numeric($this->min) && is_numeric($this->max)) {
            return $value >= $this->min && $value <= $this->max;
        }

        if (is_string($value) || $value instanceof \DateTimeInterface) {
            return $this->validateDate();
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be between %s and %s.',
            $this->ruleField,
            $this->min,
            $this->max,
        );
    }

    public function validateDate(): bool
    {
        try {
            $min = new \DateTimeImmutable($this->min);
            $max = new \DateTimeImmutable($this->max);
        } catch (\DateException $e) {
            throw new RuleException('Both `min` and `max` set on the [' . class_basename($this) . '] rule must be valid date strings.');
        }

        if ($min > $max || $max < $min) {
            throw new RuleException('Date range set on the [' . class_basename($this) . '] rule is invalid.');
        }

        if (is_string($value = $this->ruleValue)) {
            try {
                $value = new \DateTimeImmutable($value);
            } catch (\DateException $e) {
                throw new RuleException('The value passed into the [' . class_basename($this) . '] rule to validate is not a valid date string.');
            }
        }

        return $value >= $min && $value <= $max;
    }
}
