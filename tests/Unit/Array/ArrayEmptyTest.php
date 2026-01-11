<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayEmptyAbstract;
use PhpTypedValues\Exception\Array\ArrayTypeException;

covers(ArrayEmptyAbstract::class);

it('constructs from an empty array', function (): void {
    $array = [];
    $c = new ArrayEmptyAbstract($array);

    expect($c->value())->toBe($array)
        ->and($c->toArray())->toBe($array)
        ->and($c->count())->toBe(0)
        ->and($c->isEmpty())->toBeTrue();
});

it('throws exception if array is not empty', function (): void {
    expect(fn() => new ArrayEmptyAbstract([1]))
        ->toThrow(ArrayTypeException::class, 'Expected empty array');
});

it('fromArray constructs from an empty array', function (): void {
    $c = ArrayEmptyAbstract::fromArray([]);
    expect($c->value())->toBe([]);
});

it('tryFromArray constructs from an empty array', function (): void {
    $c = ArrayEmptyAbstract::tryFromArray([]);
    expect($c->value())->toBe([]);
});

it('getIterator yields nothing', function (): void {
    $c = new ArrayEmptyAbstract([]);
    $yielded = iterator_to_array($c->getIterator());
    expect($yielded)->toBe([]);
});

it('isUndefined returns false', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->isUndefined())->toBeFalse();
});

it('hasUndefined returns false', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->hasUndefined())->toBeFalse();
});

it('jsonSerialize returns empty array', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->jsonSerialize())->toBe([]);
});

it('getDefinedItems returns empty array', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->getDefinedItems())->toBe([]);
});

it('isTypeOf returns true when class matches', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->isTypeOf(ArrayEmptyAbstract::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $c = new ArrayEmptyAbstract([]);
    expect($c->isTypeOf('NonExistentClass', ArrayEmptyAbstract::class, 'AnotherClass'))->toBeTrue();
});
