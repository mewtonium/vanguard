<?php

use Mewtonium\Vanguard\Tests\Fixtures\Forms\EqualRuleForm;

test('the rule passes validation', function () {
    $form = new EqualRuleForm(
        num1: 10,
        num2: 3.495,
        str1: 'test',
        str2: 'testing',
        date: '2025-01-01',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new EqualRuleForm(
        num1: 100,
        num2: 3.495,
        str1: 'invalid-value',
        str2: 'testing',
        date: '2026-01-01',
    );

    $form->validate();

    expect($form->errors()->first('num1'))->toBe('The num1 field must be equal to 10.');
    expect(array_key_exists('Equal', $form->errors()->get('num1')))->toBeTrue();

    expect($form->errors()->first('str1'))->toBe("The str1 field must be equal to 'test'.");
    expect(array_key_exists('Equal', $form->errors()->get('str1')))->toBeTrue();

    expect($form->errors()->first('date'))->toBe("The date field must be equal to '2025-01-01'.");
    expect(array_key_exists('Equal', $form->errors()->get('date')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new EqualRuleForm(
        num1: 10,
        num2: 3.495,
        str1: 'test',
        str2: 'invalid-value',
        date: '2025-01-01',
    );

    $form->validate();

    expect($form->errors()->first('str2'))->toBe('The value must equal "testing"');
});