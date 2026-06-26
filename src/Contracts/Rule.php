<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Contracts;

interface Rule
{
    /**
     * Whether the value passes validation.
     */
    public function passes(): bool;

    /**
     * The error message to report on failure.
     */
    public function message(): string;
}
