<?php

use Mewtonium\Vanguard\Tests\Fixtures\Forms\RequiredRuleForm;

test('the rule passes validation', function () {
    $form = new RequiredRuleForm(
        str1: 'Test',
        str2: 'Example',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function () {
    $form = new RequiredRuleForm(
        str1: '',
        str2: 'Example',
    );

    $form->validate();

    expect($form->errors()->first('str1'))->toBe('The str1 field is required.');
    expect(array_key_exists('Required', $form->errors()->get('str1')))->toBeTrue();
});

test('a custom validation message can be set', function () {
    $form = new RequiredRuleForm(
        str1: 'Test',
        str2: '',
    );

    $form->validate();

    expect($form->errors()->first('str2'))->toBe('Please provide str2.');
});
