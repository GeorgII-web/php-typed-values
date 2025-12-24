<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String;

use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\String\StringEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StringEmpty::class);

it('constructs from an empty string', function (): void {
    $c = new StringEmpty('');

    expect($c->value())->toBe('')
        ->and($c->toString())->toBe('')
        ->and((string) $c)->toBe('')
        ->and($c->isEmpty())->toBeTrue();
});

it('throws exception if string is not empty', function (): void {
    expect(fn() => new StringEmpty('hello'))
        ->toThrow(StringTypeException::class, 'Expected empty string, got "hello"');
});

it('fromString constructs from an empty string', function (): void {
    $c = StringEmpty::fromString('');
    expect($c->value())->toBe('');
});

it('tryFromString constructs from an empty string', function (): void {
    $c = StringEmpty::tryFromString('');
    expect($c)->toBeInstanceOf(StringEmpty::class)
        ->and($c->value())->toBe('');
});

it('tryFromString returns Undefined for non-empty string', function (): void {
    $c = StringEmpty::tryFromString('hello');
    expect($c)->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed constructs from an empty string', function (): void {
    $c = StringEmpty::tryFromMixed('');
    expect($c)->toBeInstanceOf(StringEmpty::class)
        ->and($c->value())->toBe('');
});

it('tryFromMixed returns Undefined for non-empty string', function (): void {
    $c = StringEmpty::tryFromMixed('hello');
    expect($c)->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed returns Undefined for non-stringable mixed', function (): void {
    $c = StringEmpty::tryFromMixed([]);
    expect($c)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns empty string', function (): void {
    $c = new StringEmpty('');
    expect($c->jsonSerialize())->toBe('');
});

it('isUndefined returns false', function (): void {
    $c = new StringEmpty('');
    expect($c->isUndefined())->toBeFalse();
});
