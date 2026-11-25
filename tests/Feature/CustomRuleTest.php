<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\Between;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\After;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\Equal;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\IsStatus;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\MultipleOf;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\Same;
use Mewtonium\Vanguard\Tests\Fixtures\CustomRules\Uppercase;
use Mewtonium\Vanguard\Vanguard;

test('a custom rule extending Rule is discovered and runs', function (): void {
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

test('a custom rule extending Rule passes for valid input', function (): void {
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

test('a custom message can use placeholders', function (): void {
    $form = new class (age: 17) {
        use Vanguard;

        public function __construct(
            #[Between(18, 99, message: 'The :field must be :min to :max.')]
            protected int $age,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->first('age'))->toBe('The age must be 18 to 99.');
});

test('a custom rule prepares its arguments alongside the value', function (): void {
    $form = new class (startsAt: '2024-06-01') {
        use Vanguard;

        public function __construct(
            #[After('2025-01-01')]
            protected string $startsAt,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->first('startsAt'))->toBe('The startsAt field must be after 2025-01-01 00:00:00.');
});

test('a custom rule interpolates private arguments', function (): void {
    $form = new class (quantity: 12) {
        use Vanguard;

        public function __construct(
            #[MultipleOf(5)]
            protected int $quantity,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->first('quantity'))->toBe('The quantity field must be a multiple of 5.');
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

test('a lenient custom rule keeps strings that are not dates', function (): void {
    $form = new class (status: 'active', startsAt: '2025-01-01', endsAt: 'later') {
        use Vanguard;

        public function __construct(
            #[Same('active')]
            protected string $status,
            #[Same('2025-01-01 00:00')]
            protected string $startsAt,
            #[Same('2025-01-01')]
            protected string $endsAt,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->has('status'))->toBeFalse();
    expect($form->errors()->has('startsAt'))->toBeFalse();
    expect($form->errors()->first('endsAt'))->toBe('The endsAt field must be the same as 2025-01-01 00:00:00.');
});

test('a lenient custom rule keeps the value when prepare() throws a ValueError', function (): void {
    $form = new class (valid: 'active', invalid: 'bogus') {
        use Vanguard;

        public function __construct(
            #[IsStatus]
            protected string $valid,
            #[IsStatus]
            protected string $invalid,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->has('valid'))->toBeFalse();
    expect($form->errors()->first('invalid'))->toBe('The invalid field must be a valid status.');
});
