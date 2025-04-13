<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\LessOrEqual;
use Mewtonium\Vanguard\Vanguard;

test('an exception is thrown if an invalid date string is passed as the rule value', function (): void {
    $form = new class (date: 'invalid-date') {
        use Vanguard;

        public function __construct(
            #[LessOrEqual('2025-01-01')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            \DateException::class,
            "An invalid datetime string was provided: 'invalid-date'",
        );
});

test('an exception is thrown if an invalid date string is passed as the rule property', function (): void {
    $form = new class (date: '2025-01-01') {
        use Vanguard;

        public function __construct(
            #[LessOrEqual('invalid-date')]
            protected string $date,
        ) {
            //
        }
    };

    expect(fn () => $form->validate())
        ->toThrow(
            \DateException::class,
            "An invalid datetime string was provided: 'invalid-date'",
        );
});
