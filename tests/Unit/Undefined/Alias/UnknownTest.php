<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Unknown;

describe('Unknown', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = Unknown::create();
            expect($u)->toBeInstanceOf(Unknown::class);
        });

        it('creates instance via fromString factory', function (): void {
            $u = Unknown::fromString('anything');
            expect($u)->toBeInstanceOf(Unknown::class);
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = Unknown::tryFromMixed('hello');
            $fromInt = Unknown::tryFromMixed(123);
            $fromArray = Unknown::tryFromMixed([]);
            $fromNull = Unknown::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(Unknown::class)
                ->and($fromInt)
                ->toBeInstanceOf(Unknown::class)
                ->and($fromArray)
                ->toBeInstanceOf(Unknown::class)
                ->and($fromNull)
                ->toBeInstanceOf(Unknown::class);
        });

        it('checks tryFrom"Any" methods', function (string $method, mixed $input): void {
            $result = Unknown::$method($input);

            expect($result)->toBeInstanceOf(Unknown::class);

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

        it('tryFromString always returns Unknown', function (): void {
            $v = Unknown::tryFromString('anything');

            expect($v)->toBeInstanceOf(Unknown::class);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = Unknown::create();
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
            $u1 = Unknown::create();
            $u2 = Unknown::fromString('ignored');

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = Unknown::create();
            $u2 = Unknown::fromString('ignored');

            expect($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $v = Unknown::create();
                expect($v->isTypeOf(Unknown::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $v = Unknown::create();
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $v = Unknown::create();
                expect($v->isTypeOf('NonExistentClass', Unknown::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
