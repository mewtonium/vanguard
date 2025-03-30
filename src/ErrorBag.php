<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard;

final class ErrorBag
{
    /**
     * The validation errors.
     */
    protected array $errors = [];

    /**
     * Returns a list of all errors.
     */
    public function all(): array
    {
        return $this->errors;
    }

    /**
     * Get an error from the bag by field.
     */
    public function get(string $field): ?array
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Add an error to the bag by field and rule.
     */
    public function add(string $field, string $rule, string $message): void
    {
        $this->errors[$field][$rule] = $message;
    }

    /**
     * Get the first error from the bag by field.
     */
    public function first(string $field): ?string
    {
        $error = $this->get($field) ?: [];

        return reset($error) ?: null;
    }

    /**
     * Check if an error exists in the bag by field.
     */
    public function has(string $field): bool
    {
        return array_key_exists($field, $this->all());
    }

    /**
     * Counts how many errors are in the bag.
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Removes all errors from the bag.
     */
    public function flush(): void
    {
        $this->errors = [];
    }
}
