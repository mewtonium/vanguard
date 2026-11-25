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

function strictIntegerForm(mixed $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[Integer(strict: true)]
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
})->with([10, '42', '-5', '+10']);

test('the rule fails for non-integers', function (mixed $value): void {
    $form = integerForm($value);
    $form->validate();

    expect($form->errors()->first('count'))->toBe('The count field must be an integer.');
})->with([1.5, 10.0, '3.14', '10.0', '1e3', '010', '0x1A', 'abc', true]);

test('the strict rule passes for integers', function (): void {
    $form = strictIntegerForm(10);
    $form->validate();

    expect($form->invalid())->toBeFalse();
});

test('the strict rule fails for integer strings', function (mixed $value): void {
    $form = strictIntegerForm($value);
    $form->validate();

    expect($form->errors()->first('count'))->toBe('The count field must be an integer.');
})->with(['42', 10.0, true]);
