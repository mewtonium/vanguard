<?php

use Mewtonium\Vanguard\Tests\Fixtures\Forms\LessThanRuleForm;

test('the rule passes validation', function () {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 8,
        num3: 5,
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 10,
        num3: 5,
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be less than 10.');
    expect(array_key_exists('LessThan', $form->errors()->get('num2')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new LessThanRuleForm(
        num1: 9,
        num2: 8,
        num3: 10,
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number less than 10');
});