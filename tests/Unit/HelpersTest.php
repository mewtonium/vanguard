<?php

use Mewtonium\Vanguard\Tests\Fixtures\Dummy;

test('the `class_basename` helper works correctly', function () {
    expect(class_basename(new Dummy))->toBe('Dummy');
    expect(class_basename(Dummy::class))->toBe('Dummy');
    expect(class_basename('App\\Dummy'))->toBe('Dummy');

    expect(class_basename(new \stdClass))->toBe('stdClass');
    expect(class_basename(\stdClass::class))->toBe('stdClass');
    expect(class_basename((object) [1, 2, 3]))->toBe('stdClass');
});
