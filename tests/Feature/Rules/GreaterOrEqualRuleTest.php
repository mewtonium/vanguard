<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Tests\Fixtures\Forms\GreaterOrEqualRuleForm;

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

    expect($form->errors()->first('date'))->toBe('The date field must be greater than or equal to 2025-01-01 00:00:00.');
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
