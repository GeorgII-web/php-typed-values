<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotExist;

describe('NotExist', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = NotExist::create();
            expect($u)->toBeInstanceOf(NotExist::class);
        });

        it('creates instance via fromString factory', function (): void {
            $u = NotExist::fromString('anything');
            expect($u)->toBeInstanceOf(NotExist::class);
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = NotExist::tryFromMixed('hello');
            $fromInt = NotExist::tryFromMixed(123);
            $fromArray = NotExist::tryFromMixed([]);
            $fromNull = NotExist::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(NotExist::class)
                ->and($fromInt)
                ->toBeInstanceOf(NotExist::class)
                ->and($fromArray)
                ->toBeInstanceOf(NotExist::class)
                ->and($fromNull)
                ->toBeInstanceOf(NotExist::class);
        });

        it('checks tryFrom"Any" methods', function (string $method, mixed $input): void {
            $result = NotExist::$method($input);

            expect($result)->toBeInstanceOf(NotExist::class);

            if ($method === 'tryFromBool') {
                expect(fn() => $result->toBool())->toThrow(UndefinedTypeException::class);
            }
        })->with([
            'tryFromString' => ['tryFromString', 'hello'],
            'tryFromInt' => ['tryFromInt', 123],
            'tryFromArray' => ['tryFromArray', []],
            'tryFromFloat' => ['tryFromFloat', 1.1],
            'tryFromBool' => ['tryFromBool', true],
        ]);

        it('tryFromString always returns NotExist', function (): void {
            $v = NotExist::tryFromString('anything');

            expect($v)->toBeInstanceOf(NotExist::class);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = NotExist::create();
            $expect = expect(fn() => $u->{$method}());

            $message = match ($method) {
                'toString', '__toString' => 'UndefinedType cannot be converted to string.',
                'toInt' => 'UndefinedType cannot be converted to integer.',
                'toFloat' => 'UndefinedType cannot be converted to float.',
                'toArray' => 'UndefinedType cannot be converted to array.',
                'value' => 'UndefinedType has no value.',
                'jsonSerialize' => 'UndefinedType cannot be serialized for Json.',
                default => throw new RuntimeException("Unknown method: {$method}"),
            };

            $expect->toThrow(UndefinedTypeException::class, $message);
        })->with([
            'toString',
            'toInt',
            'toFloat',
            'toArray',
            'value',
            '__toString',
            'jsonSerialize',
        ]);
    });

    describe('Information', function () {
        it('isEmpty returns true', function (): void {
            $u1 = NotExist::create();
            $u2 = NotExist::fromString('ignored');

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = NotExist::create();
            $u2 = NotExist::fromString('ignored');

            expect($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $v = NotExist::create();
                expect($v->isTypeOf(NotExist::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $v = NotExist::create();
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $v = NotExist::create();
                expect($v->isTypeOf('NonExistentClass', NotExist::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
