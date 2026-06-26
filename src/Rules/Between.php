<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Contracts\PreparesValue;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Between extends BaseRule implements PreparesValue
{
    use PreparesDate;

    protected int|float|string|\DateTimeInterface $min;

    protected int|float|string|\DateTimeInterface $max;

    public function __construct(
        int|float|string|\DateTimeInterface $min,
        int|float|string|\DateTimeInterface $max,
        ?string $message = null,
    ) {
        $this->min = $this->prepare($min);
        $this->max = $this->prepare($max);

        parent::__construct($message);
    }

    public function passes(): bool
    {
        if ($this->min > $this->max) {
            if ($this->min instanceof \DateTimeInterface && $this->max instanceof \DateTimeInterface) {
                throw new RuleException('Date range set on the [' . class_basename($this) . '] rule is invalid.');
            }

            throw new RuleException('Number range set on the [' . class_basename($this) . '] rule is invalid.');
        }

        return $this->value >= $this->min && $this->value <= $this->max;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be between %s and %s.',
            $this->field,
            $this->format($this->min),
            $this->format($this->max),
        );
    }
}
