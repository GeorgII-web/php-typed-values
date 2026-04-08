<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\ArrayType;

use PhpTypedValues\ArrayType\ArrayOfPrimitives;
use PhpTypedValues\Exception\ArrayType\PrimitivesArrayTypeException;
use PhpTypedValues\Integer\Alias\IntegerType;
use PhpTypedValues\String\Alias\StringType;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(ArrayOfPrimitives::class);

describe('ArrayOfPrimitives', function () {
    describe('Creation', function () {
        describe('fromArray', function () {
            it('constructs from a valid list of primitives and preserves order', function () {
                $i1 = IntegerType::fromInt(1);
                $s1 = StringType::fromString('A');

                $c = ArrayOfPrimitives::fromArray([$i1, $s1]);

                expect($c->value())
                    ->toHaveCount(2)
                    ->and($c->value()[0])->toBe($i1)
                    ->and($c->value()[1])->toBe($s1);
            });

            it('throws when any item is not a primitive', function () {
                expect(fn() => ArrayOfPrimitives::fromArray([1, new stdClass()]))
                    ->toThrow(PrimitivesArrayTypeException::class, 'Expected array of PrimitiveTypeInterface instances');
            });
        });

        describe('fromItems', function () {
            it('creates instance from variadic arguments', function () {
                $i1 = IntegerType::fromInt(1);
                $s1 = StringType::fromString('A');

                $array = ArrayOfPrimitives::fromItems($i1, $s1);

                expect($array)->toBeInstanceOf(ArrayOfPrimitives::class)
                    ->and($array->count())->toBe(2)
                    ->and($array->value())->toBe([$i1, $s1]);
            });
        });
    });

    describe('Collection Methods', function () {
        it('isEmpty() returns correct boolean', function (array $input, bool $expected) {
            $c = new ArrayOfPrimitives($input);
            expect($c->isEmpty())->toBe($expected);
        })->with([
            'empty' => [[], true],
            'not empty' => [[IntegerType::fromInt(1)], false],
        ]);

        it('count() returns correct number of items', function (array $input, int $expected) {
            $c = new ArrayOfPrimitives($input);
            expect($c->count())->toBe($expected);
        })->with([
            'empty' => [[], 0],
            'three items' => [[IntegerType::fromInt(1), IntegerType::fromInt(2), IntegerType::fromInt(3)], 3],
        ]);
    });

    describe('Undefined Handling', function () {
        it('hasUndefined() detects undefined', function () {
            $array = new ArrayOfPrimitives([IntegerType::fromInt(1), Undefined::create()]);
            expect($array->hasUndefined())->toBeTrue();
        });

        it('hasUndefined() returns false for no undefined', function () {
            $array = new ArrayOfPrimitives([IntegerType::fromInt(1)]);
            expect($array->hasUndefined())->toBeFalse();
        });

        it('isUndefined() returns true for all undefined items', function () {
            $array = new ArrayOfPrimitives([Undefined::create(), Undefined::create()]);
            expect($array->isUndefined())->toBeTrue();
        });

        it('isUndefined() returns false for empty array', function () {
            $array = new ArrayOfPrimitives([]);
            expect($array->isUndefined())->toBeFalse();
        });

        it('isUndefined() returns false for mixed undefined and defined', function () {
            $array = new ArrayOfPrimitives([IntegerType::fromInt(1), Undefined::create()]);
            expect($array->isUndefined())->toBeFalse();
        });
    });

    describe('Accessors', function () {
        it('getDefinedItems() returns only defined items', function () {
            $items = [IntegerType::fromInt(1), Undefined::create(), IntegerType::fromInt(2)];
            $array = new ArrayOfPrimitives($items);

            expect($array->getDefinedItems())->toHaveCount(2)
                ->and($array->getDefinedItems()[0])->toBe($items[0])
                ->and($array->getDefinedItems()[1])->toBe($items[2]);
        });

        it('getIterator() iterates over all items', function () {
            $items = [IntegerType::fromInt(1), IntegerType::fromInt(2)];
            $array = new ArrayOfPrimitives($items);
            $iterated = [];
            foreach ($array as $item) {
                $iterated[] = $item;
            }
            expect($iterated)->toBe($items);
        });

        it('isTypeOf() returns true for current class', function () {
            $array = new ArrayOfPrimitives([]);
            expect($array->isTypeOf(ArrayOfPrimitives::class))->toBeTrue();
        });

        it('isTypeOf() returns false for unknown class', function () {
            $array = new ArrayOfPrimitives([]);
            expect($array->isTypeOf(stdClass::class))->toBeFalse();
        });

        it('toArray() and jsonSerialize() return array representation', function () {
            $i1 = IntegerType::fromInt(1);
            $i2 = IntegerType::fromInt(2);
            $array = new ArrayOfPrimitives([$i1, $i2]);

            expect($array->toArray())->toBe([1, 2])
                ->and($array->jsonSerialize())->toBe([1, 2]);
        });
    });
});
