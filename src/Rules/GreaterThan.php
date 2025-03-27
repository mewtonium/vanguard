<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Rules\Rule;
use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class GreaterThan extends Rule implements ValidatesDates
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
            return $value > $this->value;
        }

        if (is_string($value) || $value instanceof \DateTimeInterface) {
            return $this->validateDate();
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be greater than %s.',
            $this->ruleField,
            $this->value,
        );
    }

    public function validateDate(): bool
    {
        if (is_null($value = to_date($this->value))) {
            throw new RuleException('The value set on the [' . class_basename($this) . '] rule must be a valid date string.');
        }

        if (is_null($ruleValue = to_date($this->ruleValue))) {
            throw new RuleException('The value passed into the [' . class_basename($this) . '] rule to validate is not a valid date string.');
        }

        return $ruleValue > $value;
    }
}
