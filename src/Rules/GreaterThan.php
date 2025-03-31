<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\ValidatesDates;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class GreaterThan extends Rule implements ValidatesDates
{
    public function __construct(
        protected float|int|string|\DateTimeInterface $min,
        ?string $message = null,
    ) {
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
            $this->min instanceof \DateTimeInterface ? $this->min->format('Y-m-d H:i:s') : $this->min,
        );
    }
}
