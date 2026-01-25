<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\DateTime\Timestamp;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ReasonableRangeDateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

describe('TimestampMilliseconds', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('preserves instant and renders milliseconds (truncates microseconds)', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05.678+00:00');
                $vo = TimestampMilliseconds::fromDateTime($dt);

                // Expected milliseconds: 1735787045 seconds and 678 ms
                expect($vo->value()->format('U'))->toBe('1735787045')
                    ->and($vo->toString())->toBe('1735787045678')
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
            });

            it('normalizes timezone to UTC while preserving the instant', function () {
                // Source datetime has +03:00 offset, should be normalized to UTC internally
                $dt = new DateTimeImmutable('2025-01-02T03:04:05.123+03:00');
                $vo = TimestampMilliseconds::fromDateTime($dt);

                // Unix timestamp in seconds must be equal regardless of timezone
                expect($vo->value()->format('U'))->toBe($dt->format('U'))
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC')
                    // Milliseconds should reflect the source microseconds truncated to ms
                    ->and($vo->toString())->toBe((string) ((int) $dt->format('U') * 1000 + (int) ((int) $dt->format('u') / 1000)));
            });

            it('truncates microseconds using divisor 1000', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05.999999+00:00');
                $vo = TimestampMilliseconds::fromDateTime($dt);

                expect($vo->value()->format('U'))->toBe('1735787045')
                    ->and($vo->toString())->toBe('1735787045999');
            });
        });

        describe('fromInt', function () {
            it('creates instance from integer', function () {
                $vo = TimestampMilliseconds::fromInt(1732445696123);
                expect($vo->value()->format('U.u'))->toBe('1732445696.123000')
                    ->and($vo->toString())->toBe('1732445696123');
            });

            it('accepts custom timezone', function () {
                $vo = TimestampMilliseconds::fromInt(1732445696123, 'America/New_York');
                expect($vo->toString())->toBe('1732445696123')
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
            });
        });

        describe('fromString', function () {
            it('creates instance from valid milliseconds string', function (string $input, string $expectedU, string $expectedUu) {
                $vo = TimestampMilliseconds::fromString($input);
                expect($vo->toString())->toBe($input)
                    ->and($vo->value()->format('U'))->toBe($expectedU)
                    ->and($vo->value()->format('U.u'))->toBe($expectedUu);
            })->with([
                'standard' => ['1000000000000', '1000000000', '1000000000.000000'],
                'with remainder' => ['1732445696123', '1732445696', '1732445696.123000'],
                '999 remainder' => ['1732445696999', '1732445696', '1732445696.999000'],
                'zero' => ['0', '0', '0.000000'],
            ]);

            it('accepts custom timezone', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123', 'Europe/Berlin');
                expect($vo->toString())->toBe('1732445696123')
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('throws exception on invalid input', function (string $input, string $exception, string $messagePart) {
                expect(fn() => TimestampMilliseconds::fromString($input))
                    ->toThrow($exception, $messagePart);
            })->with([
                'non-numeric' => ['not-a-number', DateTimeTypeException::class, 'Expected milliseconds timestamp as digits'],
                'trailing space' => ['1000000000000 ', DateTimeTypeException::class, 'Expected milliseconds timestamp as digits'],
                'above max' => ['253402300800000', ReasonableRangeDateTimeTypeException::class, 'out of supported range'],
            ]);
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = TimestampMilliseconds::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(TimestampMilliseconds::class)
                        ->and($result->toString())->toBe($input);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['1732445696123', true],
                'invalid' => ['abc', false],
            ]);

            it('accepts custom timezone', function () {
                $s = '1732445696123';
                $vo = TimestampMilliseconds::tryFromString($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(TimestampMilliseconds::class)
                    ->and($vo->toString())->toBe($s)
                    ->and($vo->value()->getOffset())->toBe(0);
            });
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = TimestampMilliseconds::tryFromMixed($input);
                expect($result)->toBeInstanceOf(TimestampMilliseconds::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'string' => ['1732445696123', '1732445696123'],
                'integer' => [1732445696123, '1732445696123'],
                'Stringable' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '1732445696123';
                        }
                    },
                    '1732445696123',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = TimestampMilliseconds::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [['x']],
                'object' => [new stdClass()],
                'DateTimeImmutable' => [new DateTimeImmutable()],
            ]);

            it('accepts custom timezone', function () {
                $s = '1732445696123';
                $vo = TimestampMilliseconds::tryFromMixed($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(TimestampMilliseconds::class)
                    ->and($vo->toString())->toBe($s)
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('kills mutants in tryFromMixed', function () {
                $customDefault = Undefined::create();

                // Kill InstanceOfToTrue for Stringable by passing non-stringable
                expect(TimestampMilliseconds::tryFromMixed([], 'UTC', $customDefault))
                    ->toBe($customDefault);

                // Kill RemoveStringCast for Stringable
                $stringable = new class implements Stringable {
                    public function __toString(): string
                    {
                        return '1732445696123';
                    }
                };
                expect(TimestampMilliseconds::tryFromMixed($stringable, 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampMilliseconds::class);

                // Kill InstanceOfToTrue for Stringable in tryFromMixed (by passing integer which is not string/stringable)
                expect(TimestampMilliseconds::tryFromMixed(123, 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampMilliseconds::class);
            });
        });

        describe('getFormat', function () {
            it('returns internal pattern', function () {
                expect(TimestampMilliseconds::getFormat())->toBe('U.u');
            });
        });
    });

    describe('Instance Methods', function () {
        it('value() returns internal DateTimeImmutable (normalized to UTC)', function () {
            $vo = TimestampMilliseconds::fromString('1732445696123');
            expect($vo->value()->format('U.u'))->toBe('1732445696.123000')
                ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
        });

        it('toString and __toString return milliseconds string', function () {
            $s = '1732445696123';
            $vo = TimestampMilliseconds::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s)
                ->and($vo->__toString())->toBe($s);
        });

        it('jsonSerialize and toInt return integer', function () {
            $vo = TimestampMilliseconds::fromString('1732445696123');
            expect($vo->jsonSerialize())->toBe(1732445696123)
                ->and($vo->toInt())->toBe(1732445696123);
        });

        it('isEmpty is always false', function () {
            $vo = TimestampMilliseconds::fromString('0');
            expect($vo->isEmpty())->toBeFalse();

            // Kills the FalseToTrue mutant in isEmpty
            if ($vo->isEmpty() !== false) {
                throw new Exception('isEmpty mutant!');
            }
        });

        it('isUndefined is always false', function () {
            $vo = TimestampMilliseconds::fromString('0');
            expect($vo->isUndefined())->toBeFalse();

            // Kills the FalseToTrue mutant in isUndefined
            if ($vo->isUndefined() !== false) {
                throw new Exception('isUndefined mutant!');
            }
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(TimestampMilliseconds::class)
                    ->and($vo2->toString())->toBe('1732445696123')
                    ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');

                // original is immutable
                expect($vo->toString())->toBe('1732445696123');
            });
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123');
                expect($vo->isTypeOf(TimestampMilliseconds::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123');
                expect($vo->isTypeOf('NonExistentClass', TimestampMilliseconds::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = TimestampMilliseconds::fromString('1732445696123');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
