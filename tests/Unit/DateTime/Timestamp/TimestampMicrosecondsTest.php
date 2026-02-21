<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\DateTime\Timestamp;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\DateTime\Timestamp\TimestampMicroseconds;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

describe('TimestampMicroseconds', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('preserves instant and renders microseconds', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05.678901+00:00');
                $vo = TimestampMicroseconds::fromDateTime($dt);

                // Expected microseconds: 1735787045 seconds and 678901 micros
                expect($vo->value()->format('U'))->toBe('1735787045')
                    ->and($vo->toString())->toBe('1735787045678901')
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
            });

            it('normalizes timezone to UTC while preserving the instant', function () {
                // Source datetime has +03:00 offset, should be normalized to UTC internally
                $dt = new DateTimeImmutable('2025-01-02T03:04:05.123456+03:00');
                $vo = TimestampMicroseconds::fromDateTime($dt);

                // Unix timestamp in seconds must be equal regardless of timezone
                expect($vo->value()->format('U'))->toBe($dt->format('U'))
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC')
                    ->and($vo->toString())->toBe((string) ((int) $dt->format('U') * 1000000 + (int) $dt->format('u')));
            });
        });

        describe('fromInt', function () {
            it('creates instance from integer', function () {
                // 1732445696123456
                $vo = TimestampMicroseconds::fromInt(1732445696123456);
                expect($vo->value()->format('U.u'))->toBe('1732445696.123456')
                    ->and($vo->toString())->toBe('1732445696123456');
            });
        });

        describe('fromString', function () {
            it('creates instance from valid microseconds string', function (string $input, string $expectedU, string $expectedUu) {
                $vo = TimestampMicroseconds::fromString($input);
                expect($vo->toString())->toBe($input)
                    ->and($vo->value()->format('U'))->toBe($expectedU)
                    ->and($vo->value()->format('U.u'))->toBe($expectedUu);
            })->with([
                'standard' => ['1000000000000000', '1000000000', '1000000000.000000'],
                'with remainder' => ['1732445696123456', '1732445696', '1732445696.123456'],
                '999999 remainder' => ['1732445696999999', '1732445696', '1732445696.999999'],
                'zero' => ['0', '0', '0.000000'],
                'short' => ['123', '0', '0.000123'],
            ]);

            it('throws exception on invalid input', function (string $input, string $exception, string $messagePart) {
                expect(fn() => TimestampMicroseconds::fromString($input))
                    ->toThrow($exception, $messagePart);
            })->with([
                'non-numeric' => ['not-a-number', DateTimeTypeException::class, 'Expected microseconds timestamp as digits'],
            ]);
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = TimestampMicroseconds::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(TimestampMicroseconds::class)
                        ->and($result->toString())->toBe($input);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['1732445696123456', true],
                'invalid' => ['abc', false],
            ]);

            it('accepts custom timezone', function () {
                $s = '1732445696123456';
                $vo = TimestampMicroseconds::tryFromString($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(TimestampMicroseconds::class)
                    ->and($vo->toString())->toBe($s)
                    ->and($vo->value()->getOffset())->toBe(0);
            });
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = TimestampMicroseconds::tryFromMixed($input);
                expect($result)->toBeInstanceOf(TimestampMicroseconds::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'string' => ['1732445696123456', '1732445696123456'],
                'integer' => [1732445696123456, '1732445696123456'],
                'Stringable' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '1732445696123456';
                        }
                    },
                    '1732445696123456',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = TimestampMicroseconds::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [['x']],
                'object' => [new stdClass()],
                'DateTimeImmutable' => [new DateTimeImmutable()],
            ]);

            it('accepts custom timezone', function () {
                $s = '1732445696123456';
                $vo = TimestampMicroseconds::tryFromMixed($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(TimestampMicroseconds::class)
                    ->and($vo->toString())->toBe($s)
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('kills mutants in tryFromMixed', function () {
                $customDefault = Undefined::create();

                // Kill InstanceOfToTrue for Stringable by passing non-stringable
                expect(TimestampMicroseconds::tryFromMixed([], 'UTC', $customDefault))
                    ->toBe($customDefault);

                // Kill RemoveStringCast for Stringable
                $stringable = new class implements Stringable {
                    public function __toString(): string
                    {
                        return '1732445696123456';
                    }
                };
                expect(TimestampMicroseconds::tryFromMixed($stringable, 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampMicroseconds::class);

                // Kill InstanceOfToTrue for Stringable in tryFromMixed (by passing integer which is not string/stringable)
                expect(TimestampMicroseconds::tryFromMixed(123456, 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampMicroseconds::class);
            });
        });

        describe('getFormat', function () {
            it('returns internal pattern', function () {
                expect(TimestampMicroseconds::getFormat())->toBe('U.u');
            });
        });
    });

    describe('Instance Methods', function () {
        it('value() returns internal DateTimeImmutable (normalized to UTC)', function () {
            $vo = TimestampMicroseconds::fromString('1732445696123456');
            expect($vo->value()->format('U.u'))->toBe('1732445696.123456')
                ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
        });

        it('toString and __toString return microseconds string', function () {
            $s = '1732445696123456';
            $vo = TimestampMicroseconds::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s)
                ->and($vo->__toString())->toBe($s);
        });

        it('jsonSerialize and toInt return integer', function () {
            $vo = TimestampMicroseconds::fromString('1732445696123456');
            expect($vo->jsonSerialize())->toBe(1732445696123456)
                ->and($vo->toInt())->toBe(1732445696123456);
        });

        it('isEmpty is always false', function () {
            $vo = TimestampMicroseconds::fromString('0');
            expect($vo->isEmpty())->toBeFalse();

            // Kills the FalseToTrue mutant in isEmpty
            if ($vo->isEmpty() !== false) {
                throw new Exception('isEmpty mutant!');
            }
        });

        it('isUndefined is always false', function () {
            $vo = TimestampMicroseconds::fromString('0');
            expect($vo->isUndefined())->toBeFalse();

            // Kills the FalseToTrue mutant in isUndefined
            if ($vo->isUndefined() !== false) {
                throw new Exception('isUndefined mutant!');
            }
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = TimestampMicroseconds::fromString('1732445696123456');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(TimestampMicroseconds::class)
                    ->and($vo2->toString())->toBe('1732445696123456')
                    ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');

                // original is immutable
                expect($vo->toString())->toBe('1732445696123456');
            });
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = TimestampMicroseconds::fromString('1732445696123456');
                expect($vo->isTypeOf(TimestampMicroseconds::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = TimestampMicroseconds::fromString('1732445696123456');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = TimestampMicroseconds::fromString('1732445696123456');
                expect($vo->isTypeOf('NonExistentClass', TimestampMicroseconds::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = TimestampMicroseconds::fromString('1732445696123456');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = TimestampMicroseconds::fromString('1732445696123456');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
