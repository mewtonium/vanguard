<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\Between;
use Mewtonium\Vanguard\Tests\Fixtures\Forms\BetweenRuleForm;
use Mewtonium\Vanguard\Vanguard;

test('the rule passes validation', function (): void {
    $form = new BetweenRuleForm(
        num1: 5,
        num2: 5,
        date: '2025-03-27',
    );

    $form->validate();

    expect($form->invalid())->toBeFalse();
});

test('the rule fails validation', function (): void {
    $form = new BetweenRuleForm(
        num1: 100,
        num2: 5,
        date: '2027-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num1'))->toBe('The num1 field must be between 1 and 10.');
    expect(array_key_exists('Between', $form->errors()->get('num1')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe('The date field must be between 2025-01-01 00:00:00 and 2026-01-01 00:00:00.');
    expect(array_key_exists('Between', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new BetweenRuleForm(
        num1: 5,
        num2: 100,
        date: '2025-03-27',
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('You must pick a number between 1 and 10');
});

test('an exception is thrown if the min date is greater than the max date or vice versa', function (): void {
    $form = new class (date: '2025-01-01') {
        use Vanguard;

        public function __construct(
            #[Between(min: '2030-01-01', max: '2026-01-01')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'Date range set on the [Between] rule is invalid.',
        );
});

test('an exception is thrown if the min value is greater than the max value or vice versa', function (): void {
    $form = new class (number: 50) {
        use Vanguard;

        public function __construct(
            #[Between(min: 100, max: 1)]
            protected int $number,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'Number range set on the [Between] rule is invalid.',
        );
});
