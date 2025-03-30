<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\GreaterOrEqual;
use Mewtonium\Vanguard\Tests\Fixtures\Forms\GreaterOrEqualRuleForm;
use Mewtonium\Vanguard\Vanguard;

test('the rule passes validation', function (): void {
    $form = new GreaterOrEqualRuleForm(
        num1: 10,
        num2: 15,
        num3: 20,
        date: '2025-03-27',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function (): void {
    $form = new GreaterOrEqualRuleForm(
        num1: 10,
        num2: 0,
        num3: 20,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be greater than or equal to 10.');
    expect(array_key_exists('GreaterOrEqual', $form->errors()->get('num2')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe('The date field must be greater than or equal to 2025-01-01.');
    expect(array_key_exists('GreaterOrEqual', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new GreaterOrEqualRuleForm(
        num1: 10,
        num2: 15,
        num3: 0,
        date: '2025-03-27',
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number greater or equal to 10');
});

test('an exception is thrown if an invalid date string is passed as the rule value', function (): void {
    $form = new class (date: 'invalid-date') {
        use Vanguard;

        public function __construct(
            #[GreaterOrEqual('2025-01-01')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value passed into the [GreaterOrEqual] rule to validate is not a valid date string.'
        );
});

test('an exception is thrown if an invalid date string is passed', function (): void {
    $form = new class (date: '2025-01-01') {
        use Vanguard;

        public function __construct(
            #[GreaterOrEqual('invalid-date')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The value set on the [GreaterOrEqual] rule must be a valid date string.',
        );
});
