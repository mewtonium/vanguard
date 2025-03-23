<?php

use Mewtonium\Vanguard\Tests\Fixtures\Rules\BetweenRuleForm;

test('the rule passes validation', function () {
    $form = new BetweenRuleForm(
        num1: 5,
        num2: 5,
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new BetweenRuleForm(
        num1: 100,
        num2: 5,
    );

    $form->validate();

    expect($form->errors()->first('num1'))->toBe('The num1 field must be between 1 and 10.');
    expect(array_key_exists('Between', $form->errors()->get('num1')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new BetweenRuleForm(
        num1: 5,
        num2: 100,
    );

    $form->validate();

    expect($form->errors()->first('num2'))->toBe('You must pick a number between 1 and 10');
});