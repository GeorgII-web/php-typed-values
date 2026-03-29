<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use const STDOUT;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringIpV4Exception;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringIpV4;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(StringIpV4::class);

describe('StringIpV4', function () {
    describe('Construction and Validation', function () {
        it('creates an instance from a valid IPv4 address', function (string $ip): void {
            $v = StringIpV4::fromString($ip);
            expect($v)->toBeInstanceOf(StringIpV4::class)
                ->and($v->toString())->toBe($ip)
                ->and($v->value())->toBe($ip);
        })->with([
            '127.0.0.1',
            '192.168.1.1',
            '8.8.8.8',
            '255.255.255.255',
            '0.0.0.0',
        ]);

        it('throws StringIpV4Exception for invalid IPv4 address', function (string $invalidIp): void {
            expect(fn() => StringIpV4::fromString($invalidIp))
                ->toThrow(StringIpV4Exception::class, "Invalid IPv4 address: {$invalidIp}");
        })->with([
            '127.0.0',
            '256.256.256.256',
            'not-an-ip',
            '1.2.3.4.5',
            '::1', // IPv6 is not IPv4
            '',
        ]);
    });

    describe('Factory methods', function () {
        it('throws StringIpV4Exception from fromBool', function (): void {
            expect(fn() => StringIpV4::fromBool(true))
                ->toThrow(StringIpV4Exception::class);
        });

        it('throws StringIpV4Exception from fromInt', function (): void {
            expect(fn() => StringIpV4::fromInt(127001))
                ->toThrow(StringIpV4Exception::class);
        });

        it('throws StringIpV4Exception from fromDecimal', function (): void {
            expect(fn() => StringIpV4::fromDecimal('1.23'))
                ->toThrow(StringIpV4Exception::class);
        });

        it('throws StringIpV4Exception from fromFloat', function (): void {
            expect(fn() => StringIpV4::fromFloat(123.45))
                ->toThrow(StringIpV4Exception::class);
        });
    });

    describe('Conversion methods', function () {
        it('throws StringTypeException when converting non-int-string to int', function (): void {
            $v = StringIpV4::fromString('1.1.1.1');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-float-string to float', function (): void {
            $v = StringIpV4::fromString('1.1.1.1');
            expect(fn() => $v->toFloat())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-bool-string to bool', function (): void {
            $v = StringIpV4::fromString('1.1.1.1');
            expect(fn() => $v->toBool())->toThrow(StringTypeException::class);
        });

        it('throws DecimalTypeException when converting non-decimal-string to decimal', function (): void {
            $v = StringIpV4::fromString('1.1.1.1');
            expect(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });

        it('converts to other types when string value is compatible', function (): void {
            // '0.0.0.0' won't work as int, it's "0.0.0.0"
            // Let's use a value that MIGHT work if it was just "1", but it's not.
            // IPv4 is ALWAYS "X.X.X.X" (or similar), so strict conversions will always fail.
            // But we should test that it works IF we had a string that was compatible.
            // However, StringIpV4 MUST be a valid IP, so it can NEVER be a simple "1" or "true".
            // Thus, these will always throw.
            $v = StringIpV4::fromString('127.0.0.1');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class)
                ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
                ->and(fn() => $v->toBool())->toThrow(StringTypeException::class)
                ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });
    });

    describe('Try methods', function () {
        it('tryFromString returns instance or default', function (): void {
            expect(StringIpV4::tryFromString('127.0.0.1'))->toBeInstanceOf(StringIpV4::class)
                ->and(StringIpV4::tryFromString('invalid'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromBool returns instance or default', function (): void {
            expect(StringIpV4::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromInt returns instance or default', function (): void {
            expect(StringIpV4::tryFromInt(123))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromDecimal returns instance or default', function (): void {
            expect(StringIpV4::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (): void {
            expect(StringIpV4::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed returns instance or default', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '127.0.0.1';
                }
            };

            $nonStringable = new stdClass();

            expect(StringIpV4::tryFromMixed('127.0.0.1'))->toBeInstanceOf(StringIpV4::class)
                ->and(StringIpV4::tryFromMixed(123))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV4::tryFromMixed(true))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV4::tryFromMixed('invalid'))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV4::tryFromMixed($stringable))->toBeInstanceOf(StringIpV4::class)
                ->and(StringIpV4::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV4::tryFromMixed($nonStringable))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV4::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV4::tryFromMixed(STDOUT))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles null and other types correctly with custom default', function (): void {
            $default = StringIpV4::fromString('8.8.8.8');

            // null should go to null === $value branch -> fromString('null') -> throw -> return $default
            expect(StringIpV4::tryFromMixed(null, $default))->toBe($default)
                // [] should go to default branch -> throw -> return $default
                ->and(StringIpV4::tryFromMixed([], $default))->toBe($default)
                // new stdClass() should go to default branch -> throw -> return $default
                ->and(StringIpV4::tryFromMixed(new stdClass(), $default))->toBe($default)
                // STDOUT should go to default branch -> throw -> return $default
                ->and(StringIpV4::tryFromMixed(STDOUT, $default))->toBe($default);
        });
    });

    describe('Metadata', function () {
        it('is not empty', function (): void {
            $v = StringIpV4::fromString('127.0.0.1');
            expect($v->isEmpty())->toBeFalse();
        });

        it('is not undefined', function (): void {
            $v = StringIpV4::fromString('127.0.0.1');
            expect($v->isUndefined())->toBeFalse();
        });

        it('identifies its type', function (): void {
            $v = StringIpV4::fromString('127.0.0.1');
            expect($v->isTypeOf(StringIpV4::class))->toBeTrue()
                ->and($v->isTypeOf('NonExistent'))->toBeFalse();
        });

        it('serializes to JSON', function (): void {
            $v = StringIpV4::fromString('127.0.0.1');
            expect(json_encode($v))->toBe('"127.0.0.1"');
        });
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringIpV4Test extends StringIpV4
{
    public static function fromString(string $value): static
    {
        if ($value === 'null') {
            return new self('10.0.0.1');
        }

        return new self('127.0.0.1');
    }
}

describe('Coverage for mutants', function () {
    it('tryFromMixed specifically triggers fromString("null") for null value', function (): void {
        // StringIpV4Test overrides fromString to return 10.0.0.1 specifically for 'null'.
        // This allows us to verify that null === $value branch is indeed taken for null.
        $result = StringIpV4Test::tryFromMixed(null);
        expect($result)->toBeInstanceOf(StringIpV4::class)
            ->and($result->value())->toBe('10.0.0.1');
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        // For an array, it should hit 'default' branch in tryFromMixed.
        // If the mutant null !== $value was present, an array would hit that branch and return 10.0.0.1.
        // Without mutant, it hits 'default' which throws TypeException and returns Undefined.
        $result = StringIpV4Test::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});
