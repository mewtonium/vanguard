# Vanguard - PHP Attribute-Based Validation Library

> [!CAUTION]
> 🛑 This package is intended for demo purposes only and is **<u>not</u>** suitable for production use. Please do not use it in live or production environments. 🛑

Vanguard is a simple, attribute-based validation library for PHP, providing a clean and easy way to validate data using attributes on class properties.

## Features

- Simple attribute-based validation.  
- Support for common validation rules, with more to be added in the future.  
  - **Comparison and range-based rules** (`Between`, `GreaterOrEqual`, `GreaterThan`, `LessOrEqual`, `LessThan`) also validate strings as dates when applicable.  
  - `Equal` can check strings as either a date string or a standard value. 

Available rules so far:  
  - `Between(min<int|float|string|DateTimeInterface>, max<int|float|string|DateTimeInterface>)` - Upper and lower bounds inclusive.  
  - `Email`  
  - `Equal(value<int|float|string|DateTimeInterface>)`  
  - `GreaterOrEqual(value<int|float|string|DateTimeInterface>)`  
  - `GreaterThan(value<int|float|string|DateTimeInterface>)`  
  - `In(values<array>)`  
  - `LessOrEqual(value<int|float|string|DateTimeInterface>)`  
  - `LessThan(value<int|float|string|DateTimeInterface>)`  
  - `MaxLength(value<int>)` - Checks max length of a string or array.  
  - `MinLength(value<int>)` - Checks min length of a string or array.  
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
     *         'MinLength' => 'The lastName field must be a minimum of 2 characters long.',
     *     ],
     *     'age' => [
     *         'Between' => 'The age field must be between 18 and 99.',
     *     ],
     *     'email' => [
     *         'Email' => 'The email field must be a valid email.',
     *     ],
     *     'country' => [
     *         'In' => 'The country field does not have a valid selection.',
     *     ],
     *     'date' => [
     *         'GreaterOrEqual' => 'The date field must be greater than or equal to 2025-01-01.',
     *     ]
     * ]
     */
}
```

## Changelog

See the full changelog [here](CHANGELOG.md).
