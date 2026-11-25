# Vanguard - PHP Attribute-Based Validation Library

> [!CAUTION]
> 🛑 This package is intended for demo purposes only and is **<u>not</u>** suitable for production use. Please do not use it in live or production environments. 🛑

Vanguard is a simple, dependency-free, attribute-based validation library for PHP, providing a clean and easy way to
validate data using attributes on class properties.

```php
final class SignupForm
{
    use Vanguard;

    public function __construct(
        #[Required, Email]
        protected string $email,

        #[Required, Between(18, 99)]
        protected int $age,
    ) {
        //
    }
}
```

## Features

- Simple attribute-based validation.
- Dependency-free - no external libraries required.
- Supports common validation rules, with more to be added in future.
    - **Comparison and range-based rules** (`Between`, `GreaterOrEqual`, `GreaterThan`, `LessOrEqual`, `LessThan`) also
      validate strings as dates when applicable.
    - `Equal` can check strings as either a date string, or a standard value.
    - **Scalar rules** such as `Url`, `Numeric`, `Integer`, `Boolean`, `Alpha`, `AlphaNum` and `Regex`.
- **Write your own rules** by extending the `Rule` class or using our helpful command to generate one - see [Writing custom rules](#writing-custom-rules).
- Support for custom error messages on any rule, with placeholders and plurals.

## Contents

- [Installation](#installation)
- [Quick start](#quick-start)
- [Available rules](#available-rules)
- [Working with dates](#working-with-dates)
- [Error messages](#error-messages)
- [Handling errors](#handling-errors)
- [Writing custom rules](#writing-custom-rules)
- [Exceptions](#exceptions)
- [Upgrading from v2](#upgrading-from-v2)

## Installation

Vanguard requires PHP 8.4 or later.

```bash
composer require mewtonium/vanguard
```

## Quick start

Add the `Mewtonium\Vanguard\Vanguard` trait to a class, put rule attributes on its properties, then call `validate()`:

```php
use Mewtonium\Vanguard\Rules\Between;
use Mewtonium\Vanguard\Rules\Email;
use Mewtonium\Vanguard\Rules\GreaterOrEqual;
use Mewtonium\Vanguard\Rules\In;
use Mewtonium\Vanguard\Rules\MaxLength;
use Mewtonium\Vanguard\Rules\MinLength;
use Mewtonium\Vanguard\Rules\Required;
use Mewtonium\Vanguard\Vanguard;

final class AccountSignupForm
{
    use Vanguard;

    public function __construct(
        #[Required, MinLength(2), MaxLength(255)]
        protected string $firstName,

        #[Required(message: 'Please provide your last name.'), MinLength(2), MaxLength(255)]
        protected string $lastName,

        #[Required, Between(18, 99)]
        protected int $age,

        #[Required, Email]
        protected string $email,

        #[Required, In(['GB', 'FR', 'DE', 'ES', 'IT', 'IE', 'JP', 'ZH'])]
        protected string $country,

        #[Required, GreaterOrEqual('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}

$form = new AccountSignupForm(
    firstName: 'Joe',
    lastName: '',
    age: 17,
    email: 'joe.bloggs',
    country: 'CH',
    date: '2024-01-01',
);

$form->validate();

if ($form->invalid()) {
    $errors = $form->errors()->all();
}
```

`$errors` holds each failed rule's message, grouped by field and keyed by rule name:

```php
[
    'lastName' => [
        'Required' => 'Please provide your last name.',
        'MinLength' => 'The lastName field must be at least 2 characters.',
    ],
    'age' => [
        'Between' => 'The age field must be between 18 and 99.',
    ],
    'email' => [
        'Email' => 'The email field must be a valid email.',
    ],
    'country' => [
        'In' => 'The country field does not have a valid selection.',
    ],
    'date' => [
        'GreaterOrEqual' => 'The date field must be greater than or equal to 2025-01-01 00:00:00.',
    ],
]
```

> [!IMPORTANT]
> Vanguard performs <u>validation only</u>. It does not sanitise or modify your input data in any way. Rules may convert values internally - e.g. date strings to date objects - but your input data is left unchanged. Ensure you handle sanitisation separately.

## Available rules

All built-in rules live in the `Mewtonium\Vanguard\Rules` namespace. Every rule also accepts a `message` argument -
see [Custom messages](#custom-messages).

- [Alpha](#alpha)
- [AlphaNum](#alphanum)
- [Between](#between)
- [Boolean](#boolean)
- [Email](#email)
- [Equal](#equal)
- [GreaterOrEqual](#greaterorequal)
- [GreaterThan](#greaterthan)
- [In](#in)
- [Integer](#integer)
- [LessOrEqual](#lessorequal)
- [LessThan](#lessthan)
- [MaxLength](#maxlength)
- [MinLength](#minlength)
- [Numeric](#numeric)
- [Regex](#regex)
- [Required](#required)
- [Url](#url)

### `Alpha`

Validates that the value is a string of ASCII letters only.

```php
use Mewtonium\Vanguard\Rules\Alpha;

#[Alpha]
protected string $name;
```

---

### `AlphaNum`

Validates that the value is a string of ASCII letters and digits only.

```php
use Mewtonium\Vanguard\Rules\AlphaNum;

#[AlphaNum]
protected string $username;
```

---

### `Between`

Validates that the value is between `min` and `max`, inclusive. Works with numbers and [dates](#working-with-dates).

```php
use Mewtonium\Vanguard\Rules\Between;

#[Between(1, 100)]
protected int $quantity;

#[Between('2020-01-01', '2030-01-01')]
protected string $date;
```

---

### `Boolean`

Validates that the value is a boolean. Accepts `true`, `false`, `1`, `0`, `'1'` and `'0'`. With `strict: true`, only
`true` and `false` pass.

```php
use Mewtonium\Vanguard\Rules\Boolean;

#[Boolean]
protected mixed $active;

#[Boolean(strict: true)]
protected bool $subscribed;
```

---

### `Email`

Validates that the value is a valid email address using `FILTER_VALIDATE_EMAIL`.

```php
use Mewtonium\Vanguard\Rules\Email;

#[Email]
protected string $email;
```

---

### `Equal`

Validates that the value is identical (`===`) to `to`. If both are [date strings](#working-with-dates), they are
compared as dates.

```php
use Mewtonium\Vanguard\Rules\Equal;

#[Equal(30)]
protected int $age;

#[Equal('2025-01-01')]
protected string $date;
```

---

### `GreaterOrEqual`

Validates that the value is greater than or equal to `min`. Works with numbers and [dates](#working-with-dates).

```php
use Mewtonium\Vanguard\Rules\GreaterOrEqual;

#[GreaterOrEqual(30)]
protected int $age;

#[GreaterOrEqual('2025-01-01')]
protected string $date;
```

---

### `GreaterThan`

Validates that the value is strictly greater than `min`. Works with numbers and [dates](#working-with-dates).

```php
use Mewtonium\Vanguard\Rules\GreaterThan;

#[GreaterThan(30)]
protected int $age;

#[GreaterThan('2025-01-01')]
protected string $date;
```

---

### `In`

Validates that the value is one of `values`. The comparison is loose, so `'1'` matches `1`.

```php
use Mewtonium\Vanguard\Rules\In;

#[In(['GB', 'FR', 'DE'])]
protected string $country;
```

---

### `Integer`

Validates that the value is an integer or an integer string such as `'42'`. Booleans, floats and strings like `'4.0'`
fail. With `strict: true`, only integers pass.

```php
use Mewtonium\Vanguard\Rules\Integer;

#[Integer]
protected mixed $count;

#[Integer(strict: true)]
protected mixed $quantity;
```

---

### `LessOrEqual`

Validates that the value is less than or equal to `max`. Works with numbers and [dates](#working-with-dates).

```php
use Mewtonium\Vanguard\Rules\LessOrEqual;

#[LessOrEqual(30)]
protected int $age;

#[LessOrEqual('2030-01-01')]
protected string $date;
```

---

### `LessThan`

Validates that the value is strictly less than `max`. Works with numbers and [dates](#working-with-dates).

```php
use Mewtonium\Vanguard\Rules\LessThan;

#[LessThan(30)]
protected int $age;

#[LessThan('2030-01-01')]
protected string $date;
```

---

### `MaxLength`

Validates that a string has at most `length` characters (multibyte-aware), or an array has at most `length` items.

```php
use Mewtonium\Vanguard\Rules\MaxLength;

#[MaxLength(255)]
protected string $url;

#[MaxLength(10)]
protected array $tags;
```

---

### `MinLength`

Validates that a string has at least `length` characters (multibyte-aware), or an array has at least `length` items.

```php
use Mewtonium\Vanguard\Rules\MinLength;

#[MinLength(3)]
protected string $name;

#[MinLength(1)]
protected array $tags;
```

---

### `Numeric`

Validates that the value is an integer, a float or a numeric string. With `strict: true`, only integers and floats
pass.

```php
use Mewtonium\Vanguard\Rules\Numeric;

#[Numeric]
protected mixed $amount;

#[Numeric(strict: true)]
protected mixed $price;
```

---

### `Regex`

Validates that the value is a string matching the given regular expression `pattern`.

> [!WARNING]
> Write regex patterns yourself and never build them from user input. Vanguard doesn't check patterns for catastrophic backtracking (ReDoS), so make sure yours aren't vulnerable to it.

```php
use Mewtonium\Vanguard\Rules\Regex;

#[Regex('/^[A-Z]{2}-\d{3}$/')]
protected string $reference;
```

---

### `Required`

Validates that the value isn't `null`, an empty or whitespace-only string, or an empty array.

```php
use Mewtonium\Vanguard\Rules\Required;

#[Required]
protected string $name;
```

---

### `Url`

Validates that the value is a string holding a valid URL using `FILTER_VALIDATE_URL`.

```php
use Mewtonium\Vanguard\Rules\Url;

#[Url]
protected string $website;
```

---

### Default messages

Each rule uses this message when you don't pass your own `message`:

| Rule             | Default message                                                                                                                                        |
|------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| `Alpha`          | The :field field must only contain letters.                                                                                                            |
| `AlphaNum`       | The :field field must only contain letters and numbers.                                                                                                |
| `Between`        | The :field field must be between :min and :max.                                                                                                        |
| `Boolean`        | The :field field must be a boolean.                                                                                                                    |
| `Email`          | The :field field must be a valid email.                                                                                                                |
| `Equal`          | The :field field must be equal to :to.                                                                                                                 |
| `GreaterOrEqual` | The :field field must be greater than or equal to :min.                                                                                                |
| `GreaterThan`    | The :field field must be greater than :min.                                                                                                            |
| `In`             | The :field field does not have a valid selection.                                                                                                      |
| `Integer`        | The :field field must be an integer.                                                                                                                   |
| `LessOrEqual`    | The :field field must be less than or equal to :max.                                                                                                   |
| `LessThan`       | The :field field must be less than :max.                                                                                                               |
| `MaxLength`      | (string) The :field field must not exceed :length {character\|characters}.<br/>(array) The :field field must not have more than :length {item\|items}. |
| `MinLength`      | (string) The :field field must be at least :length {character\|characters}.<br/>(array) The :field field must have at least :length {item\|items}.     |
| `Numeric`        | The :field field must be numeric.                                                                                                                      |
| `Regex`          | The :field field is in an invalid format.                                                                                                              |
| `Required`       | The :field field is required.                                                                                                                          |
| `Url`            | The :field field must be a valid URL.                                                                                                                  |

## Working with dates

The comparison rules (`Equal`, `GreaterThan`, `GreaterOrEqual`, `LessThan`, `LessOrEqual` and `Between`) work with
dates as well as numbers. Before comparing, date strings in any of these formats become `DateTimeImmutable` objects,
both in the rule's arguments and in the value:

| Format        | Example               | Notes                       |
|---------------|-----------------------|-----------------------------|
| `Y-m-d`       | `2025-01-01`          | The time is set to midnight |
| `Y-m-d H:i`   | `2025-01-01 09:30`    | Seconds are set to `00`     |
| `Y-m-d H:i:s` | `2025-01-01 09:30:15` |                             |

Impossible dates such as `2024-02-31` are rejected rather than silently rolled over into March.

```php
#[GreaterOrEqual('2025-01-01')]
protected string $startsOn;

#[Between('2025-01-01 09:00', '2025-01-01 17:00')]
protected string $appointment;
```

Rules differ in how they handle strings that aren't dates:

- **`GreaterThan`, `GreaterOrEqual`, `LessThan`, `LessOrEqual` and `Between` are strict.** Any other string, whether
  an argument or the value, throws a `\DateException` instead of recording a validation error. If the value comes
  from user input, catch `\DateException` around `validate()`.
- **`Equal` is lenient.** Strings that aren't dates are compared as plain strings, so `#[Equal('active')]` works as
  expected.

Dates in failure messages are formatted as `Y-m-d H:i:s`.

## Error messages

### Custom messages

Pass `message` to any rule to replace its default message:

```php
#[Required(message: 'Please enter your first name.')]
#[MinLength(2, message: 'Your last name must be at least 2 characters long.')]
```

### Placeholders

Default and custom messages can both use placeholders:

| Placeholder   | Replaced with                                                                   |
|---------------|---------------------------------------------------------------------------------|
| `:field`      | The name of the property being validated                                        |
| `:<argument>` | The value of the rule's argument with that name, e.g. `:min`, `:max`, `:values` |

```php
#[Between(18, 99, message: 'You must be between :min and :max to sign up.')]
protected int $age;
// "You must be between 18 and 99 to sign up."

#[In(['GB', 'FR'], message: 'Choose one of: :values.')]
protected string $country;
// "Choose one of: GB, FR."
```

Argument values are formatted like this:

- dates as `Y-m-d H:i:s`
- arrays as a comma-separated list
- booleans as `true` or `false`
- enums by their backed value or case name

`Equal` also puts quotes around strings (`'active'`). A `:name` that doesn't match a placeholder is left as it is.

### Plurals

To choose a word based on a placeholder's value, put a `{singular|plural}` group straight after the placeholder. The
singular form is used when the value is `1`, or an array with one item. Anything else uses the plural form:

```php
#[MaxLength(3, message: 'You can add up to :length {tag|tags}.')]
protected array $tags;
// MaxLength(1) → "You can add up to 1 tag."
// MaxLength(3) → "You can add up to 3 tags."

#[In(['GB'], message: 'Only :values {is|are} supported.')]
protected string $country;
// "Only GB is supported."
```

A group only takes effect straight after a placeholder, though spaces between the two are fine. Braces anywhere else,
or without a `|` inside, are left as they are.

## Handling errors

After `validate()`, use `invalid()` to see whether anything failed, and `errors()` to get the `ErrorBag`:

```php
$form->validate();

if ($form->invalid()) {
    $form->errors()->first('email'); // 'The email field must be a valid email.'
}
```

| Method                          | Returns                                                       |
|---------------------------------|---------------------------------------------------------------|
| `all(): array`                  | Every error, as `[field => [RuleName => message]]`            |
| `get(string $field): ?array`    | A field's errors keyed by rule name, or `null` if it has none |
| `first(string $field): ?string` | A field's first error message, or `null` if it has none       |
| `has(string $field): bool`      | Whether the field has any errors                              |
| `count(): int`                  | The number of fields with errors                              |
| `flush(): void`                 | Removes all errors from the bag                               |

## Writing custom rules

### A basic rule

Extend `Mewtonium\Vanguard\Rules\Rule`, add the `#[\Attribute]` attribute, and implement `passes()` and `message()`:

```php
use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Uppercase extends Rule
{
    public function passes(mixed $value): bool
    {
        return is_string($value) && mb_strtoupper($value) === $value;
    }

    public function message(): string
    {
        return 'The :field field must be uppercase.';
    }
}
```

Use it like any built-in rule, custom `message` included:

```php
#[Required, Uppercase]
protected string $code;

#[Uppercase(message: 'Shout it!')]
protected string $shout;
```

### Generating a rule

Vanguard ships a small command that creates this skeleton for you:

```bash
vendor/bin/vanguard make:rule Uppercase
# Rule [App\Rules\Uppercase] created at [src/Rules/Uppercase.php].
```

Rules are created under `Rules` in your first PSR-4 namespace, unless the name already starts with one of your
namespaces. Use `/` to separate namespaces:

```bash
vendor/bin/vanguard make:rule Text/Slug
# Rule [App\Rules\Text\Slug] created at [src/Rules/Text/Slug.php].

vendor/bin/vanguard make:rule App/Validation/Uppercase
# Rule [App\Validation\Uppercase] created at [src/Validation/Uppercase.php].
```

For a rule that compares dates, add `--date` to include the
[`PreparesDate`](#preparing-values) trait, which converts date strings before `passes()` runs:

```bash
vendor/bin/vanguard make:rule Before --date
```

> [!NOTE]
> The command never overwrites an existing file, and it rejects names already used by built-in rules.

### Rules with arguments

Declare arguments as promoted constructor properties and include `public ?string $message = null` so the rule still accepts a custom message through the named `message:` argument. You don't need to call the parent constructor.

Each argument is available as a placeholder:

```php
use Mewtonium\Vanguard\Rules\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MultipleOf extends Rule
{
    public function __construct(
        protected int $factor,
        public ?string $message = null,
    ) {
        //
    }

    public function passes(mixed $value): bool
    {
        return is_int($value) && $value % $this->factor === 0;
    }

    public function message(): string
    {
        return 'The :field field must be a multiple of :factor.';
    }
}
```

```php
#[MultipleOf(5)]
protected int $quantity;
// "The quantity field must be a multiple of 5."
```

The value that was validated is available as `$this->value` inside `message()`, after `prepare()` has run. Use it to
return a different message depending on what was submitted. For example, the built-in `MinLength` rule reports
characters for strings and items for arrays:

```php
public function message(): string
{
    return is_array($this->value)
        ? 'The :field field must have at least :length {item|items}.'
        : 'The :field field must be at least :length {character|characters}.';
}
```

### Preparing values

Override `prepare()` to change a value before `passes()` checks it. It runs on the property's value and on each of
the rule's arguments (except `$message`), so you're always comparing like with like.

For dates, use the `Mewtonium\Vanguard\Concerns\PreparesDate` trait. It applies the same
[strict date conversion](#working-with-dates) as the built-in comparison rules:

```php
use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Rules\Rule;

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
        return $value instanceof \DateTimeInterface && $value > $this->date;
    }

    public function message(): string
    {
        return 'The :field field must be after :date.';
    }
}
```

```php
#[After('2025-01-01')]
protected string $startsAt;
// "The startsAt field must be after 2025-01-01 00:00:00."
```

#### Lenient preparation

When `prepare()` can't convert a value, it should throw an exception. By default, that exception stops validation.
Set `$lenient` to `true` on your rule and the original value is kept instead.

For example, `PreparesDate` throws a `DateException` for a string that isn't a valid date. `Equal` is lenient (as it can
also check equality for other data types), so it keeps the string as-is:

```php
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Same extends Rule
{
    use PreparesDate;

    protected bool $lenient = true;

    // ...
}
```

This works for any conversion, not just dates. For example, turning strings into an enum:

```php
#[\Override]
public function prepare(mixed $value): mixed
{
    return is_string($value) ? Status::from($value) : $value;
}
```

### Formatting arguments in messages

`formatValueForMessage()` turns each argument into text for its placeholder. Override it to change how values
appear, and call the parent for any you want formatted as normal. For example, `Equal` wraps strings in quotes:

```php
#[\Override]
protected function formatValueForMessage(mixed $value): string
{
    return is_string($value) ? "'{$value}'" : parent::formatValueForMessage($value);
}
```

## Exceptions

These exceptions mean a rule was set up or used incorrectly, or, for `\DateException`, given a value it can't convert.
They are thrown from `validate()` and are never recorded as validation errors.

<table>
    <thead>
        <tr>
            <th>Exception</th>
            <th>Thrown when</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td rowspan="4"><code>Mewtonium\Vanguard\Exceptions\RuleException</code></td>
            <td>A custom rule has the same short class name as a built-in rule</td>
        </tr>
        <tr>
            <td><code>Between</code> is given a <code>min</code> greater than its <code>max</code></td>
        </tr>
        <tr>
            <td><code>In</code> is given an empty <code>values</code> array</td>
        </tr>
        <tr>
            <td><code>Regex</code> is given an invalid pattern</td>
        </tr>
        <tr>
            <td><code>\DateException</code></td>
            <td>A strict date rule is given a string that isn't a date, as an argument or as the value</td>
        </tr>
    </tbody>
</table>

## Upgrading from v2

v3 is a breaking release that removes most of the boilerplate from writing rules. See the
[upgrade guide in the changelog](CHANGELOG.md#upgrading-from-v2) for step-by-step instructions and a before/after
example.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

Vanguard is open-source software licensed under the [MIT license](LICENSE).
