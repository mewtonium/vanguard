<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Between extends Rule implements ValidatesDates
{
    public function __construct(
        protected int|float|string|\DateTimeInterface $min,
        protected int|float|string|\DateTimeInterface $max,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        if (
            $this->min instanceof \DateTimeInterface
            && $this->max instanceof \DateTimeInterface
            && ($this->min > $this->max || $this->max < $this->min)
        ) {
            throw new RuleException('Date range set on the [' . class_basename($this) . '] rule is invalid.');
        }

        return $this->value >= $this->min && $this->value <= $this->max;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be between %s and %s.',
            $this->field,
            $this->min instanceof \DateTimeInterface ? $this->min->format('Y-m-d H:i:s') : $this->min,
            $this->max instanceof \DateTimeInterface ? $this->max->format('Y-m-d H:i:s') : $this->max,
        );
    }
}
