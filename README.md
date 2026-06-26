# Vanguard - PHP Attribute-Based Validation Library

> [!CAUTION]
> 🛑 This package is intended for demo purposes only and is **<u>not</u>** suitable for production use. Please do not use it in live or production environments. 🛑

Vanguard is a simple, attribute-based validation library for PHP, providing a clean and easy way to validate data using attributes on class properties.

## Features

- Simple attribute-based validation.  
- Dependency-free - no external libraries required.  
- Supports common validation rules, with more to be added in future.  
  - **Comparison and range-based rules** (`Between`, `GreaterOrEqual`, `GreaterThan`, `LessOrEqual`, `LessThan`) also validate strings as dates when applicable.  
  - `Equal` can check strings as either a date string, or a standard value.  
  - **Scalar rules** such as `Url`, `Numeric`, `Integer`, `Boolean`, `Alpha`, `AlphaNum` and `Regex`.  
- **Write your own rules** by implementing a simple contract — see [Writing Custom Rules](#writing-custom-rules).  
- Supports for a custom error message on any rule.

## Installation

```bash
composer require mewtonium/vanguard
```

## Usage

Add the `Vanguard` trait to any class, add a few `Rule` attributes to properties and call `validate()` - simple!

```php
<?php

use App\Validation\Rules\Uppercase;
use Mewtonium\Vanguard\Rules\In;
use Mewtonium\Vanguard\Rules\MaxLength;
use Mewtonium\Vanguard\Rules\MinLength;
use Mewtonium\Vanguard\Rules\Email;
use Mewtonium\Vanguard\Rules\Between;
use Mewtonium\Vanguard\Rules\Required;
use Mewtonium\Vanguard\Vanguard;

class AccountSignupForm
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
        
        #[Required, In(['GB', 'FR', 'DE', 'ES', 'IT', 'IE', 'JP', 'ZH']), Uppercase]
        protected string $country,
        
        #[Required, GreaterOrEqual('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}

$data = [
    'firstName' => 'Joe',
    'lastName' => '',
    'age' => 17,
    'email' => 'joe.bloggs',
    'country' => 'ch',
    'date' => '2024-01-01',
];

$form = new AccountSignupForm(...$data);
$form->validate();
```

## Validation Rules

> **Note:** Vanguard performs <u>validation only</u>. It does not sanitise or modify your input data in any way — ensure you handle sanitisation separately, if needed.

Here are all the currently supported validation rules:

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

Validates that the value contains letters only.

```php
use Mewtonium\Vanguard\Rules\Alpha;

#[Alpha]
public string $name;
```

---

### `AlphaNum`

Validates that the value contains letters and numbers only.

```php
use Mewtonium\Vanguard\Rules\AlphaNum;

#[AlphaNum]
public string $username;
```

---

### `Between`

Validates whether the value lies within the inclusive range of `min` and `max`.

```php
use Mewtonium\Vanguard\Rules\Between;

#[Between(1, 100)]
public int $numbers;

#[Between('2020-01-01', '2030-01-01')] 
public string $date;
```

---

### `Boolean`

Validates that the value is a boolean. Accepts `true`, `false`, `1`, `0`, `'1'` and `'0'`.

```php
use Mewtonium\Vanguard\Rules\Boolean;

#[Boolean]
public mixed $active;
```

---

### `Email`

Validates that the value is a valid email address using `filter_var`.

```php
use Mewtonium\Vanguard\Rules\Email;

#[Email]
public string $email;
```

---

### `Equal`

Validates if the value is equal to the given value. If both are strings and resemble date strings, it compares them as dates.

```php
use Mewtonium\Vanguard\Rules\Equal;

#[Equal(30)]
public int $age;

#[Equal('2025-01-01')]
public string $date;
```

---

### `GreaterOrEqual`

Validates if the value is greater than or equal to the given value.

```php
use Mewtonium\Vanguard\Rules\GreaterOrEqual;

#[GreaterOrEqual(30)]
public int $age;

#[GreaterOrEqual('2025-01-01')] 
public string $date;
```

---

### `GreaterThan`

Validates if the value is strictly greater than the given value.

```php
use Mewtonium\Vanguard\Rules\GreaterThan;

#[GreaterThan(30)]
public int $age;

#[GreaterThan('2025-01-01')] 
public string $date;
```

---

### `In`

Validate that the value exists within a predefined list of values.

```php
use Mewtonium\Vanguard\Rules\In;

#[In(['GB', 'FR', 'DE'])]
public string $country;
```

---

### `Integer`

Validates that the value is an integer (or an integer-like string). Booleans are rejected.

```php
use Mewtonium\Vanguard\Rules\Integer;

#[Integer]
public mixed $count;
```

---

### `LessOrEqual`

Checks if the value is less than or equal to the given value.

```php
use Mewtonium\Vanguard\Rules\LessOrEqual;

#[LessOrEqual(30)]
public int $age;

#[LessOrEqual('2030-01-01')] 
public string $date;
```

---

### `LessThan`

Validates if the value is strictly less than the given value.

```php
use Mewtonium\Vanguard\Rules\LessThan;

#[LessThan(30)]
public int $age;

#[LessThan('2030-01-01')] 
public string $date;
```

---

### `MaxLength`

Validates that a string or array has no more than the given number of characters or items.

```php
use Mewtonium\Vanguard\Rules\MaxLength;

#[MaxLength(255)]
public string $url;

#[MaxLength(10)]
public array $data;
```

---

### `MinLength`

Validates that a string or array has at least the given number of characters or items.

```php
use Mewtonium\Vanguard\Rules\MinLength;

#[MinLength(3)]
public string $name;

#[MinLength(3)]
public array $data;
```

---

### `Numeric`

Validates that the value is numeric (an integer, float, or numeric string).

```php
use Mewtonium\Vanguard\Rules\Numeric;

#[Numeric]
public mixed $amount;
```

---

### `Regex`

Validates that the value matches a given PCRE pattern.

> **Note:** The pattern is supplied by you in the form definition — never derive it from end-user input. Authoring a
> pattern vulnerable to catastrophic backtracking (ReDoS) is your responsibility.

```php
use Mewtonium\Vanguard\Rules\Regex;

#[Regex('/^[A-Z]{2}-\d{3}$/')]
public string $reference;
```

---

### `Required`

Validate that the value is not null, an empty string, or an empty array.

```php
use Mewtonium\Vanguard\Rules\Required;

#[Required]
public string $name;
```

---

### `Url`

Validates that the value is a valid URL using `filter_var`.

```php
use Mewtonium\Vanguard\Rules\Url;

#[Url]
public string $website;
```

## Writing Custom Rules

Write your own rule by **extending the abstract `BaseRule` class** and adding the
`#[\Attribute(\Attribute::TARGET_PROPERTY)]` attribute. `BaseRule` gives you the injected `$field`/`$value`
properties and the custom-message plumbing — you only implement `passes()` and the default `message()`:

```php
use Mewtonium\Vanguard\Rules\BaseRule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Uppercase extends BaseRule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        return is_string($this->value) && mb_strtoupper($this->value) === $this->value;
    }

    public function message(): string
    {
        return "The {$this->field} field must be uppercase.";
    }
}
```

```php
#[Uppercase]
public string $code;

#[Uppercase(message: 'Shout it!')]
public string $shout;
```

`BaseRule` implements the `Mewtonium\Vanguard\Contracts\Rule` contract (`passes()` + `message()`); a non-null
`message` passed to the rule takes priority over the default returned by `message()`.

Here's another example — a rule that uses a regular expression to reject values with leading or trailing whitespace:

```php
use Mewtonium\Vanguard\Rules\BaseRule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Trimmed extends BaseRule
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    public function passes(): bool
    {
        return is_string($this->value) && preg_match('/^\s|\s$/', $this->value) === 0;
    }

    public function message(): string
    {
        return "The {$this->field} field must not have leading or trailing whitespace.";
    }
}
```

### Preparing values before validation

A rule may implement the `Mewtonium\Vanguard\Contracts\PreparesValue` contract to transform a value **before**
`passes()` runs. Vanguard calls `prepare(mixed $value): mixed` and validates the returned value, so `$this->value`
inside `passes()` is the prepared value. This is how the built-in date rules cast `Y-m-d` strings to
`DateTimeInterface`.

A date-based rule can reuse the `Mewtonium\Vanguard\Concerns\PreparesDate` trait, which implements `prepare()` for
you. The property's own value is prepared automatically; prepare any date arguments you accept in the constructor,
then compare them in `passes()`:

```php
use Mewtonium\Vanguard\Concerns\PreparesDate;
use Mewtonium\Vanguard\Contracts\PreparesValue;
use Mewtonium\Vanguard\Rules\BaseRule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class FutureDate extends BaseRule implements PreparesValue
{
    use PreparesDate;

    public function __construct(
        protected string|\DateTimeInterface $after,
        ?string $message = null,
    ) {
        $this->after = $this->prepare($after);

        parent::__construct($message);
    }

    public function passes(): bool
    {
        return $this->value instanceof \DateTimeInterface
            && $this->value > $this->after;
    }

    public function message(): string
    {
        return sprintf(
            'The %s field must be a date after %s.',
            $this->field,
            $this->format($this->after),
        );
    }
}
```

```php
#[FutureDate('2025-01-01')]
public string $startsAt;
```

### Strict vs. lenient date parsing

By default the `PreparesDate` trait is **strict**: a string that isn't a valid date throws a `\DateException`. Set `$lenient = true`
when your rule should accept plain, non-date strings and date strings. An unrecognised string is then left untouched instead of
throwing an exception.

Which one you want depends on what the rule does. For example:

- **`Equal` is lenient.** It checks equality, which is just as valid for a plain string (`'active'`) as for a date
  (`'2025-01-01'`), so non-date strings have to be allowed through.
- **`GreaterThan`, `LessThan`, `Between` (and the like) are strict.** "Greater than" only makes sense for numbers
  and dates, so a non-date string is treated as a mistake and rejected.

If your rule needs lenient mode, set it in the constructor before preparing any values:

```php
public function __construct(/* ... */)
{
    $this->lenient = true;

    // ...
}
```

## Custom Validation Message

All validation rules support a custom error message. You can specify a custom message by passing the `message` argument:

```php
#[Required(message: 'Please enter your first name.')]
#[MinLength(2, message: 'Your last name must be at least 2 characters long.')]
```

## Handling Validation Errors

When validation fails, errors are stored in an `ErrorBag` instance:

```php
if ($form->invalid()) {
    $errors = $form->errors(); // Returns an instance of `ErrorBag`

    // ...
}
```

Available methods:

```php
$errors->all();         // Returns all validation errors
$errors->get($field);   // Returns all errors for a specific field
$errors->first($field); // Returns the first error for a specific field
$errors->has($field);   // Checks if a field has any errors
$errors->count();       // Total number of validation errors
$errors->flush();       // Clears the bag
```

### Example Output

For the example in the "Usage" section, calling `$errors->all()` would return:

```php
[
    'lastName' => [
        'Required' => 'Please provide your last name.',
        'MinLength' => 'The lastName field must be a minimum of 2 characters long.',
    ],
    'age' => [
        'Between' => 'The age field must be between 18 and 99.',
    ],
    'email' => [
        'Email' => 'The email field must be a valid email.',
    ],
    'country' => [
        'In' => 'The country field does not have a valid selection.',
        'Uppercase' => 'The country field must be uppercase.',
    ],
    'date' => [
        'GreaterOrEqual' => 'The date field must be greater than or equal to 2025-01-01.',
    ],
]
```

## Changelog

See the full changelog [here](CHANGELOG.md).

## License

This project is open-source and available under the [MIT license](LICENSE).
