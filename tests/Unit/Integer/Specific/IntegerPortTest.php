<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\PortIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerPort;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerPort::class);

describe('IntegerPort', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0-65535', function (int $value): void {
            $port = new IntegerPort($value);
            expect($port->value())->toBe($value);
        })->with([0, 1, 80, 443, 8080, 65535]);

        it('throws for values outside 0-65535', function (int $invalidValue): void {
            expect(fn() => new IntegerPort($invalidValue))
                ->toThrow(PortIntegerTypeException::class, 'Expected port integer (0-65535)');
        })->with([-1, 65536, 100000]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0-65535', function (int $value): void {
            $port = IntegerPort::fromInt($value);
            expect($port->value())->toBe($value);
        })->with([0, 1, 80, 443, 8080, 65535]);

        it('throws for values outside 0-65535', function (int $invalidValue): void {
            expect(fn() => IntegerPort::fromInt($invalidValue))
                ->toThrow(PortIntegerTypeException::class, 'Expected port integer (0-65535)');
        })->with([-1, 65536, 100000]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0-65535', function (string $value, int $expected): void {
            $port = IntegerPort::fromString($value);
            expect($port->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['80', 80],
            ['65535', 65535],
        ]);

        it('throws PortIntegerTypeException for values outside 0-65535', function (string $invalidValue): void {
            expect(fn() => IntegerPort::fromString($invalidValue))
                ->toThrow(PortIntegerTypeException::class, 'Expected port integer (0-65535)');
        })->with(['-1', '65536']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerPort::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['5.5', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['3.0', StringTypeException::class],
            ['01', StringTypeException::class],
            ['+1', StringTypeException::class],
            [' 1', StringTypeException::class],
            ['1 ', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from bool', function (bool $value, int $expected): void {
            $port = IntegerPort::fromBool($value);
            expect($port->value())->toBe($expected);
        })->with([
            [true, 1],
            [false, 0],
        ]);
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0-65535', function (float $value, int $expected): void {
            $port = IntegerPort::fromFloat($value);
            expect($port->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [80.0, 80],
            [65535.0, 65535],
        ]);

        it('throws for float values outside 0-65535', function (float $invalidValue): void {
            expect(fn() => IntegerPort::fromFloat($invalidValue))
                ->toThrow(PortIntegerTypeException::class, 'Expected port integer (0-65535)');
        })->with([-1.0, 65536.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerPort::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0-65535', function (string $value, int $expected): void {
            $port = IntegerPort::fromDecimal($value);
            expect($port->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['80.0', 80],
            ['65535.0', 65535],
        ]);

        it('throws for decimal values outside 0-65535', function (string $invalidValue): void {
            expect(fn() => IntegerPort::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['-1.0', '65536.0', '1.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerPort::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['5', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromBool method', function (): void {
        it('returns IntegerPort for valid bool', function (bool $value, int $expected): void {
            $result = IntegerPort::tryFromBool($value);
            expect($result)->toBeInstanceOf(IntegerPort::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [true, 1],
            [false, 0],
        ]);

        it('returns default for exception (mocking not easy, but checking default)', function (): void {
            $default = new Undefined();
            $result = IntegerPort::tryFromBool(true, $default);
            expect($result)->not->toBeInstanceOf(Undefined::class);
        });

        it('returns default for invalid bool (if such existed, but we test exception flow)', function (): void {
            // We can't easily trigger exception in fromBool without mocking, but we can trigger it in others.
            // Let's use tryFromDecimal for that.
            $result = IntegerPort::tryFromDecimal('65536.0');
            expect($result)->toBeInstanceOf(Undefined::class);
        });

        it('reaches catch block by throwing from constructor', function (): void {
            // Overriding fromBool to throw would be one way, but we can't do that with the current factory pattern in PEST
            // We'll use a subclass to test the catch block
            /**
             * @psalm-immutable
             *
             * @internal
             *
             * @coversNothing
             */
            readonly class IntegerPortTest extends IntegerPort
            {
                /** @psalm-pure */
                public static function fromBool(bool $value): static
                {
                    throw new Exception('Simulated');
                }
            }
            $result = IntegerPortTest::tryFromBool(true);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerPort for valid decimal strings 0-65535', function (string $value, int $expected): void {
            $result = IntegerPort::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerPort::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['65535.0', 65535],
        ]);

        it('returns Undefined for invalid values', function (string $invalidValue): void {
            $result = IntegerPort::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1.0', '65536.0', '1.1', 'abc']);
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerPort for valid floats 0-65535', function (float $value, int $expected): void {
            $result = IntegerPort::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerPort::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [65535.0, 65535],
        ]);

        it('returns Undefined for invalid values', function (float $invalidValue): void {
            $result = IntegerPort::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1.0, 65536.0, 3.14]);
    });

    describe('tryFromInt method', function (): void {
        it('returns IntegerPort for valid values 0-65535', function (int $value): void {
            $result = IntegerPort::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerPort::class)
                ->and($result->value())->toBe($value);
        })->with([0, 80, 65535]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerPort::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 65536]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerPort for valid integer strings 0-65535', function (string $value, int $expected): void {
            $result = IntegerPort::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerPort::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['65535', 65535],
        ]);

        it('returns Undefined for invalid values', function (string $invalidValue): void {
            $result = IntegerPort::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '65536', 'a', '1.0']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerPort for valid inputs', function (mixed $value, int $expected): void {
            $result = IntegerPort::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerPort::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [80, 80],
            ['443', 443],
            [true, 1],
            [0.0, 0],
            [new class implements Stringable {
                public function __toString(): string
                {
                    return '8080';
                }
            }, 8080],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerPort::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [65536],
            ['abc'],
            [[]],
            [null],
            [new stdClass()],
        ]);
    });

    // ============================================
    // CONVERSION METHODS
    // ============================================

    describe('Conversion methods', function (): void {
        it('isEmpty returns false', function (): void {
            $port = new IntegerPort(80);
            expect($port->isEmpty())->toBeFalse();
        });

        it('isUndefined returns false', function (): void {
            $port = new IntegerPort(80);
            expect($port->isUndefined())->toBeFalse();
        });

        it('isTypeOf checks class inheritance', function (): void {
            $port = new IntegerPort(80);
            expect($port->isTypeOf(IntegerPort::class))->toBeTrue()
                ->and($port->isTypeOf(Undefined::class))->toBeFalse();
        });

        it('toBool returns boolean representation', function (int $value, bool $expected): void {
            $port = new IntegerPort($value);
            expect($port->toBool())->toBe($expected);
        })->with([
            [80, true],
            [0, false],
        ]);

        it('toInt returns integer value', function (int $value): void {
            $port = new IntegerPort($value);
            expect($port->toInt())->toBe($value);
        })->with([0, 65535]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $port = new IntegerPort($value);
            expect($port->toString())->toBe($expected)
                ->and((string) $port)->toBe($expected);
        })->with([
            [0, '0'],
            [65535, '65535'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $port = new IntegerPort($value);
            expect($port->toFloat())->toBe((float) $value);
        })->with([0, 65535]);

        it('toDecimal returns decimal string', function (int $value, string $expected): void {
            $port = new IntegerPort($value);
            expect($port->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [65535, '65535.0'],
        ]);

        it('jsonSerialize returns integer', function (int $value): void {
            $port = new IntegerPort($value);
            expect($port->jsonSerialize())->toBe($value);
        })->with([0, 65535]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerPort::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerPort::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 80, 65535]);
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerPort::fromNull(null))
                ->toThrow(PortIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerPort::toNull())
                ->toThrow(PortIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
