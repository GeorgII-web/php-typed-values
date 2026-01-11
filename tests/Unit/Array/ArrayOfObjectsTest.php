<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayOfObjectsAbstract;
use PhpTypedValues\ArrayType\ArrayUndefinedAbstract;
use PhpTypedValues\Exception\Array\ArrayTypeException;
use PhpTypedValues\Float\Alias\Positive;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

use function array_map;

it('constructs from a valid list of PrimitiveType objects and preserves order', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $s1 = StringNonEmpty::fromString('A');

    $c = ArrayOfObjectsAbstract::fromArray([$i1, $s1]);

    expect($c->value())
        ->toHaveCount(2)
        ->and($c->value()[0])->toBe($i1)
        ->and($c->value()[1])->toBe($s1);
});

it('tryFromArray on ivalid array to get EmptyArray', function (): void {
    expect(ArrayOfObjectsAbstract::tryFromArray([IntegerNonNegative::fromInt(1), 5, 'x']))
        ->toBeInstanceOf(ArrayUndefinedAbstract::class);
});

it('isEmpty, count and iteration behave correctly', function (): void {
    $empty = ArrayOfObjectsAbstract::fromArray([]);
    expect($empty->isEmpty())->toBeTrue()
        ->and($empty->count())->toBe(0);

    $i1 = IntegerNonNegative::fromInt(2);
    $s1 = StringNonEmpty::fromString('B');
    $c = ArrayOfObjectsAbstract::fromArray([$i1, $s1]);

    $iterated = [];
    foreach ($c as $item) {
        $iterated[] = $item;
    }

    expect($c->isEmpty())->toBeFalse()
        ->and($c->count())->toBe(2)
        ->and($iterated)->toBe([$i1, $s1]);
});

it('isUndefined returns true only when all items are Undefined (and non-empty)', function (): void {
    $empty = ArrayOfObjectsAbstract::tryFromArray([]);
    expect($empty->isUndefined())->toBeFalse();

    $allUndef = ArrayOfObjectsAbstract::tryFromArray([1, 'x']);
    expect($allUndef->isUndefined())->toBeTrue();

    $mixed = ArrayOfObjectsAbstract::tryFromArray([IntegerNonNegative::fromInt(1), 'x']);
    expect($mixed->isUndefined())->toBeTrue();
});

it('hasUndefined detects presence of Undefined items', function (): void {
    $c1 = ArrayOfObjectsAbstract::fromArray([IntegerNonNegative::fromInt(1)]);
    expect($c1->hasUndefined())->toBeFalse();

    $c2 = ArrayOfObjectsAbstract::tryFromArray([IntegerNonNegative::fromInt(1), 5]);
    expect($c2->hasUndefined())->toBeTrue();
});

it('getDefinedItems returns only 2 objects', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $s1 = StringNonEmpty::fromString('A');
    $c = ArrayOfObjectsAbstract::tryFromArray([$i1, $s1]);

    $defined = $c->getDefinedItems();
    expect($defined)->toBe([$i1, $s1]);
});

it('toArray returns array of scalars via JsonSerializable and jsonSerialize delegates', function (): void {
    $i1 = IntegerNonNegative::fromInt(3);
    $s1 = StringNonEmpty::fromString('Z');
    $c = ArrayOfObjectsAbstract::tryFromArray([$i1, $s1]);

    $expected = array_map(static fn($o) => $o->jsonSerialize(), [$i1, $s1]);
    expect($c->toArray())->toBe($expected)
        ->and($c->jsonSerialize())->toBe($expected);
});

it('toArray throws when an item is not JsonSerializable', function (): void {
    $obj = new stdClass(); // not JsonSerializable
    $c = ArrayOfObjectsAbstract::fromArray([$obj]);
    expect(fn() => $c->toArray())
        ->toThrow(ArrayTypeException::class, 'Conversion to array of Scalars failed, should implement JsonSerializable interface');
});

it('fromArray throws when any item is not an object (early fail in constructor)', function (): void {
    expect(fn() => ArrayOfObjectsAbstract::fromArray([1, new stdClass()]))
        ->toThrow(ArrayTypeException::class, 'Expected array of Object instances');
});

it('does not mutate internal state across calls', function (): void {
    $i1 = IntegerNonNegative::fromInt(5);
    $i2 = IntegerNonNegative::fromInt(7);
    $c = ArrayOfObjectsAbstract::fromArray([$i1, $i2]);

    $v1 = $c->value();
    $v2 = $c->value();
    expect($v1)->toBe($v2)
        ->and($c->toArray())->toBe($c->toArray());
});

it('can be created fromItems (variadic factory)', function (): void {
    $i1 = IntegerNonNegative::fromInt(1);
    $s1 = StringNonEmpty::fromString('A');

    $c = ArrayOfObjectsAbstract::fromItems($i1, $s1);

    expect($c->value())
        ->toHaveCount(2)
        ->and($c->value()[0])->toBe($i1)
        ->and($c->value()[1])->toBe($s1);
});

describe('ArrayOfObjects specific tests', function () {
    // Test lines 145-152: getDefinedItems method with mixed undefined/defined items
    it('getDefinedItems returns only non-Undefined objects', function () {
        $items = [
            new stdClass(),
            new Undefined(),
            new class {
                public $prop = 'value';
            },
            new Undefined(),
            Positive::fromFloat(1.0),
        ];

        $array = new ArrayOfObjectsAbstract($items);

        $definedItems = $array->getDefinedItems();

        expect($definedItems)->toHaveCount(3)
            ->and($definedItems[0])->toBe($items[0])
            ->and($definedItems[1])->toBe($items[2])
            ->and($definedItems[2])->toBe($items[4]);

        // Verify no Undefined instances in result
        foreach ($definedItems as $item) {
            expect($item)->not->toBeInstanceOf(Undefined::class);
        }
    });

    // Test line 127-129: toArray with non-JsonSerializable objects
    it('toArray throws when objects dont implement JsonSerializable', function () {
        $items = [
            new stdClass(), // Doesn't implement JsonSerializable
            new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return 'serialized';
                }
            },
        ];

        $array = new ArrayOfObjectsAbstract($items);

        // This should throw because stdClass doesn't implement JsonSerializable
        expect(fn() => $array->toArray())
            ->toThrow(ArrayTypeException::class, 'JsonSerializable');
    });

    // Test line 137-143: jsonSerialize calls toArray
    it('jsonSerialize returns same as toArray', function () {
        $items = [
            new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return ['id' => 1];
                }
            },
            new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return ['id' => 2];
                }
            },
        ];

        $array = new ArrayOfObjectsAbstract($items);

        expect($array->jsonSerialize())->toBe($array->toArray())
            ->and($array->jsonSerialize())->toBe([['id' => 1], ['id' => 2]]);
    });

    // Test line 114-119: isUndefined with empty array
    it('isUndefined returns false for empty array', function () {
        $emptyArray = new ArrayOfObjectsAbstract([]);

        expect($emptyArray->isUndefined())->toBeFalse();
    });

    // Test line 114-125: isUndefined with all Undefined items
    it('isUndefined returns true when all items are Undefined', function () {
        $items = [new Undefined(), new Undefined(), new Undefined()];
        $array = new ArrayOfObjectsAbstract($items);

        expect($array->isUndefined())->toBeTrue();
    });

    // Test line 114-125: isUndefined with mixed items
    it('isUndefined returns false when any item is not Undefined', function () {
        $items = [new Undefined(), new stdClass(), new Undefined()];
        $array = new ArrayOfObjectsAbstract($items);

        expect($array->isUndefined())->toBeFalse();
    });

    // Test line 90-95: hasUndefined method
    it('hasUndefined detects Undefined items in array', function () {
        // Array with no Undefined
        $array1 = new ArrayOfObjectsAbstract([new stdClass(), new stdClass()]);
        expect($array1->hasUndefined())->toBeFalse();

        // Array with some Undefined
        $array2 = new ArrayOfObjectsAbstract([new stdClass(), new Undefined(), new stdClass()]);
        expect($array2->hasUndefined())->toBeTrue();

        // Array with all Undefined
        $array3 = new ArrayOfObjectsAbstract([new Undefined(), new Undefined()]);
        expect($array3->hasUndefined())->toBeTrue();
    });

    // Test line 70-74: count method with empty array
    it('count returns correct number for empty array', function () {
        $emptyArray = new ArrayOfObjectsAbstract([]);

        expect($emptyArray->count())->toBe(0)
            ->and($emptyArray->isEmpty())->toBeTrue();
    });

    // Test line 70-74: count method with items
    it('count returns correct number for array with items', function () {
        $items = [new stdClass(), new stdClass(), new stdClass()];
        $array = new ArrayOfObjectsAbstract($items);

        expect($array->count())->toBe(3)
            ->and($array->isEmpty())->toBeFalse();
    });

    // Test line 49-55: fromItems static method
    it('fromItems creates array from variadic arguments', function () {
        $obj1 = new stdClass();
        $obj2 = new class {
            public $test = 'value';
        };
        $obj3 = Positive::fromFloat(5.0);

        $array = ArrayOfObjectsAbstract::fromItems($obj1, $obj2, $obj3);

        expect($array)->toBeInstanceOf(ArrayOfObjectsAbstract::class)
            ->and($array->count())->toBe(3)
            ->and($array->value())->toBe([$obj1, $obj2, $obj3]);
    });

    // Test line 39-46: constructor validation
    it('constructor throws when array contains non-objects', function () {
        expect(fn() => new ArrayOfObjectsAbstract(['string', 123, new stdClass()]))
            ->toThrow(ArrayTypeException::class, 'Object instances');
    });

    // Edge case: toArray with all JsonSerializable objects
    it('toArray works with all JsonSerializable objects', function () {
        $items = [
            new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return 'first';
                }
            },
            new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return ['nested' => 'value'];
                }
            },
            Positive::fromFloat(3.14), // This implements JsonSerializable
        ];

        $array = new ArrayOfObjectsAbstract($items);

        expect($array->toArray())->toBe(['first', ['nested' => 'value'], 3.14]);
    });

    // Test getIterator yields all items
    it('getIterator yields all items in order', function () {
        $items = [new stdClass(), new stdClass(), new stdClass()];
        $array = new ArrayOfObjectsAbstract($items);

        $iterated = [];
        foreach ($array as $key => $value) {
            $iterated[$key] = $value;
        }

        expect($iterated)->toBe($items)
            ->and(array_keys($iterated))->toBe([0, 1, 2]);
    });

    // Test value() returns original array
    it('value() returns the original array', function () {
        $original = [new stdClass(), new Undefined(), Positive::fromFloat(2.0)];
        $array = new ArrayOfObjectsAbstract($original);

        expect($array->value())->toBe($original);
    });

    // Test isTypeOf method
    it('isTypeOf returns true when class matches', function () {
        $array = new ArrayOfObjectsAbstract([new stdClass()]);
        expect($array->isTypeOf(ArrayOfObjectsAbstract::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () {
        $array = new ArrayOfObjectsAbstract([new stdClass()]);
        expect($array->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () {
        $array = new ArrayOfObjectsAbstract([new stdClass()]);
        expect($array->isTypeOf('NonExistentClass', ArrayOfObjectsAbstract::class, 'AnotherClass'))->toBeTrue();
    });
});

// Additional tests for tryFromArray from parent class
describe('ArrayOfObjects tryFromArray tests', function () {
    it('tryFromArray returns ArrayOfObjects for valid object array', function () {
        $items = [new stdClass(), new stdClass()];

        $result = ArrayOfObjectsAbstract::tryFromArray($items);

        expect($result)->toBeInstanceOf(ArrayOfObjectsAbstract::class)
            ->and($result->count())->toBe(2);
    });

    it('tryFromArray returns default for invalid array', function () {
        $default = new ArrayOfObjectsAbstract([new stdClass()]);
        $invalidArray = ['not-an-object', 123];

        $result = ArrayOfObjectsAbstract::tryFromArray($invalidArray, $default);

        expect($result)->toBe($default);
    });

    it('tryFromArray returns ArrayEmpty default when no custom default provided', function () {
        $invalidArray = ['not-an-object'];

        $result = ArrayOfObjectsAbstract::tryFromArray($invalidArray);

        expect($result)->toBeInstanceOf(ArrayUndefinedAbstract::class);
    });
});
