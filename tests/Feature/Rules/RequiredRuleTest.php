<?php

use Mewtonium\Vanguard\Tests\Fixtures\Rules\RequiredRuleForm;

test('the rule validates', function () {
    $form = new RequiredRuleForm(
        str1: 'Test',
        str2: 'Example',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule is invalid if a required value is missing', function () {
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
