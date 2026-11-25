<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Same extends Rule
{
    use PreparesDate;

    protected bool $lenient = true;

    public function __construct(
        protected string|\DateTimeInterface $as,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value): bool
    {
        if ($value instanceof \DateTimeInterface && $this->as instanceof \DateTimeInterface) {
            return $value == $this->as;
        }

        return $value === $this->as;
    }

    public function message(): string
    {
        return 'The :field field must be the same as :as.';
    }
}
