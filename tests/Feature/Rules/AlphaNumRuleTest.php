<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\AlphaNum;
use Mewtonium\Vanguard\Vanguard;

function alphaNumForm(string $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[AlphaNum]
            protected string $username,
        ) {
            //
        }
    };
}

test('the rule passes for letters and numbers', function (string $value): void {
    $form = alphaNumForm($value);
    $form->validate();

    expect($form->invalid())->toBeFalse();
})->with(['Vanguard', 'user123', '12345']);

test('the rule fails when other characters are present', function (string $value): void {
    $form = alphaNumForm($value);
    $form->validate();

    expect($form->errors()->first('username'))->toBe('The username field must only contain letters and numbers.');
})->with(['user_123', 'has space', '', 'a@b']);
