<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class In extends Rule
{
    public function __construct(
        protected array $values = [],
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        if (count($this->values) === 0) {
            throw new RuleException('The [' . class_basename($this) . '] rule must have at least one value to check against.');
        }

        return in_array($value, $this->values);
    }

    public function message(): string
    {
        return 'The :field field does not have a valid selection.';
    }
}
