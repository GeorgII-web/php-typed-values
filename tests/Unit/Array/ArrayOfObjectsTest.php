<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayOfObjects;
use PhpTypedValues\ArrayType\ArrayUndefined;
use PhpTypedValues\Exception\ArrayType\ArrayTypeException;
use PhpTypedValues\Float\Alias\Positive;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('ArrayOfObjects', function () {
    describe('Creation', function () {
        describe('fromArray', function () {
            it('constructs from a valid list of objects and preserves order', function () {
                $i1 = IntegerNonNegative::fromInt(1);
                $s1 = StringNonEmpty::fromString('A');

                $c = ArrayOfObjects::fromArray([$i1, $s1]);

                expect($c->value())
                    ->toHaveCount(2)
                    ->and($c->value()[0])->toBe($i1)
                    ->and($c->value()[1])->toBe($s1);
            });

            it('throws when any item is not an object', function () {
                expect(fn() => ArrayOfObjects::fromArray([1, new stdClass()]))
                    ->toThrow(ArrayTypeException::class, 'Expected array of Object instances');
            });
        });

        describe('fromItems', function () {
            it('creates instance from variadic arguments', function () {
                $obj1 = new stdClass();
                $obj2 = new class {
                    public string $test = 'value';
                };
                $obj3 = Positive::fromFloat(5.0);

                $array = ArrayOfObjects::fromItems($obj1, $obj2, $obj3);

                expect($array)->toBeInstanceOf(ArrayOfObjects::class)
                    ->and($array->count())->toBe(3)
                    ->and($array->value())->toBe([$obj1, $obj2, $obj3]);
            });
        });

        describe('tryFromArray', function () {
            it('returns instance for valid object array', function () {
                $items = [new stdClass(), new stdClass()];
                $result = ArrayOfObjects::tryFromArray($items);

                expect($result)->toBeInstanceOf(ArrayOfObjects::class)
                    ->and($result->count())->toBe(2);
            });

            it('returns default for invalid array', function () {
                $default = new ArrayOfObjects([new stdClass()]);
                $invalidArray = ['not-an-object', 123];

                $result = ArrayOfObjects::tryFromArray($invalidArray, $default);

                expect($result)->toBe($default);
            });

            it('returns ArrayUndefined when no custom default provided for invalid input', function () {
                expect(ArrayOfObjects::tryFromArray(['not-an-object']))
                    ->toBeInstanceOf(ArrayUndefined::class);
            });
        });

        describe('Constructor', function () {
            it('throws when array contains non-objects', function () {
                expect(fn() => new ArrayOfObjects(['string', 123, new stdClass()]))
                    ->toThrow(ArrayTypeException::class, 'Object instances');
            });
        });
    });

    describe('Collection Methods', function () {
        it('isEmpty() returns correct boolean', function (array $input, bool $expected) {
            $c = new ArrayOfObjects($input);
            expect($c->isEmpty())->toBe($expected);
        })->with([
            'empty' => [[], true],
            'not empty' => [[new stdClass()], false],
        ]);

        it('count() returns correct number of items', function (array $input, int $expected) {
            $c = new ArrayOfObjects($input);
            expect($c->count())->toBe($expected);
        })->with([
            'empty' => [[], 0],
            'three items' => [[new stdClass(), new stdClass(), new stdClass()], 3],
        ]);

        it('getIterator() yields all items in order', function () {
            $items = [new stdClass(), new stdClass(), new stdClass()];
            $array = new ArrayOfObjects($items);

            $iterated = [];
            foreach ($array as $key => $value) {
                $iterated[$key] = $value;
            }

            expect($iterated)->toBe($items)
                ->and(array_keys($iterated))->toBe([0, 1, 2]);
        });

        it('getDefinedItems() returns only non-Undefined objects', function () {
            $items = [
                new stdClass(),
                new Undefined(),
                new class {
                    public string $prop = 'value';
                },
                new Undefined(),
                Positive::fromFloat(1.0),
            ];

            $array = new ArrayOfObjects($items);
            $definedItems = $array->getDefinedItems();

            expect($definedItems)->toHaveCount(3)
                ->and($definedItems[0])->toBe($items[0])
                ->and($definedItems[1])->toBe($items[2])
                ->and($definedItems[2])->toBe($items[4]);

            foreach ($definedItems as $item) {
                expect($item)->not->toBeInstanceOf(Undefined::class);
            }
        });

        it('hasUndefined() detects presence of Undefined items', function (array $items, bool $expected) {
            $c = new ArrayOfObjects($items);
            expect($c->hasUndefined())->toBe($expected);
        })->with([
            'no Undefined' => [[new stdClass(), new stdClass()], false],
            'some Undefined' => [[new stdClass(), new Undefined(), new stdClass()], true],
            'all Undefined' => [[new Undefined(), new Undefined()], true],
        ]);

        it('isUndefined() returns true only when non-empty and all items are Undefined', function (array $items, bool $expected) {
            $c = new ArrayOfObjects($items);
            expect($c->isUndefined())->toBe($expected);
        })->with([
            'empty' => [[], false],
            'all Undefined' => [[new Undefined(), new Undefined()], true],
            'mixed' => [[new stdClass(), new Undefined()], false],
            'none Undefined' => [[new stdClass()], false],
        ]);
    });

    describe('Instance Methods', function () {
        it('value() returns internal array', function () {
            $original = [new stdClass(), new Undefined(), Positive::fromFloat(2.0)];
            $array = new ArrayOfObjects($original);

            expect($array->value())->toBe($original);
        });

        it('toArray() and jsonSerialize() return array of scalars', function () {
            $i1 = IntegerNonNegative::fromInt(3);
            $s1 = StringNonEmpty::fromString('Z');
            $c = ArrayOfObjects::fromArray([$i1, $s1]);

            $expected = [3, 'Z'];
            expect($c->toArray())->toBe($expected)
                ->and($c->jsonSerialize())->toBe($expected);
        });

        it('toArray() throws when an item is not JsonSerializable', function () {
            $obj = new stdClass();
            $c = new ArrayOfObjects([$obj]);
            expect(fn() => $c->toArray())
                ->toThrow(ArrayTypeException::class, 'Conversion to array of Scalars failed, should implement JsonSerializable interface');
        });

        it('jsonSerialize() calls toArray()', function () {
            $item = new class implements JsonSerializable {
                public function jsonSerialize(): mixed
                {
                    return 'serialized';
                }
            };
            $array = new ArrayOfObjects([$item]);

            expect($array->jsonSerialize())->toBe($array->toArray())
                ->and($array->jsonSerialize())->toBe(['serialized']);
        });

        it('isTypeOf() returns true when class matches', function () {
            $array = new ArrayOfObjects([new stdClass()]);
            expect($array->isTypeOf(ArrayOfObjects::class))->toBeTrue();
        });

        it('isTypeOf() returns false when class does not match', function () {
            $array = new ArrayOfObjects([new stdClass()]);
            expect($array->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf() returns true for multiple classNames when one matches', function () {
            $array = new ArrayOfObjects([new stdClass()]);
            expect($array->isTypeOf('NonExistentClass', ArrayOfObjects::class, 'AnotherClass'))->toBeTrue();
        });

        it('isTypeOf() returns false for empty classNames', function () {
            $array = new ArrayOfObjects([new stdClass()]);
            expect($array->isTypeOf())->toBeFalse();
        });

        it('isTypeOf() returns false when none match', function () {
            $array = new ArrayOfObjects([new stdClass()]);
            expect($array->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
        });
    });

    describe('Immutability and Stability', function () {
        it('does not mutate internal state across calls', function () {
            $i1 = IntegerNonNegative::fromInt(5);
            $i2 = IntegerNonNegative::fromInt(7);
            $c = ArrayOfObjects::fromArray([$i1, $i2]);

            $v1 = $c->value();
            $v2 = $c->value();
            expect($v1)->toBe($v2)
                ->and($c->toArray())->toBe($c->toArray());
        });
    });
});
