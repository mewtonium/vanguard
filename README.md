# Vanguard - PHP Attribute-Based Validation Library

> [!CAUTION]
> 🛑 This package is intended for demo purposes only and is **<u>not</u>** suitable for production use. Please do not use it in live or production environments. 🛑

Vanguard is a simple, attribute-based validation library for PHP, providing a clean and easy way to validate data using attributes on class properties.

## Features

- Simple attribute-based validation.
- Support for common validation rules with more to be added in future. Available rules so far:
  - `Between(from<int|float>, to<int|float>)` - Boundaries are included by default; disable with `inclusive: false` - e.g. `Between(10, 100, inclusive: false)`
  - `Email`
  - `Equal(int|float|string)` - Checks equality for string length or numeric value
  - `GreaterOrEqual(int|float)`
  - `GreaterThan(int|float)`
  - `In(array)`
  - `LessOrEqual(int|float)`
  - `LessThan(int|float)`
  - `Max(int)` - Checks max length of a string or array
  - `Min(int)` - Checks min length of a string or array
  - `Required`

## Installation

> **Note:** This package is not yet published on Packagist, so the command below will not work.

```bash
composer require mewtonium/vanguard
```

## Usage

Add the `Vanguard` trait to any class, add a few `Rule` attributes to properties and call `validate()` - simple!

```php
<?php

use Mewtonium\Vanguard\Rules\In;
use Mewtonium\Vanguard\Rules\Max;
use Mewtonium\Vanguard\Rules\Min;
use Mewtonium\Vanguard\Rules\Email;
use Mewtonium\Vanguard\Rules\Between;
use Mewtonium\Vanguard\Rules\Required;
use Mewtonium\Vanguard\Vanguard;

class AccountSignupForm
{
    use Vanguard;

    public function __construct(
        #[Required, Min(2), Max(255)]
        protected string $firstName,
        
        #[Required(message: 'Please provide your last name.'), Min(2), Max(255)]
        protected string $lastName,

        #[Required, Between(18, 99)]
        protected int $age,

        #[Required, Email]
        protected string $email,

        #[Required, In(['GB', 'FR', 'DE', 'ES', 'IT', 'IE', 'JP', 'ZH'])]
        protected string $country,
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
];

$form = new AccountSignupForm(...$data);
$form->validate();

if ($form->invalid()) {
    $errors = $form->errors(); // Returns an instance of `ErrorBag`

    /**
     * ErrorBag methods:
     * 
     * $errors->all() // returns an list of all errors
     * $errors->get($field) // fetches errors by field
     * $errors->first($field) // finds the first error by field
     * $errors->add($field, $file, $message) // adds an error to the bag
     * $errors->has($field) // checks if an error exists in the bag by field
     * $errors->count() // returns count of all errors
     * $errors->flush() // removes all errors from the bag
     *
     * Calling $errors->all() for the above data will return:
     * 
     * [
     *     'lastName' => [
     *         'Required' => 'Please provide your first name.',
     *         'Min' => 'The lastName field must be a minimum of 2 characters long.',
     *     ],
     *     'age' => [
     *         'Between' => 'The age field must be between 18 and 99.',
     *     ],
     *     'email' => [
     *         'Email' => 'The email field must be a valid email.',
     *     ],
     *     'country' => [
     *         'In' => 'The country field does not have a valid selection.',
     *     ]
     * ]
     */
}
```


