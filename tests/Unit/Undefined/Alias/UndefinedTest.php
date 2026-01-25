<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('Undefined', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = Undefined::create();
            expect($u)->toBeInstanceOf(Undefined::class);
        });

        it('creates instance via fromString factory', function (): void {
            $u = Undefined::fromString('anything');
            expect($u)->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = Undefined::tryFromMixed('hello');
            $fromInt = Undefined::tryFromMixed(123);
            $fromArray = Undefined::tryFromMixed([]);
            $fromNull = Undefined::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromInt)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromArray)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromNull)
                ->toBeInstanceOf(Undefined::class);
        });

        it('checks tryFrom"Any" methods', function (string $method, mixed $input): void {
            $result = Undefined::$method($input);

            expect($result)->toBeInstanceOf(Undefined::class);

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

        it('tryFromString always returns Undefined', function (): void {
            $v = Undefined::tryFromString('anything');

            expect($v)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = Undefined::create();
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
            $u1 = Undefined::create();
            $u2 = Undefined::fromString('ignored');

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = Undefined::create();
            $u2 = Undefined::fromString('ignored');

            expect($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $v = Undefined::create();
                expect($v->isTypeOf(Undefined::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $v = Undefined::create();
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $v = Undefined::create();
                expect($v->isTypeOf('NonExistentClass', Undefined::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
