<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Contracts\PreparesValue;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends BaseRule implements PreparesValue
{
    use PreparesDate;

    protected float|int|string|\DateTimeInterface $to;

    public function __construct(
        float|int|string|\DateTimeInterface $to,
        ?string $message = null,
    ) {
        // Equality may legitimately target a non-date string, so cast leniently.
        $this->lenient = true;

        $this->to = $this->prepare($to);

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
            is_string($this->to) ? "'{$this->to}'" : $this->format($this->to),
        );
    }
}
