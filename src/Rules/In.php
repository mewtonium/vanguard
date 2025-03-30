<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class In extends Rule
{
    public function __construct(
        protected array $values = [],
        ?string $message = null,
    ) {
        parent::__construct($message);

        if (count($values) === 0) {
            throw new RuleException('The [' . class_basename($this) . '] rule must have at least one value to check against.');
        }
    }

    public function passes(mixed $value): bool
    {
        return in_array($value, $this->values);
    }

    public function message(): string
    {
        return sprintf(
            'The %s field does not have a valid selection.',
            $this->field,
        );
    }
}
