<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayUndefinedAbstract;
use PhpTypedValues\Exception\Array\ArrayUndefinedTypeException;

describe('ArrayUndefined specific tests', function () {
    // Test lines 24-27: fromArray always returns new instance
    it('fromArray returns new ArrayUndefined instance regardless of input', function () {
        $result1 = ArrayUndefinedAbstract::fromArray([]);
        $result2 = ArrayUndefinedAbstract::fromArray([1, 2, 3]);
        $result3 = ArrayUndefinedAbstract::fromArray(['key' => 'value']);

        expect($result1)->toBeInstanceOf(ArrayUndefinedAbstract::class)
            ->and($result2)->toBeInstanceOf(ArrayUndefinedAbstract::class)
            ->and($result3)->toBeInstanceOf(ArrayUndefinedAbstract::class)
            ->and($result1)->not->toBe($result2) // Different instances
            ->and($result1->isEmpty())->toBeTrue()
            ->and($result1->isUndefined())->toBeTrue();
    });

    // Test lines 30-33: value() throws exception
    it('value() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->value())
            ->toThrow(ArrayUndefinedTypeException::class, 'Undefined array has no value');
    });

    // Test lines 36-39: getIterator() throws exception
    it('getIterator() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->getIterator())
            ->toThrow(ArrayUndefinedTypeException::class, 'iterator');

        // Also test that foreach throws
        $array = new ArrayUndefinedAbstract();
        expect(function () use ($array) {
            foreach ($array as $item) {
                // This should never execute
                throw new Exception('Should not reach here');
            }
        })->toThrow(ArrayUndefinedTypeException::class);
    });

    // Test lines 42-45: count() throws exception
    it('count() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->count())
            ->toThrow(ArrayUndefinedTypeException::class, 'count');

        // Also test count() function usage
        expect(fn() => \count($array))
            ->toThrow(ArrayUndefinedTypeException::class);
    });

    // Test lines 48-51: toArray() throws exception
    it('toArray() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->toArray())
            ->toThrow(ArrayUndefinedTypeException::class, 'converted to array');
    });

    // Test lines 54-57: jsonSerialize() throws exception
    it('jsonSerialize() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->jsonSerialize())
            ->toThrow(ArrayUndefinedTypeException::class, 'Json');

        // Test json_encode also throws
        expect(fn() => json_encode($array))
            ->toThrow(ArrayUndefinedTypeException::class);
    });

    // Test lines 60-62: isEmpty() always returns true
    it('isEmpty() always returns true', function () {
        $array = new ArrayUndefinedAbstract();

        expect($array->isEmpty())->toBeTrue();
    });

    // Test lines 64-66: isUndefined() always returns true
    it('isUndefined() always returns true', function () {
        $array = new ArrayUndefinedAbstract();

        expect($array->isUndefined())->toBeTrue();
    });

    // Test lines 68-70: hasUndefined() always returns true
    it('hasUndefined() always returns true', function () {
        $array = new ArrayUndefinedAbstract();

        expect($array->hasUndefined())->toBeTrue();
    });

    // Test lines 73-78: getDefinedItems() throws exception
    it('getDefinedItems() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->getDefinedItems())
            ->toThrow(ArrayUndefinedTypeException::class, 'defined items');
    });

    // Test lines 81-83: create() factory method
    it('create() returns new ArrayUndefined instance', function () {
        $result1 = ArrayUndefinedAbstract::create();
        $result2 = ArrayUndefinedAbstract::create();

        expect($result1)->toBeInstanceOf(ArrayUndefinedAbstract::class)
            ->and($result2)->toBeInstanceOf(ArrayUndefinedAbstract::class)
            ->and($result1)->not->toBe($result2) // Different instances
            ->and($result1->isEmpty())->toBeTrue()
            ->and($result1->isUndefined())->toBeTrue();
    });

    // Test lines 86-89: toInt() throws exception
    it('toInt() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->toInt())
            ->toThrow(ArrayUndefinedTypeException::class, 'integer');
    });

    // Test lines 92-95: toFloat() throws exception
    it('toFloat() throws ArrayUndefinedTypeException', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => $array->toFloat())
            ->toThrow(ArrayUndefinedTypeException::class, 'float');
    });

    // Test constructor works without arguments
    it('can be instantiated without arguments', function () {
        $array = new ArrayUndefinedAbstract();

        expect($array)->toBeInstanceOf(ArrayUndefinedAbstract::class)
            ->and($array->isEmpty())->toBeTrue()
            ->and($array->isUndefined())->toBeTrue();
    });

    // Edge case: test all throwing methods with try-catch to verify exception type
    it('all throwing methods throw ArrayUndefinedTypeException specifically', function () {
        $array = new ArrayUndefinedAbstract();
        $throwingMethods = [
            'value',
            'getIterator',
            'count',
            'toArray',
            'jsonSerialize',
            'getDefinedItems',
            'toInt',
            'toFloat',
        ];

        foreach ($throwingMethods as $method) {
            try {
                $array->{$method}();
                expect(false)->toBeTrue(); // Should never reach here
            } catch (ArrayUndefinedTypeException $e) {
                expect($e)->toBeInstanceOf(ArrayUndefinedTypeException::class);
            } catch (Throwable $e) {
                fail("Method {$method} threw " . $e::class . ' instead of ArrayUndefinedTypeException');
            }
        }
    });

    // Test that non-throwing methods work as expected
    it('non-throwing methods return expected values', function () {
        $array = new ArrayUndefinedAbstract();

        expect($array->isEmpty())->toBeTrue()
            ->and($array->isUndefined())->toBeTrue()
            ->and($array->hasUndefined())->toBeTrue();
    });

    // Test isTypeOf method
    it('isTypeOf returns true when class matches', function () {
        $array = new ArrayUndefinedAbstract();
        expect($array->isTypeOf(ArrayUndefinedAbstract::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () {
        $array = new ArrayUndefinedAbstract();
        expect($array->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () {
        $array = new ArrayUndefinedAbstract();
        expect($array->isTypeOf('NonExistentClass', ArrayUndefinedAbstract::class, 'AnotherClass'))->toBeTrue();
    });
});

// Test ArrayUndefined in collection context
describe('ArrayUndefined in collection operations', function () {
    it('cannot be used in array functions expecting countable', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => iterator_to_array($array->getIterator()))
            ->toThrow(ArrayUndefinedTypeException::class);

        expect(fn() => \count($array))
            ->toThrow(ArrayUndefinedTypeException::class);
    });

    it('cannot be serialized to JSON', function () {
        $array = new ArrayUndefinedAbstract();

        expect(fn() => json_encode($array))
            ->toThrow(ArrayUndefinedTypeException::class);
    });

    it('represents truly undefined state unlike empty array', function () {
        $undefined = new ArrayUndefinedAbstract();
        $emptyArray = new PhpTypedValues\ArrayType\ArrayOfObjectsAbstract([]);

        // Both are empty
        expect($undefined->isEmpty())->toBeTrue()
            ->and($emptyArray->isEmpty())->toBeTrue();

        // But only undefined is... undefined
        expect($undefined->isUndefined())->toBeTrue()
            ->and($emptyArray->isUndefined())->toBeFalse();

        // And undefined throws on access
        expect(fn() => $undefined->value())
            ->toThrow(ArrayUndefinedTypeException::class);

        // While empty array returns empty array
        expect($emptyArray->value())->toBe([]);
    });
});
