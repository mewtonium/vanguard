<?php

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\GreaterThan;
use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Tests\Fixtures\Forms\GreaterThanRuleForm;

test('the rule passes validation', function () {
    $form = new GreaterThanRuleForm(
        num1: 15,
        num2: 20,
        num3: 25,
        date: '2026-01-01',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new GreaterThanRuleForm(
        num1: 15,
        num2: 10,
        num3: 25,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be greater than 10.');
    expect(array_key_exists('GreaterThan', $form->errors()->get('num2')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe('The date field must be greater than 2025-01-01.');
    expect(array_key_exists('GreaterThan', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new GreaterThanRuleForm(
        num1: 15,
        num2: 20,
        num3: 10,
        date: '2026-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number greater than 10');
});

test('an exception is thrown if an invalid date string is passed as the rule value', function () {
    $form = new class(date: 'invalid-date') {
        use Vanguard;

        public function __construct(
            #[GreaterThan('2025-01-01')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value passed into the [GreaterThan] rule to validate is not a valid date string.'
        );
});

test('an exception is thrown if an invalid date string is passed', function () {
    $form = new class(date: '2025-01-01') {
        use Vanguard;

        public function __construct(
            #[GreaterThan('invalid-date')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value set on the [GreaterThan] rule must be a valid date string.',
        );
});