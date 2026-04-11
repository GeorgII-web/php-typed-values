<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use const STDOUT;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\MacAddressStringException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringMacAddress;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(StringMacAddress::class);

describe('StringMacAddress', function () {
    describe('Construction and Validation', function () {
        it('creates an instance from a valid MAC address', function (string $mac): void {
            $v = StringMacAddress::fromString($mac);
            expect($v)->toBeInstanceOf(StringMacAddress::class)
                ->and($v->toString())->toBe($mac)
                ->and($v->value())->toBe($mac);
        })->with([
            '00:00:5e:00:53:01',
            '01-23-45-67-89-ab',
            '01:23:45:67:89:AB',
            '0123.4567.89ab',
        ]);

        it('throws MacAddressStringException for invalid MAC address', function (string $invalidMac): void {
            expect(fn() => StringMacAddress::fromString($invalidMac))
                ->toThrow(MacAddressStringException::class, "Invalid MAC address: {$invalidMac}");
        })->with([
            '00:00:5e:00:53',
            '00:00:5e:00:53:01:02',
            'not-a-mac',
            'G0:00:5e:00:53:01',
            '',
        ]);
    });

    describe('Factory methods', function () {
        it('throws MacAddressStringException from fromBool', function (): void {
            expect(fn() => StringMacAddress::fromBool(true))
                ->toThrow(MacAddressStringException::class);
        });

        it('throws MacAddressStringException from fromInt', function (): void {
            expect(fn() => StringMacAddress::fromInt(123456))
                ->toThrow(MacAddressStringException::class);
        });

        it('throws MacAddressStringException from fromDecimal', function (): void {
            expect(fn() => StringMacAddress::fromDecimal('1.23'))
                ->toThrow(MacAddressStringException::class);
        });

        it('throws MacAddressStringException from fromFloat', function (): void {
            expect(fn() => StringMacAddress::fromFloat(123.45))
                ->toThrow(MacAddressStringException::class);
        });
    });

    describe('Conversion methods', function () {
        it('throws StringTypeException when converting non-int-string to int', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-float-string to float', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect(fn() => $v->toFloat())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-bool-string to bool', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect(fn() => $v->toBool())->toThrow(StringTypeException::class);
        });

        it('throws DecimalTypeException when converting non-decimal-string to decimal', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });
    });

    describe('Try methods', function () {
        it('tryFromString returns instance or default', function (): void {
            expect(StringMacAddress::tryFromString('00:00:5e:00:53:01'))->toBeInstanceOf(StringMacAddress::class)
                ->and(StringMacAddress::tryFromString('invalid'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromBool returns instance or default', function (): void {
            expect(StringMacAddress::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromInt returns instance or default', function (): void {
            expect(StringMacAddress::tryFromInt(123))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromDecimal returns instance or default', function (): void {
            expect(StringMacAddress::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (): void {
            expect(StringMacAddress::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed returns instance or default', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '00:00:5e:00:53:01';
                }
            };

            $nonStringable = new stdClass();

            expect(StringMacAddress::tryFromMixed('00:00:5e:00:53:01'))->toBeInstanceOf(StringMacAddress::class)
                ->and(StringMacAddress::tryFromMixed(123))->toBeInstanceOf(Undefined::class)
                ->and(StringMacAddress::tryFromMixed(true))->toBeInstanceOf(Undefined::class)
                ->and(StringMacAddress::tryFromMixed('invalid'))->toBeInstanceOf(Undefined::class)
                ->and(StringMacAddress::tryFromMixed($stringable))->toBeInstanceOf(StringMacAddress::class)
                ->and(StringMacAddress::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(StringMacAddress::tryFromMixed($nonStringable))->toBeInstanceOf(Undefined::class)
                ->and(StringMacAddress::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(StringMacAddress::tryFromMixed(STDOUT))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles null and other types correctly with custom default', function (): void {
            $default = StringMacAddress::fromString('00:00:5e:00:53:02');

            expect(StringMacAddress::tryFromMixed(null, $default))->toBe($default)
                ->and(StringMacAddress::tryFromMixed([], $default))->toBe($default)
                ->and(StringMacAddress::tryFromMixed(new stdClass(), $default))->toBe($default)
                ->and(StringMacAddress::tryFromMixed(STDOUT, $default))->toBe($default);
        });
    });

    describe('Metadata', function () {
        it('is not empty', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect($v->isEmpty())->toBeFalse();
        });

        it('is not undefined', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect($v->isUndefined())->toBeFalse();
        });

        it('identifies its type', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect($v->isTypeOf(StringMacAddress::class))->toBeTrue()
                ->and($v->isTypeOf(StringTypeAbstract::class))->toBeTrue()
                ->and($v->isTypeOf(PrimitiveTypeAbstract::class))->toBeTrue()
                ->and($v->isTypeOf('PhpTypedValues\String\Specific\StringMacAddress'))->toBeTrue()
                ->and($v->isTypeOf('PhpTypedValues\Base\Primitive\String\StringTypeAbstract'))->toBeTrue()
                ->and($v->isTypeOf('PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract'))->toBeTrue()
                ->and($v->isTypeOf(StringMacAddress::class, 'Other'))->toBeTrue()
                ->and($v->isTypeOf('Other', StringMacAddress::class))->toBeTrue()
                ->and($v->isTypeOf('Other', StringTypeAbstract::class))->toBeTrue()
                ->and($v->isTypeOf('Other', PrimitiveTypeAbstract::class))->toBeTrue()
                ->and($v->isTypeOf('Other'))->toBeFalse()
                ->and($v->isTypeOf())->toBeFalse()
                ->and($v->isTypeOf('NonExistent'))->toBeFalse();
        });

        it('serializes to JSON', function (): void {
            $v = StringMacAddress::fromString('00:00:5e:00:53:01');
            expect(json_encode($v))->toBe('"00:00:5e:00:53:01"');
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
readonly class StringMacAddressTest extends StringMacAddress
{
    public static function fromString(string $value): static
    {
        if ($value === 'null') {
            return new self('00:00:5e:00:53:01');
        }

        return new self('00:00:5e:00:53:02');
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return parent::isTypeOf(...$classNames);
    }
}

describe('Coverage for mutants', function () {
    it('tryFromMixed specifically triggers fromString("null") for null value', function (): void {
        $result = StringMacAddressTest::tryFromMixed(null);
        expect($result)->toBeInstanceOf(StringMacAddress::class)
            ->and($result->value())->toBe('00:00:5e:00:53:01');
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        $result = StringMacAddressTest::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});

describe('Null checks', function () {
    it('throws exception on fromNull', function () {
        expect(fn() => StringMacAddress::fromNull(null))
            ->toThrow(MacAddressStringException::class);
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringMacAddress::toNull())
            ->toThrow(MacAddressStringException::class);
    });
});
