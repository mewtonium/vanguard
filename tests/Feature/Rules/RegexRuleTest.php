<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Exceptions\RuleException;
use Mewtonium\Vanguard\Rules\Regex;
use Mewtonium\Vanguard\Vanguard;

test('the rule passes when the value matches the pattern', function (): void {
    $form = new class (reference: 'AB-123') {
        use Vanguard;

        public function __construct(
            #[Regex('/^[A-Z]{2}-\d{3}$/')]
            protected string $reference,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->invalid())->toBeFalse();
});

test('the rule fails when the value does not match the pattern', function (): void {
    $form = new class (reference: 'nope') {
        use Vanguard;

        public function __construct(
            #[Regex('/^[A-Z]{2}-\d{3}$/')]
            protected string $reference,
        ) {
            //
        }
    };

    $form->validate();

    expect($form->errors()->first('reference'))->toBe('The reference field is in an invalid format.');
    expect(array_key_exists('Regex', $form->errors()->get('reference')))->toBeTrue();
});

test('the rule throws when given an invalid pattern', function (): void {
    $form = new class (reference: 'anything') {
        use Vanguard;

        public function __construct(
            #[Regex('not-a-valid-pattern')]
            protected string $reference,
        ) {
            //
        }
    };

    // The invalid pattern makes preg_match() emit a native warning before it
    // returns false; silence it so only the RuleException under test surfaces.
    withoutErrorHandler(fn () => expect(fn () => $form->validate())
        ->toThrow(
            RuleException::class,
            'The pattern supplied to the [Regex] rule is invalid.',
        ));
});
