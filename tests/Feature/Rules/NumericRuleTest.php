<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Numeric;
use Mewtonium\Vanguard\Vanguard;

function numericForm(mixed $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[Numeric]
            protected mixed $amount,
        ) {
            //
        }
    };
}

test('the rule passes for numeric values', function (mixed $value): void {
    $form = numericForm($value);
    $form->validate();

    expect($form->invalid())->toBeFalse();
})->with([10, 1.5, '42', '3.14']);

test('the rule fails for non-numeric values', function (mixed $value): void {
    $form = numericForm($value);
    $form->validate();

    expect($form->errors()->first('amount'))->toBe('The amount field must be numeric.');
})->with(['abc', '', true]);
