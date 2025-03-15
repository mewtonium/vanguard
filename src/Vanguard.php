<?php

namespace Mewtonium\Vanguard;

use Mewtonium\Vanguard\Contracts\Rule;

trait Vanguard
{
    /**
     * The validation errors.
     */
    protected array $errors = [];

    /**
     * Validate properties marked with `Rule` attributes.
     */
    public function validate(): void
    {
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
                    $message = array_key_exists('message', $arguments = $attribute->getArguments())
                        ? $arguments['message']
                        : $rule->message($property->getName(), $value);

                    $this->errors[$property->getName()][class_basename($rule)] = $message;
                }
            }
        }
    }

    /**
     * Get a list of validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Indicates if validation failed or not.
     */
    public function invalid(): bool
    {
        return count($this->errors()) > 0;
    }
}
