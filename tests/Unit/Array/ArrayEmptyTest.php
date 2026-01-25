<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayEmpty;
use PhpTypedValues\ArrayType\ArrayUndefined;
use PhpTypedValues\Exception\ArrayType\ArrayTypeException;

covers(ArrayEmpty::class);

describe('ArrayEmpty', function () {
    describe('Creation', function () {
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

        describe('tryFromArray', function () {
            it('constructs from an empty array', function (): void {
                $c = ArrayEmpty::tryFromArray([]);
                expect($c->value())->toBe([]);
            });

            it('returns default for non-empty array', function () {
                $default = new ArrayUndefined();
                expect(ArrayEmpty::tryFromArray([1], $default))->toBe($default);
            });
        });
    });

    describe('Collection Methods', function () {
        it('getIterator yields nothing', function (): void {
            $c = new ArrayEmpty([]);
            $yielded = iterator_to_array($c->getIterator());
            expect($yielded)->toBe([]);
        });

        it('isEmpty returns true', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->isEmpty())->toBeTrue();
        });

        it('count returns 0', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->count())->toBe(0);
        });

        it('isUndefined returns false', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->isUndefined())->toBeFalse();
        });

        it('hasUndefined returns false', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->hasUndefined())->toBeFalse();
        });

        it('getDefinedItems returns empty array', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->getDefinedItems())->toBe([]);
        });
    });

    describe('Instance Methods', function () {
        it('value returns empty array', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->value())->toBe([]);
        });

        it('toArray returns empty array', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->toArray())->toBe([]);
        });

        it('jsonSerialize returns empty array', function (): void {
            $c = new ArrayEmpty([]);
            expect($c->jsonSerialize())->toBe([]);
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $c = new ArrayEmpty([]);
                expect($c->isTypeOf(ArrayEmpty::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $c = new ArrayEmpty([]);
                expect($c->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $c = new ArrayEmpty([]);
                expect($c->isTypeOf('NonExistentClass', ArrayEmpty::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false when none match', function () {
                $c = new ArrayEmpty([]);
                expect($c->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $c = new ArrayEmpty([]);
                expect($c->isTypeOf())->toBeFalse();
            });
        });
    });
});
