<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayEmpty;
use PhpTypedValues\Exception\Array\ArrayTypeException;

covers(ArrayEmpty::class);

it('constructs from an empty array', function (): void {
    $array = [];
    $c = new ArrayEmpty($array);

    expect($c->value())->toBe($array)
        ->and($c->toArray())->toBe($array)
        ->and($c->count())->toBe(0)
        ->and($c->isEmpty())->toBeTrue();
});

it('throws exception if array is not empty', function (): void {
    expect(fn() => new ArrayEmpty([1]))
        ->toThrow(ArrayTypeException::class, 'Expected empty array');
});

it('fromArray constructs from an empty array', function (): void {
    $c = ArrayEmpty::fromArray([]);
    expect($c->value())->toBe([]);
});

it('tryFromArray constructs from an empty array', function (): void {
    $c = ArrayEmpty::tryFromArray([]);
    expect($c->value())->toBe([]);
});

it('getIterator yields nothing', function (): void {
    $c = new ArrayEmpty([]);
    $yielded = iterator_to_array($c->getIterator());
    expect($yielded)->toBe([]);
});

it('isUndefined returns false', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->isUndefined())->toBeFalse();
});

it('hasUndefined returns false', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->hasUndefined())->toBeFalse();
});

it('jsonSerialize returns empty array', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->jsonSerialize())->toBe([]);
});

it('getDefinedItems returns empty array', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->getDefinedItems())->toBe([]);
});

it('isTypeOf returns true when class matches', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->isTypeOf(ArrayEmpty::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $c = new ArrayEmpty([]);
    expect($c->isTypeOf('NonExistentClass', ArrayEmpty::class, 'AnotherClass'))->toBeTrue();
});
