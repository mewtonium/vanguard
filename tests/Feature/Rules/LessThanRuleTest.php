<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Tests\Fixtures\Forms\LessThanRuleForm;

test('the rule passes validation', function (): void {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 8,
        num3: 5,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function (): void {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 10,
        num3: 5,
        date: '2026-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be less than 10.');
    expect(array_key_exists('LessThan', $form->errors()->get('num2')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe('The date field must be less than 2025-01-01 00:00:00.');
    expect(array_key_exists('LessThan', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 8,
        num3: 10,
        date: '2024-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number less than 10');
});
