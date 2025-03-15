<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Between implements Rule
{
    public function __construct(
        protected float $from,
        protected float $to,
        protected bool $inclusive = true,
        protected ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        if (is_numeric($value)) {
            return $this->inclusive
                ? $value >= $this->from && $value <= $this->to
                : $value > $this->from && $value < $this->to;
        }

        return false;
    }

    public function message(string $field, mixed $value): string
    {
        return sprintf(
            'The %s field must be between %s and %s.',
            $field,
            $this->from,
            $this->to,
        );
    }
}
