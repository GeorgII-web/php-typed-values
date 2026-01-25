<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayNonEmpty;
use PhpTypedValues\ArrayType\ArrayUndefined;
use PhpTypedValues\Exception\ArrayType\ArrayTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('ArrayNonEmpty', function () {
    describe('Creation', function () {
        describe('Constructor', function () {
            it('throws ArrayTypeException when constructed with an empty array', function () {
                new ArrayNonEmpty([]);
            })->throws(ArrayTypeException::class, 'Expected non-empty array');

            it('accepts a non-empty array of objects', function () {
                $items = [new stdClass(), new stdClass()];
                $vo = new ArrayNonEmpty($items);

                expect($vo->value())->toBe($items)
                    ->and($vo->count())->toBe(2);
            });
        });

        describe('fromArray', function () {
            it('creates instance from valid array', function () {
                $items = [new stdClass()];
                $vo = ArrayNonEmpty::fromArray($items);

                expect($vo)->toBeInstanceOf(ArrayNonEmpty::class)
                    ->and($vo->value())->toBe($items);
            });

            it('throws exception on empty array', function () {
                ArrayNonEmpty::fromArray([]);
            })->throws(ArrayTypeException::class, 'Expected non-empty array');
        });

        describe('tryFromArray', function () {
            it('returns instance for valid array', function () {
                $items = [new stdClass()];
                $vo = ArrayNonEmpty::tryFromArray($items);

                expect($vo)->toBeInstanceOf(ArrayNonEmpty::class)
                    ->and($vo->value())->toBe($items);
            });

            it('returns default value on failure', function () {
                $default = new ArrayUndefined();
                expect(ArrayNonEmpty::tryFromArray([], $default))->toBe($default);
            });

            it('returns ArrayUndefined by default on failure', function () {
                expect(ArrayNonEmpty::tryFromArray([]))->toBeInstanceOf(ArrayUndefined::class);
            });
        });
    });

    describe('Collection Methods', function () {
        it('count() returns correct number of items', function (array $items, int $expected) {
            $vo = new ArrayNonEmpty($items);
            expect($vo->count())->toBe($expected);
        })->with([
            'one item' => [[new stdClass()], 1],
            'two items' => [[new stdClass(), new stdClass()], 2],
        ]);

        it('getIterator() yields all items in order', function () {
            $items = [new stdClass(), new stdClass()];
            $vo = new ArrayNonEmpty($items);

            $collected = [];
            foreach ($vo as $item) {
                $collected[] = $item;
            }

            expect($collected)->toBe($items);
        });

        it('isEmpty() returns correct boolean', function () {
            $vo = new ArrayNonEmpty([new stdClass()]);
            expect($vo->isEmpty())->toBeFalse();
        });

        it('isEmpty returns true if empty (unreachable via constructor)', function () {
            $vo = (new ReflectionClass(ArrayNonEmpty::class))->newInstanceWithoutConstructor();
            $reflectionProperty = new ReflectionProperty(ArrayNonEmpty::class, 'value');
            $reflectionProperty->setValue($vo, []);

            expect($vo->isEmpty())->toBeTrue();
        });

        it('isUndefined returns false if empty (unreachable via constructor)', function () {
            $vo = (new ReflectionClass(ArrayNonEmpty::class))->newInstanceWithoutConstructor();
            $reflectionProperty = new ReflectionProperty(ArrayNonEmpty::class, 'value');
            $reflectionProperty->setValue($vo, []);

            expect($vo->isUndefined())->toBeFalse();
        });

        it('isUndefined() returns true only if all items are Undefined', function (array $items, bool $expected) {
            $vo = new ArrayNonEmpty($items);
            expect($vo->isUndefined())->toBe($expected);
        })->with([
            'all Undefined' => [[Undefined::create(), Undefined::create()], true],
            'mixed' => [[Undefined::create(), new stdClass()], false],
            'none Undefined' => [[new stdClass()], false],
        ]);

        it('hasUndefined() detects presence of Undefined items', function (array $items, bool $expected) {
            $vo = new ArrayNonEmpty($items);
            expect($vo->hasUndefined())->toBe($expected);
        })->with([
            'all Undefined' => [[Undefined::create(), Undefined::create()], true],
            'mixed' => [[Undefined::create(), new stdClass()], true],
            'none Undefined' => [[new stdClass()], false],
        ]);

        it('getDefinedItems() filters out Undefined instances and returns empty if all are Undefined', function () {
            $u1 = Undefined::create();
            $o1 = new stdClass();
            $o2 = new stdClass();

            $vo1 = new ArrayNonEmpty([$u1, $o1, $u1, $o2]);
            expect($vo1->getDefinedItems())->toBe([$o1, $o2]);

            $vo2 = new ArrayNonEmpty([$u1, $u1]);
            expect($vo2->getDefinedItems())->toBe([]);
        });
    });

    describe('Instance Methods', function () {
        it('value() returns the internal array', function () {
            $items = [new stdClass()];
            $vo = new ArrayNonEmpty($items);
            expect($vo->value())->toBe($items)
                ->and($vo->value())->not->toBe([]);
        });

        it('toArray() and jsonSerialize() return expected representations', function (array $items, array $expected) {
            $vo = new ArrayNonEmpty($items);
            expect($vo->toArray())->toBe($expected)
                ->and($vo->jsonSerialize())->toBe($expected);
        })->with([
            'scalars' => [
                ['string', 123, 1.23, true, null],
                ['string', 123, 1.23, true, null],
            ],
            'JsonSerializable' => [
                [new class implements JsonSerializable {
                    public function jsonSerialize(): string
                    {
                        return 'serialized';
                    }
                }],
                ['serialized'],
            ],
            'Stringable' => [
                [new class implements Stringable {
                    public function __toString(): string
                    {
                        return 'stringable';
                    }
                }],
                ['stringable'],
            ],
            'Mixed supported items' => [
                [
                    new class implements JsonSerializable {
                        public function jsonSerialize(): string
                        {
                            return 'json';
                        }
                    },
                    'scalar',
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return 'stringable';
                        }
                    },
                    null,
                ],
                ['json', 'scalar', 'stringable', null],
            ],
        ]);

        it('jsonSerialize returns same as toArray', function () {
            $vo = new ArrayNonEmpty([1, 'a']);
            expect($vo->jsonSerialize())->toBe($vo->toArray())
                ->and($vo->jsonSerialize())->not->toBe([]);
        });

        it('toArray continues loop after JsonSerializable or scalar', function () {
            $js = new class implements JsonSerializable {
                public function jsonSerialize(): string
                {
                    return 'json';
                }
            };
            $vo = new ArrayNonEmpty([$js, 'scalar', 'another']);
            expect($vo->toArray())->toBe(['json', 'scalar', 'another']);
        });

        it('toArray throws ArrayTypeException for unsupported types', function () {
            $item = new stdClass();
            $vo = new ArrayNonEmpty([$item]);
            expect(fn() => $vo->toArray())->toThrow(ArrayTypeException::class, 'Item of type "stdClass" cannot be converted to a scalar or JSON-serializable value.');
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = new ArrayNonEmpty([new stdClass()]);
                expect($vo->isTypeOf(ArrayNonEmpty::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = new ArrayNonEmpty([new stdClass()]);
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = new ArrayNonEmpty([new stdClass()]);
                expect($vo->isTypeOf('NonExistentClass', ArrayNonEmpty::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = new ArrayNonEmpty([new stdClass()]);
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = new ArrayNonEmpty([new stdClass()]);
                expect($vo->isTypeOf())->toBeFalse();
            });

            it('returns false if IfNegated mutant triggers', function () {
                $vo = new ArrayNonEmpty([new stdClass()]);
                // If the logic is "if (!$this instanceof $className)" it would return true for non-matching class
                expect($vo->isTypeOf('stdClass'))->toBeFalse();
            });
        });
    });
});
