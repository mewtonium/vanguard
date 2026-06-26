<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\CustomRules;

use Mewtonium\Vanguard\Rules\BaseRule;

/**
 * A custom rule authored the recommended way: by extending the abstract
 * {@see BaseRule} class, which provides `$field`/`$value` injection.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Uppercase extends BaseRule
{
    public function passes(): bool
    {
        return is_string($this->value) && mb_strtoupper($this->value) === $this->value;
    }

    public function message(): string
    {
        return "The {$this->field} field must be uppercase.";
    }
}
