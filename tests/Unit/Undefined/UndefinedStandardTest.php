<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\UndefinedStandard;

covers(UndefinedStandard::class);

describe('UndefinedStandard', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = UndefinedStandard::create();
            expect($u)->toBeInstanceOf(UndefinedStandard::class);
        });

        it('creates instance via fromString factory', function (): void {
            expect(fn() => UndefinedStandard::fromString('anything'))
                ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be created from string');
        });

        it('creates instance via fromNull factory', function (): void {
            $u = UndefinedStandard::fromNull(null);
            expect($u)->toBeInstanceOf(UndefinedStandard::class);
        });

        it('throws via fromDecimal factory', function (): void {
            expect(fn() => UndefinedStandard::fromDecimal('1.23'))
                ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be created from decimal');
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = UndefinedStandard::tryFromMixed('hello');
            $fromInt = UndefinedStandard::tryFromMixed(123);
            $fromDecimal = UndefinedStandard::tryFromDecimal('1.23');
            $fromArray = UndefinedStandard::tryFromMixed([]);
            $fromNull = UndefinedStandard::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(UndefinedStandard::class)
                ->and($fromInt)
                ->toBeInstanceOf(UndefinedStandard::class)
                ->and($fromDecimal)
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
            'tryFromDecimal' => ['tryFromDecimal', '1.23'],
            'tryFromFloat' => ['tryFromFloat', 1.1],
            'tryFromBool' => ['tryFromBool', true],
        ]);

        it('tryFromString always returns Undefined', function (): void {
            $v = UndefinedStandard::tryFromString('anything');

            expect($v)->toBeInstanceOf(UndefinedStandard::class);
        });

        it('tryFrom"Any" methods return default value on failure', function (string $method, mixed $input): void {
            $default = StringStandard::fromString('default');
            $result = UndefinedStandard::$method($input, $default);

            expect($result)->toBe($default);
        })->with([
            'tryFromString' => ['tryFromString', 'hello'],
            'tryFromInt' => ['tryFromInt', 123],
            'tryFromDecimal' => ['tryFromDecimal', '1.23'],
            'tryFromFloat' => ['tryFromFloat', 1.1],
            'tryFromBool' => ['tryFromBool', true],
        ]);

        it('tryFromMixed returns default value on failure', function (): void {
            $default = StringStandard::fromString('default');
            $result = UndefinedStandard::tryFromMixed('hello', $default);

            expect($result)->toBe($default);
        });
    });

    describe('Conversions (Success Cases)', function () {
        it('toNull returns null', function (): void {
            $u = UndefinedStandard::create();
            expect($u->toNull())->toBeNull();
        });

        it('jsonSerialize returns null', function (): void {
            $u = UndefinedStandard::create();
            expect($u->jsonSerialize())->toBeNull();
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = UndefinedStandard::create();
            $expect = expect(fn() => $u->{$method}());

            $message = match ($method) {
                'toString' => 'Undefined type cannot be converted to string',
                '__toString' => 'Undefined type cannot be converted to string',
                'toDecimal' => 'Undefined type cannot be converted to decimal',
                'toInt' => 'Undefined type cannot be converted to integer',
                'toFloat' => 'Undefined type cannot be converted to float',
                'toArray' => 'Undefined type cannot be converted to array',
                'value' => 'Undefined type has no value',
                default => throw new RuntimeException("Unknown method: {$method}"),
            };

            $expect->toThrow(UndefinedTypeException::class, $message);
        })->with([
            'toString',
            'toDecimal',
            'toInt',
            'toFloat',
            'toArray',
            'value',
            '__toString',
        ]);
    });

    describe('Information', function () {
        it('isEmpty returns true', function (): void {
            $u1 = UndefinedStandard::create();
            $u2 = UndefinedStandard::create();

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = UndefinedStandard::create();
            $u2 = UndefinedStandard::create();

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
