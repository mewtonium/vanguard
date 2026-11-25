<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class After extends Rule
{
    use PreparesDate;

    public function __construct(
        protected string|\DateTimeInterface $date,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value): bool
    {
        return $value instanceof \DateTimeInterface && $value > $this->date;
    }

    public function message(): string
    {
        return 'The :field field must be after :date.';
    }
}
