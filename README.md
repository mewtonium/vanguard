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
- Supports for a custom error message on any rule.

## Installation

```bash
composer require mewtonium/vanguard
```

## Usage

Add the `Vanguard` trait to any class, add a few `Rule` attributes to properties and call `validate()` - simple!

```php
<?php

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

        #[Required, In(['GB', 'FR', 'DE', 'ES', 'IT', 'IE', 'JP', 'ZH'])]
        protected string $country,

        #[Required, GreaterOrEqual('2025-01-01')]
        protected string $date;
    ) {
        //
    }
}

$data = [
    'firstName' => 'Joe',
    'lastName' => '',
    'age' => 17,
    'email' => 'joe.bloggs',
    'country' => 'CH',
    'date' => '2024-01-01',
];

$form = new AccountSignupForm(...$data);
$form->validate();
```

## Validation Rules

> **Note:** Vanguard performs <u>validation only</u>. It does not sanitise or modify your input data in any way — ensure you handle sanitisation separately, if needed.

Here are all the currently supported validation rules:

### `Required`

Validate that the value is not null, an empty string, or an empty array.

```php
use Mewtonium\Vanguard\Rules\Required;

#[Required]
public string $name;
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

### `In`

Validate that the value exists within a predefined list of values.

```php
use Mewtonium\Vanguard\Rules\In;

#[In(['GB', 'FR', 'DE'])]
public string $country;
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

### `MaxLength`

Validates that a string or array has no more than the given number of characters or items.

```php
use Mewtonium\Vanguard\Rules\MaxLength;

#[MaxLength(255)]
public string $url;

#[MaxLength(10)]
public array $data;
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
    ],
    'date' => [
        'GreaterOrEqual' => 'The date field must be greater than or equal to 2025-01-01.',
    ]
]
```

## Changelog

See the full changelog [here](CHANGELOG.md).

## License

This project is open-source and available under the [MIT license](LICENSE).
