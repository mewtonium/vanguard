<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends Rule implements ValidatesDates
{
    public function __construct(
        protected float|int|string $value,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (! is_null(to_date($value))) {
            return $this->validateDate();
        }

        if (is_numeric($value) || is_string($value)) {
            return $value === $this->value;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be equal to %s.',
            $this->ruleField,
            is_string($this->value) ? "'{$this->value}'" : $this->value,
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

        return $ruleValue == $value; // we have to use loose comparison here
    }
}
