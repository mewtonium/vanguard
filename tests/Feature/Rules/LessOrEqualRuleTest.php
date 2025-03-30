<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\LessOrEqual;
use Mewtonium\Vanguard\Tests\Fixtures\Forms\LessOrEqualRuleForm;
use Mewtonium\Vanguard\Vanguard;

test('the rule passes validation', function (): void {
    $form = new LessOrEqualRuleForm(
        num1: 10,
        num2: 8,
        num3: 5,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function (): void {
    $form = new LessOrEqualRuleForm(
        num1: 10,
        num2: 20,
        num3: 5,
        date: '2026-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be less than or equal to 10.');
    expect(array_key_exists('LessOrEqual', $form->errors()->get('num2')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe('The date field must be less than or equal to 2025-01-01.');
    expect(array_key_exists('LessOrEqual', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new LessOrEqualRuleForm(
        num1: 10,
        num2: 8,
        num3: 20,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number less or equal to 10');
});

test('an exception is thrown if an invalid date string is passed as the rule value', function (): void {
    $form = new class (date: 'invalid-date') {
        use Vanguard;

        public function __construct(
            #[LessOrEqual('2025-01-01')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value passed into the [LessOrEqual] rule to validate is not a valid date string.'
        );
});

test('an exception is thrown if an invalid date string is passed', function (): void {
    $form = new class (date: '2025-01-01') {
        use Vanguard;

        public function __construct(
            #[LessOrEqual('invalid-date')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value set on the [LessOrEqual] rule must be a valid date string.',
        );
});
