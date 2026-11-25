<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Equal;
use Mewtonium\Vanguard\Rules\In;
use Mewtonium\Vanguard\Rules\MaxLength;
use Mewtonium\Vanguard\Rules\MinLength;
use Mewtonium\Vanguard\Vanguard;

test('a plural group uses the singular form for a count of one', function (): void {
    $form = new class () {
        use Vanguard;

        #[MaxLength(1, message: 'Only :length {tag|tags} allowed.')]
        public array $tags = ['a', 'b'];
    };

    $form->validate();

    expect($form->errors()->first('tags'))->toBe('Only 1 tag allowed.');
});

test('a plural group uses the plural form for any other count', function (): void {
    $form = new class () {
        use Vanguard;

        #[MaxLength(0, message: 'Only :length {tag|tags} allowed.')]
        public array $none = ['a'];

        #[MaxLength(3, message: 'Only :length {tag|tags} allowed.')]
        public array $some = ['a', 'b', 'c', 'd'];
    };

    $form->validate();

    expect($form->errors()->first('none'))->toBe('Only 0 tags allowed.');
    expect($form->errors()->first('some'))->toBe('Only 3 tags allowed.');
});

test('a plural group counts the items of an array argument', function (): void {
    $form = new class () {
        use Vanguard;

        #[In(['GB'], message: 'Only :values {is|are} supported.')]
        public string $one = 'FR';

        #[In(['GB', 'FR'], message: 'Only :values {is|are} supported.')]
        public string $many = 'DE';
    };

    $form->validate();

    expect($form->errors()->first('one'))->toBe('Only GB is supported.');
    expect($form->errors()->first('many'))->toBe('Only GB, FR are supported.');
});

test('the length rules pluralise their default messages', function (): void {
    $form = new class () {
        use Vanguard;

        #[MinLength(1)]
        public string $minString = '';

        #[MinLength(1)]
        public array $minArray = [];

        #[MaxLength(1)]
        public string $maxString = 'ab';

        #[MaxLength(1)]
        public array $maxArray = ['a', 'b'];
    };

    $form->validate();

    expect($form->errors()->first('minString'))->toBe('The minString field must be at least 1 character.');
    expect($form->errors()->first('minArray'))->toBe('The minArray field must have at least 1 item.');
    expect($form->errors()->first('maxString'))->toBe('The maxString field must not exceed 1 character.');
    expect($form->errors()->first('maxArray'))->toBe('The maxArray field must not have more than 1 item.');
});

test('the field placeholder is not formatted by the rule', function (): void {
    $form = new class () {
        use Vanguard;

        #[Equal('active', message: ':field must be :to.')]
        public string $status = 'inactive';
    };

    $form->validate();

    expect($form->errors()->first('status'))->toBe("status must be 'active'.");
});

test('unknown placeholders and stray braces are left untouched', function (): void {
    $form = new class () {
        use Vanguard;

        #[MinLength(5, message: 'Retry in :minutes. Use {braces} or {a|b}, but need :length.')]
        public string $code = 'abc';
    };

    $form->validate();

    expect($form->errors()->first('code'))->toBe('Retry in :minutes. Use {braces} or {a|b}, but need 5.');
});
