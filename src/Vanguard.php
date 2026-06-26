<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard;

use Mewtonium\Vanguard\Contracts\PreparesValue;
use Mewtonium\Vanguard\Contracts\Rule;
use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\BaseRule;

trait Vanguard
{
    /**
     * The validation error bag.
     */
    protected ErrorBag $errors;

    /**
     * Validate properties marked with `Rule` attributes.
     */
    public function validate(): void
    {
        $this->errors ??= new ErrorBag();

        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(
                Rule::class,
                \ReflectionAttribute::IS_INSTANCEOF,
            );

            if ($attributes === []) {
                continue;
            }

            // Treat an uninitialised typed property as a missing value so that
            // rules such as `Required` still fire instead of being skipped.
            $value = $property->isInitialized($this)
                ? $property->getValue($this)
                : null;

            foreach ($attributes as $attribute) {
                $this->guardAgainstReservedRuleName($attribute->getName());

                /** @var BaseRule */
                $rule = $attribute->newInstance();

                $this->setRuleProperty($rule, 'field', $property->getName());

                $this->setRuleProperty(
                    $rule,
                    name: 'value',
                    value: $rule instanceof PreparesValue ? $rule->prepare($value) : $value,
                );

                if (! $rule->passes()) {
                    $this->errors->add(
                        field: $property->getName(),
                        rule: class_basename($rule),
                        message: $rule->message ?? $rule->message(),
                    );
                }
            }
        }
    }

    /**
     * Fetches the validation error bag.
     */
    public function errors(): ErrorBag
    {
        return $this->errors;
    }

    /**
     * Indicates if validation failed or not.
     */
    public function invalid(): bool
    {
        return $this->errors->count() > 0;
    }

    /**
     * Sets a protected property on the given rule instance using Reflection.
     */
    private function setRuleProperty(BaseRule $rule, string $name, mixed $value): void
    {
        (new \ReflectionObject($rule))->getProperty($name)->setValue($rule, $value);
    }

    /**
     * Rejects a custom rule whose name shadows a built-in Vanguard rule.
     *
     * @throws \Mewtonium\Vanguard\Exceptions\RuleException
     */
    private function guardAgainstReservedRuleName(string $rule): void
    {
        $builtIn = 'Mewtonium\\Vanguard\\Rules\\' . class_basename($rule);

        if ($rule !== $builtIn && class_exists($builtIn)) {
            throw new RuleException(
                'The [' . class_basename($rule) . '] rule already exists as a built-in Vanguard rule.',
            );
        }
    }
}
