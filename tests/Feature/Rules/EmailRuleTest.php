<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Tests\Fixtures\Forms\EmailRuleForm;

test('the rule passes validation', function (): void {
    $form = new EmailRuleForm(
        str1: 'str1@example.com',
        str2: 'str2@example.com',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function (): void {
    $form = new EmailRuleForm(
        str1: 'invalid-email',
        str2: 'str2@example.com',
    );

    $form->validate();

    expect($form->errors()->first('str1'))->toBe('The str1 field must be a valid email.');
    expect(array_key_exists('Email', $form->errors()->get('str1')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new EmailRuleForm(
        str1: 'str1@example.com',
        str2: 'str2',
    );

    $form->validate();

    expect($form->errors()->first('str2'))->toBe('Your email is invalid');
});
