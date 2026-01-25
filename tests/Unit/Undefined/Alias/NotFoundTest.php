<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotFound;

describe('NotFound', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = NotFound::create();
            expect($u)->toBeInstanceOf(NotFound::class);
        });

        it('creates instance via fromString factory', function (): void {
            $u = NotFound::fromString('anything');
            expect($u)->toBeInstanceOf(NotFound::class);
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = NotFound::tryFromMixed('hello');
            $fromInt = NotFound::tryFromMixed(123);
            $fromArray = NotFound::tryFromMixed([]);
            $fromNull = NotFound::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(NotFound::class)
                ->and($fromInt)
                ->toBeInstanceOf(NotFound::class)
                ->and($fromArray)
                ->toBeInstanceOf(NotFound::class)
                ->and($fromNull)
                ->toBeInstanceOf(NotFound::class);
        });

        it('checks tryFrom"Any" methods', function (string $method, mixed $input): void {
            $result = NotFound::$method($input);

            expect($result)->toBeInstanceOf(NotFound::class);

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

        it('tryFromString always returns NotFound', function (): void {
            $v = NotFound::tryFromString('anything');

            expect($v)->toBeInstanceOf(NotFound::class);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = NotFound::create();
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
            $u1 = NotFound::create();
            $u2 = NotFound::fromString('ignored');

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = NotFound::create();
            $u2 = NotFound::fromString('ignored');

            expect($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $v = NotFound::create();
                expect($v->isTypeOf(NotFound::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $v = NotFound::create();
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $v = NotFound::create();
                expect($v->isTypeOf('NonExistentClass', NotFound::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
