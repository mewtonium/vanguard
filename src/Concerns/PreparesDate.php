<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Concerns;

trait PreparesDate
{
    #[\Override]
    public function prepare(mixed $value): mixed
    {
        return is_string($value) ? to_date($value, throw: true) : $value;
    }
}
