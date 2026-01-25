<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\UndefinedStandard;

describe('UndefinedStandard', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = UndefinedStandard::create();
            expect($u)->toBeInstanceOf(UndefinedStandard::class);
        });

        it('creates instance via fromString factory', function (): void {
            $u = UndefinedStandard::fromString('anything');
            expect($u)->toBeInstanceOf(UndefinedStandard::class);
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = UndefinedStandard::tryFromMixed('hello');
            $fromInt = UndefinedStandard::tryFromMixed(123);
            $fromArray = UndefinedStandard::tryFromMixed([]);
            $fromNull = UndefinedStandard::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(UndefinedStandard::class)
                ->and($fromInt)
                ->toBeInstanceOf(UndefinedStandard::class)
                ->and($fromArray)
                ->toBeInstanceOf(UndefinedStandard::class)
                ->and($fromNull)
                ->toBeInstanceOf(UndefinedStandard::class);
        });

        it('checks tryFrom"Any" methods', function (string $method, mixed $input): void {
            $result = UndefinedStandard::$method($input);

            expect($result)->toBeInstanceOf(UndefinedStandard::class);

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
            $v = UndefinedStandard::tryFromString('anything');

            expect($v)->toBeInstanceOf(UndefinedStandard::class);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = UndefinedStandard::create();
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
            $u1 = UndefinedStandard::create();
            $u2 = UndefinedStandard::fromString('ignored');

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = UndefinedStandard::create();
            $u2 = UndefinedStandard::fromString('ignored');

            expect($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $v = UndefinedStandard::create();
                expect($v->isTypeOf(UndefinedStandard::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $v = UndefinedStandard::create();
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $v = UndefinedStandard::create();
                expect($v->isTypeOf('NonExistentClass', UndefinedStandard::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
