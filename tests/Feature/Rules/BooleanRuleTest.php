<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Boolean;
use Mewtonium\Vanguard\Vanguard;

function booleanForm(mixed $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[Boolean]
            protected mixed $active,
        ) {
            //
        }
    };
}

test('the rule passes for accepted boolean representations', function (mixed $value): void {
    $form = booleanForm($value);
    $form->validate();

    expect($form->invalid())->toBeFalse();
})->with([true, false, 1, 0, '1', '0']);

test('the rule fails for non-boolean values', function (mixed $value): void {
    $form = booleanForm($value);
    $form->validate();

    expect($form->errors()->first('active'))->toBe('The active field must be a boolean.');
})->with(['yes', 'true', 2, 'on']);
