<?php

namespace Mewtonium\Vanguard\Contracts;

interface Rule
{
    /**
     * Determine if the provided value passes validation.
     */
    public function passes(mixed $value): bool;

    /**
     * Define the validation error message.
     */
    public function message(string $field, mixed $value): string;
}
