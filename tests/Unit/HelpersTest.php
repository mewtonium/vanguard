<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Tests\Fixtures\Dummy;

test('the `class_basename` helper works correctly', function (): void {
    expect(class_basename(new Dummy()))->toBe('Dummy');
    expect(class_basename(Dummy::class))->toBe('Dummy');
    expect(class_basename('App\\Dummy'))->toBe('Dummy');

    expect(class_basename(new \stdClass()))->toBe('stdClass');
    expect(class_basename(\stdClass::class))->toBe('stdClass');
    expect(class_basename((object) [1, 2, 3]))->toBe('stdClass');
});

test('the `to_date` helper works correctly', function (): void {
    expect(to_date('2025-01-01'))->toBeInstanceOf(\DateTimeInterface::class);
    expect(to_date('2025-01-01 00:00'))->toBeInstanceOf(\DateTimeInterface::class);
    expect(to_date('2025-01-01 00:00:00'))->toBeInstanceOf(\DateTimeInterface::class);

    expect(to_date('2025-01-01', immutable: true))->toBeInstanceOf(\DateTimeImmutable::class);
    expect(to_date('2025-01-01', immutable: false))->toBeInstanceOf(\DateTime::class);

    expect(to_date('not-valid'))->toBeNull();
    expect(to_date('today'))->toBeNull();
    expect(to_date('2025-01-01T00:00:00+0000'))->toBeNull();

    expect(fn () => to_date('invalid-date', throw: true))
        ->toThrow(
            \DateException::class,
            "An invalid datetime string was provided: 'invalid-date'",
        );
});
