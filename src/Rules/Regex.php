<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Regex extends Rule
{
    public function __construct(
        protected string $pattern,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $result = preg_match($this->pattern, $value);

        if ($result === false) {
            throw new RuleException('The pattern supplied to the [' . class_basename($this) . '] rule is invalid.');
        }

        return $result === 1;
    }

    public function message(): string
    {
        return 'The :field field is in an invalid format.';
    }
}
