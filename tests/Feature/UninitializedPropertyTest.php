<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Required;
use Mewtonium\Vanguard\Vanguard;

test('a required rule fails for an uninitialised typed property', function (): void {
    $form = new class () {
        use Vanguard;

        #[Required]
        public string $name;
    };

    $form->validate();

    expect($form->invalid())->toBeTrue();
    expect($form->errors()->first('name'))->toBe('The name field is required.');
});

test('an initialised property still validates normally', function (): void {
    $form = new class () {
        use Vanguard;

        #[Required]
        public string $name = 'Mark';
    };

    $form->validate();

    expect($form->invalid())->toBeFalse();
});
