<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\HttpStatusCodeIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerHttpStatusCode;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerHttpStatusCode::class);

describe('IntegerHttpStatusCode', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 100-599', function (int $value): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->value())->toBe($value);
        })->with([100, 200, 404, 500, 599]);

        it('throws for values outside 100-599', function (int $invalidValue): void {
            expect(fn() => new IntegerHttpStatusCode($invalidValue))
                ->toThrow(HttpStatusCodeIntegerTypeException::class, 'Expected HTTP status code integer (100-599)');
        })->with([99, 600, -1, 1000]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 100-599', function (int $value): void {
            $code = IntegerHttpStatusCode::fromInt($value);
            expect($code->value())->toBe($value);
        })->with([100, 200, 404, 500, 599]);

        it('throws for values outside 100-599', function (int $invalidValue): void {
            expect(fn() => IntegerHttpStatusCode::fromInt($invalidValue))
                ->toThrow(HttpStatusCodeIntegerTypeException::class, 'Expected HTTP status code integer (100-599)');
        })->with([99, 600, -1, 1000]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 100-599', function (string $value, int $expected): void {
            $code = IntegerHttpStatusCode::fromString($value);
            expect($code->value())->toBe($expected);
        })->with([
            ['100', 100],
            ['200', 200],
            ['599', 599],
        ]);

        it('throws HttpStatusCodeTypeException for values outside 100-599', function (string $invalidValue): void {
            expect(fn() => IntegerHttpStatusCode::fromString($invalidValue))
                ->toThrow(HttpStatusCodeIntegerTypeException::class, 'Expected HTTP status code integer (100-599)');
        })->with(['99', '600']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerHttpStatusCode::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['5.5', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['200.0', StringTypeException::class],
            ['0200', StringTypeException::class],
            ['+200', StringTypeException::class],
            [' 200', StringTypeException::class],
            ['200 ', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('throws HttpStatusCodeTypeException from bool because 0/1 are not in range 100-599', function (bool $value): void {
            expect(fn() => IntegerHttpStatusCode::fromBool($value))
                ->toThrow(HttpStatusCodeIntegerTypeException::class);
        })->with([true, false]);
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 100-599', function (float $value, int $expected): void {
            $code = IntegerHttpStatusCode::fromFloat($value);
            expect($code->value())->toBe($expected);
        })->with([
            [100.0, 100],
            [200.0, 200],
            [599.0, 599],
        ]);

        it('throws for float values outside 100-599', function (float $invalidValue): void {
            expect(fn() => IntegerHttpStatusCode::fromFloat($invalidValue))
                ->toThrow(HttpStatusCodeIntegerTypeException::class, 'Expected HTTP status code integer (100-599)');
        })->with([99.0, 600.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerHttpStatusCode::fromFloat(200.1))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 100-599', function (string $value, int $expected): void {
            $code = IntegerHttpStatusCode::fromDecimal($value);
            expect($code->value())->toBe($expected);
        })->with([
            ['100.0', 100],
            ['200.0', 200],
            ['599.0', 599],
        ]);

        it('throws for decimal values outside 100-599', function (string $invalidValue): void {
            expect(fn() => IntegerHttpStatusCode::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['99.0', '600.0', '200.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerHttpStatusCode::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['200', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromBool method', function (): void {
        it('returns Undefined for bool (as 0/1 are out of range)', function (bool $value): void {
            $result = IntegerHttpStatusCode::tryFromBool($value);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([true, false]);

        it('reaches catch block by throwing from constructor', function (): void {
            /**
             * @psalm-immutable
             *
             * @internal
             *
             * @coversNothing
             */
            readonly class IntegerHttpStatusCodeTest extends IntegerHttpStatusCode
            {
                /** @psalm-pure */
                public static function fromBool(bool $value): static
                {
                    throw new Exception('Simulated');
                }
            }
            $result = IntegerHttpStatusCodeTest::tryFromBool(true);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerHttpStatusCode for valid decimal strings 100-599', function (string $value, int $expected): void {
            $result = IntegerHttpStatusCode::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerHttpStatusCode::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['100.0', 100],
            ['599.0', 599],
        ]);

        it('returns Undefined for invalid values', function (string $invalidValue): void {
            $result = IntegerHttpStatusCode::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['99.0', '600.0', '200.1', 'abc']);
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerHttpStatusCode for valid floats 100-599', function (float $value, int $expected): void {
            $result = IntegerHttpStatusCode::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerHttpStatusCode::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [100.0, 100],
            [599.0, 599],
        ]);

        it('returns Undefined for invalid values', function (float $invalidValue): void {
            $result = IntegerHttpStatusCode::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([99.0, 600.0, 200.1]);
    });

    describe('tryFromInt method', function (): void {
        it('returns IntegerHttpStatusCode for valid values 100-599', function (int $value): void {
            $result = IntegerHttpStatusCode::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerHttpStatusCode::class)
                ->and($result->value())->toBe($value);
        })->with([100, 200, 599]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerHttpStatusCode::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([99, 600]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerHttpStatusCode for valid integer strings 100-599', function (string $value, int $expected): void {
            $result = IntegerHttpStatusCode::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerHttpStatusCode::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['100', 100],
            ['599', 599],
        ]);

        it('returns Undefined for invalid values', function (string $invalidValue): void {
            $result = IntegerHttpStatusCode::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['99', '600', 'a', '200.0']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerHttpStatusCode for valid inputs', function (mixed $value, int $expected): void {
            $result = IntegerHttpStatusCode::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerHttpStatusCode::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [200, 200],
            ['404', 404],
            [500.0, 500],
            [new class implements Stringable {
                public function __toString(): string
                {
                    return '201';
                }
            }, 201],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerHttpStatusCode::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [99],
            [600],
            ['abc'],
            [true], // out of range
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
            $code = new IntegerHttpStatusCode(200);
            expect($code->isEmpty())->toBeFalse();
        });

        it('isUndefined returns false', function (): void {
            $code = new IntegerHttpStatusCode(200);
            expect($code->isUndefined())->toBeFalse();
        });

        it('isTypeOf checks class inheritance', function (): void {
            $code = new IntegerHttpStatusCode(200);
            expect($code->isTypeOf(IntegerHttpStatusCode::class))->toBeTrue()
                ->and($code->isTypeOf(Undefined::class))->toBeFalse();
        });

        it('toBool returns boolean representation', function (int $value, bool $expected): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->toBool())->toBe($expected);
        })->with([
            [200, true],
        ]);

        it('toInt returns integer value', function (int $value): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->toInt())->toBe($value);
        })->with([100, 599]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->toString())->toBe($expected)
                ->and((string) $code)->toBe($expected);
        })->with([
            [100, '100'],
            [599, '599'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->toFloat())->toBe((float) $value);
        })->with([100, 599]);

        it('toDecimal returns decimal string', function (int $value, string $expected): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->toDecimal())->toBe($expected);
        })->with([
            [100, '100.0'],
            [599, '599.0'],
        ]);

        it('jsonSerialize returns integer', function (int $value): void {
            $code = new IntegerHttpStatusCode($value);
            expect($code->jsonSerialize())->toBe($value);
        })->with([100, 599]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerHttpStatusCode::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerHttpStatusCode::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([100, 200, 599]);
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerHttpStatusCode::fromNull(null))
                ->toThrow(HttpStatusCodeIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerHttpStatusCode::toNull())
                ->toThrow(HttpStatusCodeIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
