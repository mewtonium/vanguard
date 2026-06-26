<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Rules\Url;
use Mewtonium\Vanguard\Vanguard;

function urlForm(string $value): object
{
    return new class ($value) {
        use Vanguard;

        public function __construct(
            #[Url(message: 'Enter a valid website.')]
            protected string $website,
        ) {
            //
        }
    };
}

test('the rule passes validation', function (): void {
    $form = urlForm('https://example.com');
    $form->validate();

    expect($form->invalid())->toBeFalse();
});

test('the rule fails validation', function (): void {
    $form = urlForm('not-a-url');
    $form->validate();

    expect($form->errors()->first('website'))->toBe('Enter a valid website.');
    expect(array_key_exists('Url', $form->errors()->get('website')))->toBeTrue();
});
