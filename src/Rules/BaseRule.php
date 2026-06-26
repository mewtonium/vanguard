<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;

abstract class BaseRule implements Rule
{
    /**
     * The field under validation by this rule.
     */
    protected string $field;

    /**
     * The value under validation by this rule.
     */
    protected mixed $value;

    /**
     * Create a new instance of this rule.
     */
    public function __construct(
        public ?string $message = null,
    ) {
        //
    }
}
