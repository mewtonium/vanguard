<?php

use Mewtonium\Vanguard\Tests\Fixtures\Forms\LessOrEqualRuleForm;

test('the rule passes validation', function () {
    $form = new LessOrEqualRuleForm(
        num1: 10,
        num2: 8,
        num3: 5,
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new LessOrEqualRuleForm(
        num1: 10,
        num2: 20,
        num3: 5,
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be less than or equal to 10.');
    expect(array_key_exists('LessOrEqual', $form->errors()->get('num2')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new LessOrEqualRuleForm(
        num1: 10,
        num2: 8,
        num3: 20,
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number less or equal to 10');
});