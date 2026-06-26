<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\Equal;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\Uppercase;
use Mewtonium\Vanguard\Vanguard;

test('a custom rule extending BaseRule is discovered and runs', function (): void {
    $form = new class (code: 'abc') {
        use Vanguard;

        public function __construct(
            #[Uppercase]
            protected string $code,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->invalid())->toBeTrue();
    expect($form->errors()->first('code'))->toBe('The code field must be uppercase.');
    expect(array_key_exists('Uppercase', $form->errors()->get('code')))->toBeTrue();
});

test('a custom rule extending BaseRule passes for valid input', function (): void {
    $form = new class (code: 'ABC') {
        use Vanguard;

        public function __construct(
            #[Uppercase]
            protected string $code,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->invalid())->toBeFalse();
});

test('a custom rule supports a custom message', function (): void {
    $form = new class (code: 'abc') {
        use Vanguard;

        public function __construct(
            #[Uppercase(message: 'Shout it!')]
            protected string $code,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->first('code'))->toBe('Shout it!');
});

test('a custom rule that shadows a built-in rule name throws', function (): void {
    $form = new class (code: 'abc') {
        use Vanguard;

        public function __construct(
            #[Equal]
            protected string $code,
        ) {
            //
        }
    };

    expect(fn (): mixed => $form->validate())
        ->toThrow(RuleException::class, 'The [Equal] rule already exists as a built-in Vanguard rule.');
});
