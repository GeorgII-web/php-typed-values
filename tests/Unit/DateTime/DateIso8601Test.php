<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateIso8601;
use PhpTypedValues\Exception\DateTime\Iso8601DateTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(DateIso8601::class);

describe('DateIso8601', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('returns same date and toString is Y-m-d', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = DateIso8601::fromDateTime($dt);

                expect($vo->toString())->toBe('2025-01-02');
            });
        });

        describe('fromString', function () {
            it('parses valid Y-m-d', function () {
                $vo = DateIso8601::fromString('2030-12-31');
                expect($vo->toString())->toBe('2030-12-31');
            });

            it('throws exception on invalid input', function (string $input) {
                expect(fn() => DateIso8601::fromString($input))
                    ->toThrow(Iso8601DateTypeException::class);
            })->with([
                'invalid day' => ['2025-01-32'],
                'invalid month' => ['2025-13-01'],
                'with time' => ['2025-01-01 10:00:00'],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = DateIso8601::tryFromMixed($input);
                expect($result)->toBeInstanceOf(DateIso8601::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'valid string' => ['2025-01-01', '2025-01-01'],
                'DateTimeImmutable instance' => [new DateTimeImmutable('2025-01-01'), '2025-01-01'],
                'Stringable object' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '2025-01-01';
                        }
                    },
                    '2025-01-01',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = DateIso8601::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class);
            })->with([
                'null' => [null],
                'array' => [['x']],
                'stdClass' => [new stdClass()],
                'anonymous non-stringable' => [new class {}],
            ]);
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = DateIso8601::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(DateIso8601::class);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['2025-01-01', true],
                'invalid' => ['bad', false],
            ]);
        });

        describe('getFormat', function () {
            it('returns Y-m-d format', function () {
                expect(DateIso8601::getFormat())->toBe('Y-m-d');
            });
        });
    });

    describe('Instance Methods', function () {
        it('jsonSerialize returns string', function () {
            $s = '2025-01-01';
            expect(DateIso8601::fromString($s)->jsonSerialize())->toBe($s);
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = DateIso8601::fromString('2025-01-01');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(DateIso8601::class)
                    ->and($vo2->toString())->toBe('2025-01-01');
            });
        });
        it('toString returns Y-m-d formatted string', function () {
            $s = '2025-01-01';
            $vo = DateIso8601::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s);
        });

        it('isEmpty is always false', function () {
            $vo = DateIso8601::fromString('2025-01-01');
            expect($vo->isEmpty())->toBeFalse();
        });

        it('isUndefined is always false', function () {
            $vo = DateIso8601::fromString('2025-01-01');
            expect($vo->isUndefined())->toBeFalse();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = DateIso8601::fromString('2025-01-01');
                expect($vo->isTypeOf(DateIso8601::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = DateIso8601::fromString('2025-01-01');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = DateIso8601::fromString('2025-01-01');
                expect($vo->isTypeOf('NonExistentClass', DateIso8601::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = DateIso8601::fromString('2025-01-01');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = DateIso8601::fromString('2025-01-01');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
