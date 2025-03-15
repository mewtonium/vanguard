<?php

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Contracts\Rule;
use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class In implements Rule
{
    public function __construct(
        protected array $in = [],
        protected ?string $message = null,
    ) {
        if (count($in) === 0) {
            throw new RuleException('The [' . class_basename($this) . '] rule must have at least one value to check against.');
        }
    }

    public function passes(mixed $value): bool
    {
        return in_array($value, $this->in);
    }

    public function message(string $field, mixed $value): string
    {
        return sprintf(
            'The %s field does not have a valid selection.',
            $field,
        );
    }
}
