<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Rules;

use Mewtonium\Vanguard\Exceptions\RuleException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Regex extends BaseRule
{
    /**
     * @param  string  $pattern  A trusted, developer-supplied PCRE pattern. The
     *                           pattern is defined in your form, never derived
     *                           from end-user input, so ReDoS exposure is your
     *                           responsibility to manage when authoring it.
     */
    public function __construct(
        protected string $pattern,
        ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        if (! is_string($this->value)) {
            return false;
        }

        $result = preg_match($this->pattern, $this->value);

        if ($result === false) {
            throw new RuleException('The pattern supplied to the [' . class_basename($this) . '] rule is invalid.');
        }

        return $result === 1;
    }

    public function message(): string
    {
        return "The {$this->field} field is in an invalid format.";
    }
}
