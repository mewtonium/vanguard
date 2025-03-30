<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

abstract class Rule
{
    /**
     * The field under validation by this rule.
     */
    protected string $ruleField;

    /**
     * The value under validation by this rule.
     */
    protected mixed $ruleValue;

    /**
     * Create a new instance of this rule.
     */
    public function __construct(
        protected ?string $message = null,
    ) {
        //
    }

    /**
     * Determine if the provided value passes validation.
     */
    abstract public function passes(mixed $value): bool;

    /**
     * Define the validation error message.
     */
    abstract public function message(): string;

    /**
     * Gets either a custom or default validation message.
     */
    public function getMessage(): string
    {
        return $this->message ?: $this->message();
    }
}
