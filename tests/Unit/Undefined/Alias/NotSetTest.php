<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotSet;

describe('NotSet', function () {
    describe('Creation', function () {
        it('creates instance via factory', function (): void {
            $u = NotSet::create();
            expect($u)->toBeInstanceOf(NotSet::class);
        });

        it('creates instance via fromString factory', function (): void {
            $u = NotSet::fromString('anything');
            expect($u)->toBeInstanceOf(NotSet::class);
        });

        it('tryFromMixed always returns itself', function (): void {
            $fromString = NotSet::tryFromMixed('hello');
            $fromInt = NotSet::tryFromMixed(123);
            $fromArray = NotSet::tryFromMixed([]);
            $fromNull = NotSet::tryFromMixed(null);

            expect($fromString)
                ->toBeInstanceOf(NotSet::class)
                ->and($fromInt)
                ->toBeInstanceOf(NotSet::class)
                ->and($fromArray)
                ->toBeInstanceOf(NotSet::class)
                ->and($fromNull)
                ->toBeInstanceOf(NotSet::class);
        });

        it('checks tryFrom"Any" methods', function (string $method, mixed $input): void {
            $result = NotSet::$method($input);

            expect($result)->toBeInstanceOf(NotSet::class);

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

        it('tryFromString always returns NotSet', function (): void {
            $v = NotSet::tryFromString('anything');

            expect($v)->toBeInstanceOf(NotSet::class);
        });
    });

    describe('Conversions (Failure Cases)', function () {
        it('throws on conversion methods', function (string $method): void {
            $u = NotSet::create();
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
            $u1 = NotSet::create();
            $u2 = NotSet::fromString('ignored');

            expect($u1->isEmpty())->toBeTrue()
                ->and($u2->isEmpty())->toBeTrue();
        });

        it('isUndefined returns true', function (): void {
            $u1 = NotSet::create();
            $u2 = NotSet::fromString('ignored');

            expect($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function (): void {
                $v = NotSet::create();
                expect($v->isTypeOf(NotSet::class))->toBeTrue();
            });

            it('returns false when class does not match', function (): void {
                $v = NotSet::create();
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function (): void {
                $v = NotSet::create();
                expect($v->isTypeOf('NonExistentClass', NotSet::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
