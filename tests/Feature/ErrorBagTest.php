<?php

declare(strict_types=1);

use Mewtonium\Vanguard\ErrorBag;

beforeEach(function (): void {
    $this->errors = new ErrorBag();

    $this->errors->add('field1', 'Rule1', 'Test validation message 1');
    $this->errors->add('field1', 'Rule2', 'Test validation message 2');
    $this->errors->add('field2', 'Rule1', 'Test validation message 3');
});

test('that `all` will correctly return all errors', function (): void {
    expect($this->errors->all())->toEqual([
        'field1' => [
            'Rule1' => 'Test validation message 1',
            'Rule2' => 'Test validation message 2',
        ],
        'field2' => [
            'Rule1' => 'Test validation message 3',
        ],
    ]);
});

test('that `get` will correctly return all errors by field', function (): void {
    expect($this->errors->get('field1'))->toEqual([
        'Rule1' => 'Test validation message 1',
        'Rule2' => 'Test validation message 2',
    ]);

    expect($this->errors->get('xxx'))->toBeNull();
});

test('that `first` will correctly return the first error by field and rule', function (): void {
    expect($this->errors->first('field1'))->toEqual('Test validation message 1');
    expect($this->errors->first('xxx'))->toBeNull();
});

test('that `has` will correctly check an error exists', function (): void {
    expect($this->errors->has('field1'))->toBeTrue();
    expect($this->errors->has('xxx'))->toBeFalse();
});

test('that `count` will return the correct number of errors', function (): void {
    expect($this->errors->count())->toBe(2);
});

test('that `flush` will remove all errors', function (): void {
    $this->errors->flush();

    expect($this->errors->count())->toBe(0);
});
