<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard;

use Mewtonium\Vanguard\Contracts\ValidatesDates;
use Mewtonium\Vanguard\Rules\Rule;

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
            if (! $property->isInitialized($this)) {
                continue;
            }

            $value = $property->getValue($this);

            $attributes = $property->getAttributes(
                Rule::class,
                \ReflectionAttribute::IS_INSTANCEOF,
            );

            foreach ($attributes as $attribute) {
                /** @var Rule */
                $rule = $attribute->newInstance();

                $this->setRuleProperty($rule, 'field', $property->getName());

                if ($rule instanceof ValidatesDates) {
                    $this->setDateRuleProperties($rule);
                    $this->setRuleProperty($rule, 'value', is_string($value) ? (to_date($value) ?: $value) : $value);
                } else {
                    $this->setRuleProperty($rule, 'value', $value);
                }

                if (! $rule->passes($value)) {
                    $this->errors->add(
                        field: $property->getName(),
                        rule: class_basename($rule),
                        message: $rule->getMessage(),
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
     * Sets a property on the base `Rule` instance using Reflection.
     */
    private function setRuleProperty(Rule &$rule, string $name, mixed $value): void
    {
        $reflection = new \ReflectionObject($rule);

        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($rule, $value);
    }

    /**
     * Sets a date property on the `Rule` instance using Reflection.
     */
    private function setDateRuleProperties(Rule &$rule): void
    {
        $reflection = new \ReflectionObject($rule);

        $parentProperties = array_map(
            array: (new \ReflectionClass(Rule::class))->getProperties(),
            callback: fn (\ReflectionProperty $property) => $property->getName(),
        );

        foreach ($reflection->getProperties() as $property) {
            if (in_array($property->getName(), $parentProperties)) {
                continue;
            }

            if (is_string($value = $property->getValue($rule)) && ! is_null($date = to_date($value))) {
                $this->setRuleProperty($rule, $property->getName(), $date);
            }
        }
    }
}
