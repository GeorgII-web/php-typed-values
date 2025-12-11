<?php

declare(strict_types=1);

use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Usage\Example\ArrayOfStrings;

it('creates from non-empty list of strings and exposes typed values', function (): void {
    $a = ArrayOfStrings::fromArray(['foo', 'bar']);

    // value() returns non-empty-list<StringNonEmpty>
    $vals = $a->value();
    expect($vals)->toBeArray()->and($vals)->toHaveCount(2);
    foreach ($vals as $v) {
        expect($v)->toBeInstanceOf(StringNonEmpty::class);
    }

    // toArray/jsonSerialize return non-empty-list<non-empty-string>
    expect($a->toArray())->toBe(['foo', 'bar']);
    expect($a->jsonSerialize())->toBe(['foo', 'bar']);

    // IteratorAggregate yields the same items
    $iterated = [];
    foreach ($a as $item) {
        $iterated[] = $item->toString();
    }
    expect($iterated)->toBe(['foo', 'bar']);
});

it('fails on empty array input (fromArray)', function (): void {
    expect(fn() => ArrayOfStrings::fromArray([]))
        ->toThrow(TypeException::class, 'Expected non-empty array');
});

it('fails on invalid item (empty string) (fromArray)', function (): void {
    expect(fn() => ArrayOfStrings::fromArray(['']))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('tryFromArray mirrors fromArray success', function (): void {
    $a = ArrayOfStrings::tryFromArray(['one']);
    expect($a)->toBeInstanceOf(ArrayOfStrings::class);
    expect($a->toArray())->toBe(['one']);
});

it('tryFromArray fails on empty array just like fromArray (current behavior)', function (): void {
    // current implementation proxies to fromArray and throws TypeException
    expect(fn() => ArrayOfStrings::tryFromArray([]))
        ->toThrow(TypeException::class, 'Expected non-empty array');
});

it('can be constructed directly with non-empty-list<StringNonEmpty>', function (): void {
    $a = new ArrayOfStrings([new StringNonEmpty('x'), new StringNonEmpty('y')]);

    // verify end-to-end
    expect($a->toArray())->toBe(['x', 'y']);
    $collected = [];
    foreach ($a as $item) {
        $collected[] = $item->toString();
    }
    expect($collected)->toBe(['x', 'y']);
});

it('constructor fails on empty array', function (): void {
    expect(fn() => new ArrayOfStrings([]))
        ->toThrow(TypeException::class, 'Expected non-empty array');
});

it('constructor fails when element is not StringNonEmpty', function (): void {
    // mix valid object with invalid scalar to hit the element-type guard
    /** @var array $bad */
    $bad = [new StringNonEmpty('ok'), 'oops'];
    expect(fn() => new ArrayOfStrings($bad))
        ->toThrow(StringTypeException::class, 'Expected array of StringNonEmpty or Undefined instance');
});

it('casts mixed scalars to strings in fromArray', function (): void {
    // Without explicit (string) cast inside fromArray, this would fail under strict types
    $a = ArrayOfStrings::fromArray([123, 45.6, '789']);
    expect($a->toArray())->toBe(['123', '45.6', '789']);
});

it('serializes to JSON and back', function (): void {
    $array = ['foo', 'bar'];

    $arrayOfStrings = ArrayOfStrings::fromArray($array);

    $arrayOfStringsJson = json_encode($arrayOfStrings, JSON_THROW_ON_ERROR);
    $arrayOfStringsJsonDecoded = json_decode($arrayOfStringsJson, true);

    expect($arrayOfStrings->toArray())->toBe($array);
    expect($arrayOfStringsJsonDecoded)->toBe($array);
});
