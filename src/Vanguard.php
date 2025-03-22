<?php

namespace Mewtonium\Vanguard;

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
        $this->errors ??= new ErrorBag; 

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
                $this->setRuleProperty($rule, 'value', $value);

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
}
