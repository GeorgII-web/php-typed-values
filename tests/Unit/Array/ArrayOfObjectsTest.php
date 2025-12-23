<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Array\ArrayOfObjects;

use PhpTypedValues\ArrayType\ArrayOfObjects;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

use function array_map;

it('constructs from a valid list of PrimitiveType objects and preserves order', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $s1 = StringNonEmpty::fromString('A');

    $c = ArrayOfObjects::fromArray([$i1, $s1]);

    expect($c->value())
        ->toHaveCount(2)
        ->and($c->value()[0])->toBe($i1)
        ->and($c->value()[1])->toBe($s1);
});

it('tryFromArray converts non-objects to Undefined, keeps objects intact', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $c = ArrayOfObjects::tryFromArray([$i1, 5, 'x']);

    $items = $c->value();
    expect($items)->toHaveCount(3)
        ->and($items[0])->toBe($i1)
        ->and($items[1])->toBeInstanceOf(Undefined::class)
        ->and($items[2])->toBeInstanceOf(Undefined::class);
});

it('isEmpty, count and iteration behave correctly', function (): void {
    $empty = ArrayOfObjects::fromArray([]);
    expect($empty->isEmpty())->toBeTrue()
        ->and($empty->count())->toBe(0);

    $i1 = IntegerNonNegative::fromInt(2);
    $s1 = StringNonEmpty::fromString('B');
    $c = ArrayOfObjects::fromArray([$i1, $s1]);

    $iterated = [];
    foreach ($c as $item) {
        $iterated[] = $item;
    }

    expect($c->isEmpty())->toBeFalse()
        ->and($c->count())->toBe(2)
        ->and($iterated)->toBe([$i1, $s1]);
});

it('isUndefined returns true only when all items are Undefined (and non-empty)', function (): void {
    $empty = ArrayOfObjects::fromArray([]);
    expect($empty->isUndefined())->toBeFalse();

    $allUndef = ArrayOfObjects::tryFromArray([1, 'x']);
    expect($allUndef->isUndefined())->toBeTrue();

    $mixed = ArrayOfObjects::tryFromArray([IntegerNonNegative::fromInt(1), 'x']);
    expect($mixed->isUndefined())->toBeFalse();
});

it('hasUndefined detects presence of Undefined items', function (): void {
    $c1 = ArrayOfObjects::fromArray([IntegerNonNegative::fromInt(1)]);
    expect($c1->hasUndefined())->toBeFalse();

    $c2 = ArrayOfObjects::tryFromArray([IntegerNonNegative::fromInt(1), 5]);
    expect($c2->hasUndefined())->toBeTrue();
});

it('getDefinedItems returns only non-Undefined items preserving order', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $s1 = StringNonEmpty::fromString('A');
    $c = ArrayOfObjects::tryFromArray([$i1, 'x', $s1, 0]);

    $defined = $c->getDefinedItems();
    expect($defined)->toBe([$i1, $s1]);
});

it('toArray returns array of scalars via JsonSerializable and jsonSerialize delegates', function (): void {
    $i1 = IntegerNonNegative::fromInt(3);
    $s1 = StringNonEmpty::fromString('Z');
    $c = ArrayOfObjects::fromArray([$i1, $s1]);

    $expected = array_map(static fn($o) => $o->jsonSerialize(), [$i1, $s1]);
    expect($c->toArray())->toBe($expected)
        ->and($c->jsonSerialize())->toBe($expected);
});

it('toArray throws when an item is not JsonSerializable', function (): void {
    $obj = new stdClass(); // not JsonSerializable
    $c = ArrayOfObjects::fromArray([$obj]);
    expect(fn() => $c->toArray())
        ->toThrow(ArrayTypeException::class, 'Conversion to array of Scalars failed, should implement JsonSerializable interface');
});

it('fromArray throws when any item is not an object (early fail in constructor)', function (): void {
    expect(fn() => ArrayOfObjects::fromArray([1, new stdClass()]))
        ->toThrow(ArrayTypeException::class, 'Expected array of Object instances');
});

it('does not mutate internal state across calls', function (): void {
    $i1 = IntegerNonNegative::fromInt(5);
    $i2 = IntegerNonNegative::fromInt(7);
    $c = ArrayOfObjects::fromArray([$i1, $i2]);

    $v1 = $c->value();
    $v2 = $c->value();
    expect($v1)->toBe($v2)
        ->and($c->toArray())->toBe($c->toArray());
});

it('can be created fromItems (variadic factory)', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $s1 = StringNonEmpty::fromString('A');

    $c = ArrayOfObjects::fromItems($i1, $s1);

    expect($c->value())
        ->toHaveCount(2)
        ->and($c->value()[0])->toBe($i1)
        ->and($c->value()[1])->toBe($s1);
});
