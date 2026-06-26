<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Contracts\PreparesValue;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class GreaterThan extends BaseRule implements PreparesValue
{
    use PreparesDate;

    protected int|float|string|\DateTimeInterface $min;

    public function __construct(
        int|float|string|\DateTimeInterface $min,
        ?string $message = null,
    ) {
        $this->min = $this->prepare($min);

        parent::__construct($message);
    }

    public function passes(): bool
    {
        return $this->value > $this->min;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be greater than %s.',
            $this->field,
            $this->format($this->min),
        );
    }
}
