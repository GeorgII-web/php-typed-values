<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(DateTimeTypeAbstract::class);

/**
 * @internal
 *
 * @coversNothing
 */
readonly class DateTimeTypeAbstractTest extends DateTimeTypeAbstract
{
    public const string FORMAT = 'Y-m-d H:i:s';

    private DateTimeImmutable $dt;

    public function __construct(DateTimeImmutable $dt)
    {
        $this->dt = $dt->setTimezone(new DateTimeZone('UTC'));
    }

    public static function fromDateTime(DateTimeImmutable $value): static
    {
        return new self($value);
    }

    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static
    {
        return new self(
            static::stringToDateTime(
                $value,
                static::FORMAT,
                static::stringToDateTimeZone($timezone)
            )
        );
    }

    public static function getFormat(): string
    {
        return static::FORMAT;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Public wrapper for protected stringToDateTime to test it.
     */
    public static function testStringToDateTime(string $value, string $format, ?DateTimeZone $timezone = null): DateTimeImmutable
    {
        return static::stringToDateTime($value, $format, $timezone);
    }

    /**
     * Public wrapper for protected stringToDateTimeZone to test it.
     */
    public static function testStringToDateTimeZone(string $timezone): DateTimeZone
    {
        return static::stringToDateTimeZone($timezone);
    }

    public function toString(): string
    {
        return $this->dt->format(static::FORMAT);
    }

    public static function tryFromMixed(
        mixed $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return match (true) {
                \is_string($value) => static::fromString($value, $timezone),
                $value instanceof DateTimeImmutable => static::fromDateTime($value),
                $value instanceof DateTimeTypeAbstract => static::fromDateTime($value->value()),
                $value instanceof Stringable => static::fromString((string) $value, $timezone),
                default => throw new Exception(),
            };
        } catch (Throwable) {
            return $default;
        }
    }

    public static function tryFromString(
        string $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromString($value, $timezone);
        } catch (Throwable) {
            return $default;
        }
    }

    public function value(): DateTimeImmutable
    {
        return $this->dt;
    }

    public function withTimeZone(string $timezone): static
    {
        return new self($this->dt->setTimezone(static::stringToDateTimeZone($timezone)));
    }
}

describe('DateTimeTypeAbstract', function () {
    describe('Creation', function () {
        it('creates instance from DateTimeImmutable', function () {
            $dt = new DateTimeImmutable('2025-01-01 12:00:00');
            $v = DateTimeTypeAbstractTest::fromDateTime($dt);
            expect($v->value()->getTimestamp())->toBe($dt->getTimestamp());
        });

        it('creates instance from string with format', function () {
            $v = DateTimeTypeAbstractTest::fromString('2025-01-01 12:00:00');
            expect($v->toString())->toBe('2025-01-01 12:00:00');
        });

        it('throws exception on invalid string format', function () {
            expect(fn() => DateTimeTypeAbstractTest::fromString('2025/01/01'))
                ->toThrow(DateTimeTypeException::class);
        });

        it('throws exception on blank string', function () {
            expect(fn() => DateTimeTypeAbstractTest::fromString('   '))
                ->toThrow(DateTimeTypeException::class, 'blank');
        });

        it('throws exception on null bytes', function () {
            expect(fn() => DateTimeTypeAbstractTest::fromString("2025-01-01\0"))
                ->toThrow(DateTimeTypeException::class, 'null bytes');
        });

        it('throws exception on out of range timestamp', function (string $date) {
            expect(fn() => DateTimeTypeAbstractTest::testStringToDateTime($date, 'Y-m-d H:i:s'))
                ->toThrow(DateTimeTypeException::class);
        })->with([
            'before min' => ['0000-12-31 23:59:59'],
            'after max' => ['10000-01-01 00:00:00'],
        ]);

        it('throws exception on round-trip mismatch', function () {
            // PHP createFromFormat might be lenient, but we want strictness.
            // For example, '2025-01-01 12:00:00' with format 'Y-m-d' might parse but won't match.
            expect(fn() => DateTimeTypeAbstractTest::testStringToDateTime('2025-01-01 12:00:00', 'Y-m-d'))
                ->toThrow(DateTimeTypeException::class, 'Invalid date time value');
        });

        it('handles warnings and errors from createFromFormat', function () {
            // '2025-02-30 00:00:00' is invalid but PHP might "fix" it to '2025-03-02'.
            // This triggers warnings/errors in getLastErrors.
            expect(fn() => DateTimeTypeAbstractTest::fromString('2025-02-30 00:00:00'))
                ->toThrow(DateTimeTypeException::class, 'Invalid date time value');
        });
    });

    describe('Timezone Handling', function () {
        it('converts timezone string to DateTimeZone', function () {
            expect(DateTimeTypeAbstractTest::testStringToDateTimeZone('UTC'))
                ->toBeInstanceOf(DateTimeZone::class)
                ->and(DateTimeTypeAbstractTest::testStringToDateTimeZone('UTC')->getName())->toBe('UTC');
        });

        it('throws ZoneDateTimeTypeException on invalid timezone', function () {
            expect(fn() => DateTimeTypeAbstractTest::testStringToDateTimeZone('Invalid/Zone'))
                ->toThrow(ZoneDateTimeTypeException::class);
        });

        it('normalizes internal value to UTC', function () {
            $v = DateTimeTypeAbstractTest::fromString('2025-01-01 12:00:00', 'Europe/Berlin');
            // 12:00 in Berlin is 11:00 UTC (in winter)
            expect($v->value()->getTimezone()->getName())->toBe('UTC')
                ->and($v->value()->format('H:i:s'))->toBe('11:00:00');
        });
    });

    describe('tryFrom Methods', function () {
        it('tryFromString returns instance or default', function (string $input, bool $success) {
            $result = DateTimeTypeAbstractTest::tryFromString($input);
            if ($success) {
                expect($result)->toBeInstanceOf(DateTimeTypeAbstractTest::class);
            } else {
                expect($result)->toBeInstanceOf(Undefined::class);
            }
        })->with([
            'valid' => ['2025-01-01 12:00:00', true],
            'invalid' => ['invalid', false],
        ]);

        it('tryFromMixed returns instance for various inputs', function (mixed $input, bool $success) {
            $result = DateTimeTypeAbstractTest::tryFromMixed($input);
            if ($success) {
                expect($result)->toBeInstanceOf(DateTimeTypeAbstractTest::class);
            } else {
                expect($result)->toBeInstanceOf(Undefined::class);
            }
        })->with([
            'string' => ['2025-01-01 12:00:00', true],
            'DateTimeImmutable' => [new DateTimeImmutable(), true],
            'instance' => [new DateTimeTypeAbstractTest(new DateTimeImmutable()), true],
            'Stringable' => [
                new class implements Stringable {
                    public function __toString(): string
                    {
                        return '2025-01-01 12:00:00';
                    }
                },
                true,
            ],
            'null' => [null, false],
            'array' => [[], false],
        ]);
    });

    describe('Instance Methods', function () {
        it('exposes value and formats', function () {
            $dt = new DateTimeImmutable('2025-01-01 12:00:00');
            $v = new DateTimeTypeAbstractTest($dt);
            expect($v->value()->getTimestamp())->toBe($dt->getTimestamp())
                ->and($v->toString())->toBe('2025-01-01 12:00:00')
                ->and((string) $v)->toBe('2025-01-01 12:00:00')
                ->and($v->jsonSerialize())->toBe('2025-01-01 12:00:00');
        });

        it('isTypeOf works as expected', function () {
            $v = new DateTimeTypeAbstractTest(new DateTimeImmutable());
            expect($v->isTypeOf(DateTimeTypeAbstractTest::class))->toBeTrue()
                ->and($v->isTypeOf(DateTimeTypeAbstract::class))->toBeTrue()
                ->and($v->isTypeOf('NonExistent'))->toBeFalse()
                ->and($v->isTypeOf('NonExistent', DateTimeTypeAbstract::class))->toBeTrue();
        });

        it('isUndefined and isEmpty return false', function () {
            $v = new DateTimeTypeAbstractTest(new DateTimeImmutable());
            expect($v->isUndefined())->toBeFalse()
                ->and($v->isEmpty())->toBeFalse();
        });

        it('withTimeZone returns new instance and normalizes to UTC', function () {
            $v = DateTimeTypeAbstractTest::fromString('2025-01-01 12:00:00', 'UTC');
            $v2 = $v->withTimeZone('Europe/Berlin');
            // The value is normalized to UTC in stringToDateTime and constructor (if implemented so)
            // In my stub, stringToDateTime normalize to UTC.
            expect($v2->value()->getTimezone()->getName())->toBe('UTC')
                ->and($v2->value()->getTimestamp())->toBe($v->value()->getTimestamp());
        });
    });

    describe('Constants and Interfaces', function () {
        it('has correct default constants', function () {
            expect(DateTimeTypeAbstract::DEFAULT_ZONE)->toBe('UTC')
                ->and(DateTimeTypeAbstract::MAX_TIMESTAMP_SECONDS)->toBe(253402300799)
                ->and(DateTimeTypeAbstract::MIN_TIMESTAMP_SECONDS)->toBe(-62135596800);
        });
    });
});
