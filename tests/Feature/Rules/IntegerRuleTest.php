<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Integer;
use Mewtonium\Vanguard\Vanguard;

function integerForm(mixed $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[Integer]
            protected mixed $count,
        ) {
            //
        }
    };
}

test('the rule passes for integers', function (mixed $value): void {
    $form = integerForm($value);
    $form->validate();

    expect($form->invalid())->toBeFalse();
})->with([10, '42', '-5']);

test('the rule fails for non-integers', function (mixed $value): void {
    $form = integerForm($value);
    $form->validate();

    expect($form->errors()->first('count'))->toBe('The count field must be an integer.');
})->with([1.5, '3.14', 'abc', true]);
