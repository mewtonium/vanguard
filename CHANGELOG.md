# Changelog

All notable changes to this project will be documented in this file.

## [v3.0.0] - 2026-11-25

A trimmed-down release focused on making rules - built-in and custom - far quicker to write.

### Added

- A command to allow creating custom rules
- Message placeholders - e.g. `:field` and `:min`
- Plural groups in messages - e.g. `{character|characters}`
- `Rule::prepare()` to transform values before validation
- `Rule::formatValueForMessage()` to control how arguments appear in messages
- A `strict` option on `Integer` and `Numeric`

### Changed

- **Requires PHP 8.4 or later**
- `Rules\BaseRule` is renamed to `Rules\Rule`
- `passes()` receives the value under validation
- Default messages use placeholders instead of `sprintf()`
- Rules with arguments use promoted constructor properties instead of calling `parent::__construct()`
- `$lenient` has moved from `PreparesDate` to `Rule`
- `Integer` rejects floats
- `MinLength` and `MaxLength` have shorter, pluralised messages

### Removed

- `Contracts\Rule` - extend `Rules\Rule` instead
- `Contracts\PreparesValue` - override `prepare()` instead
- `PreparesDate::format()` - replaced by `Rule::formatValueForMessage()`

### Fixed

- `Equal` no longer treats a date as equal to a number
- Calling `validate()` more than once no longer keeps errors from earlier runs; each call now starts with a fresh
  `ErrorBag`

### Upgrading from v2

1. Upgrade to PHP 8.4 or later.
2. Replace `extends BaseRule` with `extends Rule`, and remove `implements PreparesValue`.
3. Change `passes(): bool` to `passes(mixed $value): bool`, and use `$value` in place of `$this->value`.
4. Delete constructors that only pass `$message` to the parent.
5. For rules with arguments, promote each argument, add `public ?string $message = null` and remove any `prepare()` and
   `parent::__construct()` calls from the constructor.
6. Replace `sprintf()`-built messages with placeholders.
7. If your rule set `$this->lenient = true` in its constructor, declare `protected bool $lenient = true;` on the class
   instead.

For example, this v2 rule:

```php
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class After extends BaseRule implements PreparesValue
{
    use PreparesDate;

    public function __construct(
        protected string|\DateTimeInterface $date,
        ?string $message = null,
    ) {
        $this->date = $this->prepare($date);

        parent::__construct($message);
    }

    public function passes(): bool
    {
        return $this->value > $this->date;
    }

    public function message(): string
    {
        return sprintf('The %s field must be after %s.', $this->field, $this->format($this->date));
    }
}
```

becomes:

```php
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class After extends Rule
{
    use PreparesDate;

    public function __construct(
        protected string|\DateTimeInterface $date,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        return $value > $this->date;
    }

    public function message(): string
    {
        return 'The :field field must be after :date.';
    }
}
```

## [v2.0.0] - 2026-06-26

### Added

- New validation rules:
    - Alpha
    - AlphaNum
    - Boolean
    - Integer
    - Numeric
    - Regex
    - Url
- Support for writing custom rules by extending `BaseRule`
- A `RuleException` is now thrown when a custom rule matches the name of a built-in rule
- A `PreparesValue` contract and `PreparesDate` trait, allowing a rule to transform a value before it is validated (used
  by the built-in date rules to cast date strings to `DateTimeInterface`)

### Changed

- Renamed the abstract `Rule` rule class to `BaseRule`; the validation contract now lives at `Contracts\Rule`. If you
  extended the old `Rules\Rule` class directly, update it to extend `Rules\BaseRule` (note that the previous
  `getMessage()` method has also been removed).
- Replaced the `ValidatesDates` and `ValidatesDatesOrNormalStrings` contracts with the single `PreparesValue` contract

### Fixed

- Uninitialised typed properties are now validated as `null` instead of being skipped, so rules such as `Required`
  correctly fail for them

## [v1.0.1] - 2025-06-26

### Fixed

- Fixed incorrect `passes()` method call in main validation rule handler

## [v1.0.0] - 2025-04-19

### Added

#### Initial release! 🚀

- Core functionality implemented
- Project structure established
- Added validation rules:
    - Required
    - Email
    - In
    - Equal
    - Between
    - GreaterThan
    - GreaterOrEqual
    - LessThan
    - LessOrEqual
    - MinLength
    - MaxLength
- Added custom validation message support
- Added validation error reporting and handling

---

## Format

This changelog follows the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) format.  
