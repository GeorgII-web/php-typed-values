<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use const STDOUT;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringIpV6Exception;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringIpV6;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(StringIpV6::class);

describe('StringIpV6', function () {
    describe('Construction and Validation', function () {
        it('creates an instance from a valid IPv6 address', function (string $ip): void {
            $v = StringIpV6::fromString($ip);
            expect($v)->toBeInstanceOf(StringIpV6::class)
                ->and($v->toString())->toBe($ip)
                ->and($v->value())->toBe($ip);
        })->with([
            '::1',
            '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            '2001:db8:85a3::8a2e:370:7334',
            'fe80::1',
            '::ffff:192.168.0.1',
        ]);

        it('throws StringIpV6Exception for invalid IPv6 address', function (string $invalidIp): void {
            expect(fn() => StringIpV6::fromString($invalidIp))
                ->toThrow(StringIpV6Exception::class, "Invalid IPv6 address: {$invalidIp}");
        })->with([
            '127.0.0.1', // IPv4 is not IPv6
            '2001:db8:g::1',
            'not-an-ip',
            '2001:db8::1::1',
            '',
        ]);
    });

    describe('Factory methods', function () {
        it('throws StringIpV6Exception from fromBool', function (): void {
            expect(fn() => StringIpV6::fromBool(true))
                ->toThrow(StringIpV6Exception::class);
        });

        it('throws StringIpV6Exception from fromInt', function (): void {
            expect(fn() => StringIpV6::fromInt(123))
                ->toThrow(StringIpV6Exception::class);
        });

        it('throws StringIpV6Exception from fromDecimal', function (): void {
            expect(fn() => StringIpV6::fromDecimal('1.23'))
                ->toThrow(StringIpV6Exception::class);
        });

        it('throws StringIpV6Exception from fromFloat', function (): void {
            expect(fn() => StringIpV6::fromFloat(123.45))
                ->toThrow(StringIpV6Exception::class);
        });
    });

    describe('Conversion methods', function () {
        it('throws StringTypeException when converting non-int-string to int', function (): void {
            $v = StringIpV6::fromString('::1');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-float-string to float', function (): void {
            $v = StringIpV6::fromString('::1');
            expect(fn() => $v->toFloat())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-bool-string to bool', function (): void {
            $v = StringIpV6::fromString('::1');
            expect(fn() => $v->toBool())->toThrow(StringTypeException::class);
        });

        it('throws DecimalTypeException when converting non-decimal-string to decimal', function (): void {
            $v = StringIpV6::fromString('::1');
            expect(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });

        it('converts to other types when string value is compatible', function (): void {
            $v = StringIpV6::fromString('::1');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class)
                ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
                ->and(fn() => $v->toBool())->toThrow(StringTypeException::class)
                ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });
    });

    describe('Try methods', function () {
        it('tryFromString returns instance or default', function (): void {
            expect(StringIpV6::tryFromString('::1'))->toBeInstanceOf(StringIpV6::class)
                ->and(StringIpV6::tryFromString('invalid'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromBool returns instance or default', function (): void {
            expect(StringIpV6::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromInt returns instance or default', function (): void {
            expect(StringIpV6::tryFromInt(123))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromDecimal returns instance or default', function (): void {
            expect(StringIpV6::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (): void {
            expect(StringIpV6::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed returns instance or default', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '::1';
                }
            };

            $nonStringable = new stdClass();

            expect(StringIpV6::tryFromMixed('::1'))->toBeInstanceOf(StringIpV6::class)
                ->and(StringIpV6::tryFromMixed(123))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV6::tryFromMixed(true))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV6::tryFromMixed('invalid'))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV6::tryFromMixed($stringable))->toBeInstanceOf(StringIpV6::class)
                ->and(StringIpV6::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV6::tryFromMixed($nonStringable))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV6::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(StringIpV6::tryFromMixed(STDOUT))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles null and other types correctly with custom default', function (): void {
            $default = StringIpV6::fromString('2001:db8::1');

            expect(StringIpV6::tryFromMixed(null, $default))->toBe($default)
                ->and(StringIpV6::tryFromMixed([], $default))->toBe($default)
                ->and(StringIpV6::tryFromMixed(new stdClass(), $default))->toBe($default)
                ->and(StringIpV6::tryFromMixed(STDOUT, $default))->toBe($default);
        });
    });

    describe('Metadata', function () {
        it('is not empty', function (): void {
            $v = StringIpV6::fromString('::1');
            expect($v->isEmpty())->toBeFalse();
        });

        it('is not undefined', function (): void {
            $v = StringIpV6::fromString('::1');
            expect($v->isUndefined())->toBeFalse();
        });

        it('identifies its type', function (): void {
            $v = StringIpV6::fromString('::1');
            expect($v->isTypeOf(StringIpV6::class))->toBeTrue()
                ->and($v->isTypeOf('StringIpV6'))->toBeFalse()
                ->and($v->isTypeOf(StringIpV6::class, 'other'))->toBeTrue();
        });

        it('serializes to JSON', function (): void {
            $v = StringIpV6::fromString('::1');
            expect(json_encode($v))->toBe('"::1"');
        });

        it('converts to string via __toString', function (): void {
            $v = StringIpV6::fromString('::1');
            expect((string) $v)->toBe('::1');
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
readonly class StringIpV6Test extends StringIpV6
{
    public static function fromString(string $value): static
    {
        if ($value === 'null') {
            return new self('2001:db8::');
        }

        return new self('::1');
    }
}

describe('Coverage for mutants', function () {
    it('tryFromMixed specifically triggers fromString("null") for null value', function (): void {
        $result = StringIpV6Test::tryFromMixed(null);
        expect($result)->toBeInstanceOf(StringIpV6::class)
            ->and($result->value())->toBe('2001:db8::');
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        $result = StringIpV6Test::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});
