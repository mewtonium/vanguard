<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Contracts;

interface PreparesValue
{
    /**
     * Transform the value before validation.
     */
    public function prepare(mixed $value): mixed;
}
