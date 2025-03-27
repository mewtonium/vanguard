<?php

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\LessThan;
use Mewtonium\Vanguard\Tests\Fixtures\Forms\LessThanRuleForm;

test('the rule passes validation', function () {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 8,
        num3: 5,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 10,
        num3: 5,
        date: '2026-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be less than 10.');
    expect(array_key_exists('LessThan', $form->errors()->get('num2')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe('The date field must be less than 2025-01-01.');
    expect(array_key_exists('LessThan', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 8,
        num3: 10,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number less than 10');
});

test('an exception is thrown if an invalid date string is passed as the rule value', function () {
    $form = new class(date: 'invalid-date') {
        use Vanguard;

        public function __construct(
            #[LessThan('2025-01-01')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value passed into the [LessThan] rule to validate is not a valid date string.'
        );
});

test('an exception is thrown if an invalid date string is passed', function () {
    $form = new class(date: '2025-01-01') {
        use Vanguard;

        public function __construct(
            #[LessThan('invalid-date')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value set on the [LessThan] rule must be a valid date string.',
        );
});