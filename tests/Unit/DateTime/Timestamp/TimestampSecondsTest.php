<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\DateTime\Timestamp;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ReasonableRangeDateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

describe('TimestampSeconds', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('creates instance from DateTimeImmutable', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = TimestampSeconds::fromDateTime($dt);

                expect($vo->value()->format('U'))->toBe('1735787045')
                    ->and($vo->toString())->toBe('1735787045');
            });

            it('normalizes timezone to UTC while preserving the instant', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+03:00');
                $vo = TimestampSeconds::fromDateTime($dt);

                expect($vo->value()->format('U'))->toBe($dt->format('U'))
                    ->and($vo->toString())->toBe($dt->format('U'))
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
            });
        });

        describe('fromInt', function () {
            it('creates instance from integer', function () {
                $vo = TimestampSeconds::fromInt(1735787045);
                expect($vo->value()->format('U'))->toBe('1735787045')
                    ->and($vo->toString())->toBe('1735787045');
            });

            it('accepts custom timezone', function () {
                $vo = TimestampSeconds::fromInt(1735787045, 'America/New_York');
                expect($vo->toString())->toBe('1735787045')
                    ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
            });
        });

        describe('fromString', function () {
            it('creates instance from valid numeric string', function (string $input, string $expected) {
                $vo = TimestampSeconds::fromString($input);
                expect($vo->toString())->toBe($expected)
                    ->and($vo->value()->format('U'))->toBe($expected);
            })->with([
                'standard' => ['1000000000', '1000000000'],
                'zero' => ['0', '0'],
                'max boundary' => ['253402300799', '253402300799'],
                'min boundary' => ['-62135596800', '-62135596800'],
            ]);

            it('accepts custom timezone', function () {
                $vo = TimestampSeconds::fromString('1735787045', 'Europe/Berlin');
                expect($vo->toString())->toBe('1735787045')
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('throws exception on invalid input', function (string $input, string $exception, string $messagePart) {
                expect(fn() => TimestampSeconds::fromString($input))
                    ->toThrow($exception, $messagePart);
            })->with([
                'non-numeric' => ['not-a-number', DateTimeTypeException::class, 'Invalid date time value'],
                'trailing space' => ['1000000000 ', DateTimeTypeException::class, 'Invalid date time value'],
                'leading zeros' => ['0000000005', DateTimeTypeException::class, 'Unexpected conversion'],
                'above max' => ['253402300800', ReasonableRangeDateTimeTypeException::class, 'out of supported range'],
                'below min' => ['-62135596801', ReasonableRangeDateTimeTypeException::class, 'out of supported range'],
            ]);
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = TimestampSeconds::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(TimestampSeconds::class)
                        ->and($result->toString())->toBe($input);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['1735787045', true],
                'invalid' => ['not-a-number', false],
            ]);

            it('accepts custom timezone', function () {
                $s = '1735787045';
                $vo = TimestampSeconds::tryFromString($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(TimestampSeconds::class)
                    ->and($vo->toString())->toBe($s)
                    ->and($vo->value()->getOffset())->toBe(0);
            });
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = TimestampSeconds::tryFromMixed($input);
                expect($result)->toBeInstanceOf(TimestampSeconds::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'string' => ['1735787045', '1735787045'],
                'integer' => [1735787045, '1735787045'],
                'DateTimeImmutable' => [new DateTimeImmutable('2025-01-01 00:00:00+00:00'), '1735689600'],
                'Stringable' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '1735689600';
                        }
                    },
                    '1735689600',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = TimestampSeconds::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [['x']],
                'object' => [new stdClass()],
            ]);

            it('accepts custom timezone', function () {
                $s = '1735787045';
                $vo = TimestampSeconds::tryFromMixed($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(TimestampSeconds::class)
                    ->and($vo->toString())->toBe($s)
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('kills mutants in tryFromMixed', function () {
                $customDefault = Undefined::create();

                // Kill InstanceOfToFalse for DateTimeImmutable
                $dt = new DateTimeImmutable('2025-01-01 00:00:00');
                expect(TimestampSeconds::tryFromMixed($dt, 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampSeconds::class);

                // Kill InstanceOfToTrue for Stringable by passing non-stringable
                expect(TimestampSeconds::tryFromMixed([], 'UTC', $customDefault))
                    ->toBe($customDefault);

                // Kill RemoveStringCast for Stringable
                $stringable = new class implements Stringable {
                    public function __toString(): string
                    {
                        return '1735689600';
                    }
                };
                expect(TimestampSeconds::tryFromMixed($stringable, 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampSeconds::class);

                // Kill InstanceOfToTrue for DateTimeImmutable in tryFromMixed
                // If mutated to true, it would try to call fromDateTime on a non-DateTimeImmutable
                expect(TimestampSeconds::tryFromMixed('1735689600', 'UTC', $customDefault))
                    ->toBeInstanceOf(TimestampSeconds::class);
            });
        });

        describe('getFormat', function () {
            it('returns U format', function () {
                expect(TimestampSeconds::getFormat())->toBe('U');
            });
        });
    });

    describe('Instance Methods', function () {
        it('value() returns internal DateTimeImmutable (normalized to UTC)', function () {
            $vo = TimestampSeconds::fromString('1735787045');
            expect($vo->value()->format('U'))->toBe('1735787045')
                ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
        });

        it('toString and __toString return seconds string', function () {
            $s = '1735787045';
            $vo = TimestampSeconds::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s)
                ->and($vo->__toString())->toBe($s);
        });

        it('jsonSerialize and toInt return integer', function () {
            $vo = TimestampSeconds::fromString('1735787045');
            expect($vo->jsonSerialize())->toBe(1735787045)
                ->and($vo->toInt())->toBe(1735787045);
        });

        it('isEmpty is always false', function () {
            $vo = TimestampSeconds::fromString('1735787045');
            expect($vo->isEmpty())->toBeFalse();

            // Kills the FalseToTrue mutant in isEmpty
            if ($vo->isEmpty() !== false) {
                throw new Exception('isEmpty mutant!');
            }
        });

        it('isUndefined is always false', function () {
            $vo = TimestampSeconds::fromString('1735787045');
            expect($vo->isUndefined())->toBeFalse();

            // Kills the FalseToTrue mutant in isUndefined
            if ($vo->isUndefined() !== false) {
                throw new Exception('isUndefined mutant!');
            }
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = TimestampSeconds::fromString('1735787045');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(TimestampSeconds::class)
                    ->and($vo2->toString())->toBe('1735787045')
                    ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');

                // original is immutable
                expect($vo->toString())->toBe('1735787045');
            });
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = TimestampSeconds::fromString('1735787045');
                expect($vo->isTypeOf(TimestampSeconds::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = TimestampSeconds::fromString('1735787045');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = TimestampSeconds::fromString('1735787045');
                expect($vo->isTypeOf('NonExistentClass', TimestampSeconds::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = TimestampSeconds::fromString('1735787045');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = TimestampSeconds::fromString('1735787045');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
