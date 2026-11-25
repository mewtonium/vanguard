<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard;

use Mewtonium\Vanguard\Exceptions\RuleException;
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
        $this->errors = new ErrorBag();

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

                /** @var Rule */
                $rule = $attribute->newInstance();

                if (! $rule->check($property->getName(), $value)) {
                    $this->errors->add(
                        field: $property->getName(),
                        rule: class_basename($rule),
                        message: $rule->error(),
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
     * Rejects a custom rule whose name shadows a built-in Vanguard rule.
     *
     * @throws \Mewtonium\Vanguard\Exceptions\RuleException
     */
    private function guardAgainstReservedRuleName(string $rule): void
    {
        $class = class_basename($rule);
        $builtIn = 'Mewtonium\\Vanguard\\Rules\\' . $class;

        if ($rule !== $builtIn && class_exists($builtIn)) {
            throw new RuleException(
                "The [{$class}] rule already exists as a built-in Vanguard rule.",
            );
        }
    }
}
