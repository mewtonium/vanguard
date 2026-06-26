<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Contracts\PreparesValue;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class LessOrEqual extends BaseRule implements PreparesValue
{
    use PreparesDate;

    protected int|float|string|\DateTimeInterface $max;

    public function __construct(
        int|float|string|\DateTimeInterface $max,
        ?string $message = null,
    ) {
        $this->max = $this->prepare($max);

        parent::__construct($message);
    }

    public function passes(): bool
    {
        return $this->value <= $this->max;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be less than or equal to %s.',
            $this->field,
            $this->format($this->max),
        );
    }
}
