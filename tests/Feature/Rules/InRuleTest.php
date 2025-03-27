<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\In;
use Mewtonium\Vanguard\Tests\Fixtures\Forms\InRuleForm;
use Mewtonium\Vanguard\Vanguard;

test('the rule passes validation', function (): void {
    $form = new InRuleForm(
        val1: 'GB',
        val2: 100,
        val3: 'FR',
    );

    $form->validate();

    expect($form->errors()->count())->toBe(0);
});

test('the rule fails validation', function (): void {
    $form = new InRuleForm(
        val1: 'GB',
        val2: 500,
        val3: 'FR',
    );

    $form->validate();

    expect($form->errors()->first('val2'))->toBe('The val2 field does not have a valid selection.');
    expect(array_key_exists('In', $form->errors()->get('val2')))->toBeTrue();
});

test('a custom validation message can be set', function (): void {
    $form = new InRuleForm(
        val1: 'GB',
        val2: 500,
        val3: 'XX',
    );

    $form->validate();

    expect($form->errors()->first('val3'))->toBe('Please pick a value');
});

test('an exception is thrown if no values are provided to validate against', function (): void {
    $form = new class (val1: 'GB') {
        use Vanguard;

        public function __construct(
            #[In([])]
            protected string $val1,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The [In] rule must have at least one value to check against.',
        );
});
