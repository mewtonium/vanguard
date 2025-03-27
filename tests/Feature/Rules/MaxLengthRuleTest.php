<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Tests\Fixtures\Forms\MaxLengthRuleForm;

test('the rule passes validation', function (): void {
    $form = new MaxLengthRuleForm(
        val1: 'test',
        val2: [1, 2, 3, 4, 5],
        val3: 'test',
        val4: [6, 7, 8, 9, 10],
        val5: 'test',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function (): void {
    $form = new MaxLengthRuleForm(
        val1: 'test',
        val2: [1, 2, 3, 4, 5],
        val3: 'example',
        val4: [6, 7, 8, 9, 10, 11, 12],
        val5: 'test',
    );

    $form->validate();

    expect($form->errors()->first('val3'))->toBe('The val3 field must be a maximum of 5 characters long.');
    expect(array_key_exists('MaxLength', $form->errors()->get('val3')))->toBeTrue();

    expect($form->errors()->first('val4'))->toBe('The val4 field must be a maximum of 5 items in size.');
    expect(array_key_exists('MaxLength', $form->errors()->get('val4')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new MaxLengthRuleForm(
        val1: 'test',
        val2: [1, 2, 3, 4, 5],
        val3: 'test',
        val4: [6, 7, 8, 9, 10],
        val5: 'example',
    );

    $form->validate();

    expect($form->errors()->first('val5'))->toBe('The length is too long');
});
