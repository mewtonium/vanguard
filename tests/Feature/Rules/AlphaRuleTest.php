<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Alpha;
use Mewtonium\Vanguard\Vanguard;

function alphaForm(string $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[Alpha]
            protected string $name,
        ) {
            //
        }
    };
}

test('the rule passes for letters only', function (): void {
    $form = alphaForm('Vanguard');
    $form->validate();

    expect($form->invalid())->toBeFalse();
});

test('the rule fails when non-letters are present', function (string $value): void {
    $form = alphaForm($value);
    $form->validate();

    expect($form->errors()->first('name'))->toBe('The name field must only contain letters.');
})->with(['Vanguard1', 'two words', '', 'a-b']);
