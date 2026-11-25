<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

abstract class Rule
{
    /**
     * The name of the field under validation.
     */
    protected string $field;

    /**
     * The value under validation, after `prepare()` has run.
     */
    protected mixed $value;

    /**
     * Whether to keep the original value when `prepare()` throws, rather than rethrowing.
     */
    protected bool $lenient = false;

    /**
     * Create a new instance of this rule.
     */
    public function __construct(
        public ?string $message = null,
    ) {
        //
    }

    /**
     * Whether the value passes validation.
     */
    abstract public function passes(mixed $value): bool;

    /**
     * The default failure message, which may use `:field` and argument placeholders.
     */
    abstract public function message(): string;

    /**
     * Prepare the value and arguments, then run the rule.
     *
     * @internal
     */
    public function check(string $field, mixed $value): bool
    {
        $this->field = $field;

        foreach ($this->arguments() as $argument) {
            $original = $argument->getValue($this);
            $prepared = $this->tryPrepare($original);

            if ($prepared !== $original) {
                $argument->setValue($this, $prepared);
            }
        }

        $this->value = $this->tryPrepare($value);

        return $this->passes($this->value);
    }

    /**
     * Transform the value and rule arguments before validation.
     */
    public function prepare(mixed $value): mixed
    {
        return $value;
    }

    /**
     * The failure message with its placeholders and plural groups replaced.
     *
     * @internal
     */
    public function error(): string
    {
        $placeholders = ['field' => [$this->field, $this->field]];

        foreach ($this->arguments() as $argument) {
            $value = $argument->getValue($this);

            $placeholders[$argument->getName()] = [$value, $this->formatValueForMessage($value)];
        }

        // Matches `:name`, optionally followed by a `{singular|plural}` group.
        return preg_replace_callback(
            '/:([a-zA-Z_][a-zA-Z0-9_]*)(\s*)(?:\{([^{}|]*)\|([^{}|]*)\})?/',
            function (array $match) use ($placeholders): string {
                [$placeholder, $name, $space, $singular, $plural] = $match;

                if (! array_key_exists($name, $placeholders)) {
                    return $placeholder;
                }

                [$value, $formatted] = $placeholders[$name];

                if ($singular === null) {
                    return $formatted . $space;
                }

                $count = is_array($value) ? count($value) : $value;

                return $formatted . $space . (is_numeric($count) && abs((float) $count) === 1.0 ? $singular : $plural);
            },
            $this->message ?? $this->message(),
            flags: PREG_UNMATCHED_AS_NULL,
        );
    }

    /**
     * Formats a value for the failure message.
     */
    protected function formatValueForMessage(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if (is_array($value)) {
            return implode(', ', array_map($this->formatValueForMessage(...), $value));
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value) || $value === null || $value instanceof \Stringable) {
            return (string) $value;
        }

        return get_debug_type($value);
    }

    /**
     * Prepare a value, keeping the original if a lenient rule can't convert it.
     */
    private function tryPrepare(mixed $value): mixed
    {
        try {
            return $this->prepare($value);
        } catch (\Exception|\ValueError $e) {
            if ($this->lenient) {
                return $value;
            }

            throw $e;
        }
    }

    /**
     * The promoted constructor arguments of this rule, excluding `message`.
     *
     * @return list<\ReflectionProperty>
     */
    private function arguments(): array
    {
        $arguments = [];
        $parameters = (new \ReflectionMethod($this, '__construct'))->getParameters();

        foreach ($parameters as $parameter) {
            if ($parameter->isPromoted() && $parameter->getName() !== 'message') {
                $arguments[] = new \ReflectionProperty($this, $parameter->getName());
            }
        }

        return $arguments;
    }
}
