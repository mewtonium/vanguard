<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Between extends Rule
{
    use PreparesDate;

    public function __construct(
        protected int|float|string|\DateTimeInterface $min,
        protected int|float|string|\DateTimeInterface $max,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        if ($this->min > $this->max) {
            if ($this->min instanceof \DateTimeInterface && $this->max instanceof \DateTimeInterface) {
                throw new RuleException('Date range set on the [' . class_basename($this) . '] rule is invalid.');
            }

            throw new RuleException('Number range set on the [' . class_basename($this) . '] rule is invalid.');
        }

        return $value >= $this->min && $value <= $this->max;
    }

    public function message(): string
    {
        return 'The :field field must be between :min and :max.';
    }
}
