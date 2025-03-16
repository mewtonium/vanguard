<?php

namespace Mewtonium\Vanguard;

use Mewtonium\Vanguard\Contracts\Rule;

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

                if (! $rule->passes($value)) {
                    $this->errors->add(
                        field: $property->getName(),
                        rule: class_basename($rule),
                        message: $this->getMessage($attribute, $rule, $property, $value),
                    );
                }
            }
        }
    }

    /**
     * Get the `Rule` default or custom validation message.
     */
    protected function getMessage(\ReflectionAttribute $attribute, Rule $rule, \ReflectionProperty $property, mixed $value): string
    {
        return array_key_exists('message', $arguments = $attribute->getArguments())
            ? $arguments['message']
            : $rule->message($property->getName(), $value);
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
}
