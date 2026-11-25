<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Concerns\PreparesDate;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Equal extends Rule
{
    use PreparesDate;

    protected bool $lenient = true;

    public function __construct(
        protected float|int|string|\DateTimeInterface $to,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        if ($value instanceof \DateTimeInterface && $this->to instanceof \DateTimeInterface) {
            return $value == $this->to;
        }

        return $value === $this->to;
    }

    public function message(): string
    {
        return 'The :field field must be equal to :to.';
    }

    #[\Override]
    protected function formatValueForMessage(mixed $value): string
    {
        return is_string($value) ? "'{$value}'" : parent::formatValueForMessage($value);
    }
}
