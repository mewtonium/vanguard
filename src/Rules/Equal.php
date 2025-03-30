<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends Rule implements ValidatesDates
{
    public function __construct(
        protected float|int|string $to,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value)) {
            return $value === $this->to;
        }

        if (is_string($value)) {
            if (! is_null(to_date($value))) {
                return $this->validateDate();
            }

            return $value === $this->to;
        }

        return false;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be equal to %s.',
            $this->field,
            is_string($this->to) ? "'{$this->to}'" : $this->to,
        );
    }

    public function validateDate(): bool
    {
        if (is_null($to = to_date($this->to))) {
            throw new RuleException('The value set on the [' . class_basename($this) . '] rule must be a valid date string.');
        }

        if (is_null($value = to_date($this->value))) {
            throw new RuleException('The value passed into the [' . class_basename($this) . '] rule to validate is not a valid date string.');
        }

        return $value == $to; // we have to use loose comparison here
    }
}
