<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\ValidatesDates;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class LessOrEqual extends Rule implements ValidatesDates
{
    public function __construct(
        protected float|int|string|\DateTimeInterface $max,
        ?string $message = null,
    ) {
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
            $this->max instanceof \DateTimeInterface ? $this->max->format('Y-m-d H:i:s') : $this->max,
        );
    }
}
