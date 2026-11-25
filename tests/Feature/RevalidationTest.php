<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Email;
use Mewtonium\Vanguard\Rules\Required;
use Mewtonium\Vanguard\Vanguard;

test('validating again clears errors that have since been fixed', function (): void {
    $form = new class () {
        use Vanguard;

        #[Email]
        public string $email = 'joe.bloggs';
    };

    $form->validate();

    expect($form->invalid())->toBeTrue();

    $form->email = 'joe@example.com';
    $form->validate();

    expect($form->invalid())->toBeFalse();
    expect($form->errors()->all())->toBe([]);
});

test('validating again only reports errors from the latest run', function (): void {
    $form = new class () {
        use Vanguard;

        #[Required]
        public string $name = '';

        #[Email]
        public string $email = 'joe@example.com';
    };

    $form->validate();

    $form->name = 'Joe';
    $form->email = 'joe.bloggs';
    $form->validate();

    expect($form->errors()->has('name'))->toBeFalse();
    expect($form->errors()->first('email'))->toBe('The email field must be a valid email.');
});

test('an error bag from an earlier run is left untouched', function (): void {
    $form = new class () {
        use Vanguard;

        #[Email]
        public string $email = 'joe.bloggs';
    };

    $form->validate();
    $previous = $form->errors();

    $form->email = 'joe@example.com';
    $form->validate();

    expect($previous->has('email'))->toBeTrue();
    expect($form->errors())->not->toBe($previous);
});
