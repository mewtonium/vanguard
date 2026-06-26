<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Concerns;

trait PreparesDate
{
    /**
     * Determines whether your rule should accept non-date strings and date strings.
     */
    protected bool $lenient = false;

    /**
     * Cast the value to a date, leaving non-strings untouched.
     */
    public function prepare(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return $this->lenient
            ? (to_date($value) ?? $value)
            : to_date($value, throw: true);
    }

    /**
     * Formats a value for the failed validation message.
     */
    protected function format(mixed $value): string
    {
        return $value instanceof \DateTimeInterface
            ? $value->format('Y-m-d H:i:s')
            : (string) $value;
    }
}
