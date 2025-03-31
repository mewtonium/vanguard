<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\ValidatesDates;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends Rule implements ValidatesDates
{
    public function __construct(
        protected float|int|string|\DateTimeInterface $to,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        return $this->value instanceof \DateTimeInterface
            ? $this->value == $this->to
            : $this->value === $this->to;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be equal to %s.',
            $this->field,
            match (true) {
                is_string($this->to) => "'{$this->to}'",
                $this->to instanceof \DateTimeInterface => $this->to->format('Y-m-d H:i:s'),
                default => $this->to,
            },
        );
    }
}
