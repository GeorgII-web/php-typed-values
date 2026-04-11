<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(Undefined::class);

describe('Undefined', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = Undefined::create();
            expect($u)->toBeInstanceOf(Undefined::class);
        });

        it('throws via fromString factory', function (): void {
            expect(fn() => Undefined::fromString('anything'))
                ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be created from string');
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = Undefined::tryFromMixed('hello');
            $fromInt = Undefined::tryFromMixed(123);
            $fromNull = Undefined::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromInt)
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
            'tryFromFloat' => ['tryFromFloat', 1.1],
            'tryFromBool' => ['tryFromBool', true],
        ]);

        it('tryFromString always returns Undefined', function (): void {
            $v = Undefined::tryFromString('anything');

            expect($v)->toBeInstanceOf(Undefined::class);
        });

        it('tryFrom"Any" methods return default value on failure', function (string $method, mixed $input): void {
            $default = StringStandard::fromString('default');
            $result = Undefined::$method($input, $default);

            expect($result)->toBe($default);
        })->with([
            'tryFromString' => ['tryFromString', 'hello'],
            'tryFromInt' => ['tryFromInt', 123],
            'tryFromFloat' => ['tryFromFloat', 1.1],
            'tryFromBool' => ['tryFromBool', true],
        ]);

        it('tryFromMixed returns default value on failure', function (): void {
            $default = StringStandard::fromString('default');
            $result = Undefined::tryFromMixed('hello', $default);

            expect($result)->toBe($default);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = Undefined::create();
            $expect = expect(fn() => $u->{$method}());

            $message = match ($method) {
                'toString' => 'Undefined type cannot be converted to string',
                '__toString' => 'Undefined type cannot be converted to string',
                'toInt' => 'Undefined type cannot be converted to integer',
                'toFloat' => 'Undefined type cannot be converted to float',
                'toArray' => 'Undefined type cannot be converted to array',
                'value' => 'Undefined type has no value',
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
        ]);
    });

    describe('Information', function () {
        it('isEmpty returns true', function (): void {
            $u1 = Undefined::create();
            $u2 = Undefined::create();

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = Undefined::create();
            $u2 = Undefined::create();

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
