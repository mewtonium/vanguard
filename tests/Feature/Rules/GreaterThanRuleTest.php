<?php

use Mewtonium\Vanguard\Tests\Fixtures\Forms\GreaterThanRuleForm;

test('the rule passes validation', function () {
    $form = new GreaterThanRuleForm(
        num1: 15,
        num2: 20,
        num3: 25,
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new GreaterThanRuleForm(
        num1: 15,
        num2: 10,
        num3: 25,
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('The num2 field must be greater than 10.');
    expect(array_key_exists('GreaterThan', $form->errors()->get('num2')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new GreaterThanRuleForm(
        num1: 15,
        num2: 20,
        num3: 10,
    );

    $form->validate();

    expect($form->errors()->first('num3'))->toBe('You must pick a number greater than 10');
});