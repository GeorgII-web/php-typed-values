<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(PrimitiveTypeAbstract::class);

/**
 * Mock concrete implementation for testing abstract class.
 *
 * @internal
 *
 * @covers \PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract
 */
readonly class PrimitiveTypeAbstractTest extends PrimitiveTypeAbstract
{
    public function __construct(private mixed $value)
    {
    }

    public static function callDecimalToBool(string $value): bool
    {
        return self::decimalToBool($value);
    }

    public static function callFloatToBool(float $value): bool
    {
        return self::floatToBool($value);
    }

    public static function callFloatToString(float $value, ?bool $roundTripConversion = null): string
    {
        if ($roundTripConversion === true) {
            return self::floatToString($value, true);
        }

        if ($roundTripConversion === false) {
            return self::floatToString($value, false);
        }

        return self::floatToString($value);
    }

    public static function callIntToBool(int $value): bool
    {
        return self::intToBool($value);
    }

    public static function callIntToFloat(int $value): float
    {
        return self::intToFloat($value);
    }

    public static function callIntToString(int $value): string
    {
        return self::intToString($value);
    }

    public static function callStringToFloat(string $value, ?bool $roundTripConversion = null): float
    {
        if ($roundTripConversion === true) {
            return self::stringToFloat($value, true);
        }

        if ($roundTripConversion === false) {
            return self::stringToFloat($value, false);
        }

        return self::stringToFloat($value);
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return true;
    }

    public function isUndefined(): bool
    {
        return $this->value instanceof Undefined;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}

describe('Concrete PrimitiveType implementation', function () {
    beforeEach(function () {
        $this->primitive = new PrimitiveTypeAbstractTest('test value');
    });

    it('abstract and cannot be instantiated', function () {
        expect(PrimitiveTypeAbstract::class)
            ->toBeAbstract()
            ->and(class_exists(PrimitiveTypeAbstractTest::class))
            ->toBeTrue();
    });

    it('isEmpty method works correctly', function ($value, $expected) {
        $primitive = new PrimitiveTypeAbstractTest($value);

        expect($primitive->isEmpty())->toBe($expected);
    })->with([
        ['value' => '', 'expected' => true],
        ['value' => 'test', 'expected' => false],
        ['value' => 0, 'expected' => true],
        ['value' => 1, 'expected' => false],
        ['value' => [], 'expected' => true],
        ['value' => null, 'expected' => true],
    ]);

    it('isUndefined method identifies Undefined instances', function () {
        $undefined = new PrimitiveTypeAbstractTest(new Undefined());
        $defined = new PrimitiveTypeAbstractTest('some value');

        expect($undefined->isUndefined())->toBeTrue()
            ->and($defined->isUndefined())->toBeFalse();
    });

    it('floatToString method converts float to string correctly', function (float $src, ?string $expected) {
        if ($expected === null) {
            expect(fn() => PrimitiveTypeAbstractTest::callFloatToString($src))
                ->toThrow(FloatTypeException::class);

            return;
        }

        if ($expected === 'SPECIAL_STRING_EXCEPTION') {
            expect(fn() => PrimitiveTypeAbstractTest::callFloatToString($src))
                ->toThrow(StringTypeException::class);

            return;
        }

        if ($expected === 'SUB_EXCEPTION') {
            expect(fn() => PrimitiveTypeAbstractTest::callFloatToString($src))
                ->toThrow(FloatTypeException::class);

            return;
        }

        expect(PrimitiveTypeAbstractTest::callFloatToString($src))->toBe($expected);
    })->with([
        // ─────────────
        // ZEROES
        // ─────────────
        ['src' => 0.0, 'expected' => '0.0'],
        ['src' => -0.0, 'expected' => '0.0'],  // canonical zero
        ['src' => +0.0, 'expected' => '0.0'],

        // ─────────────
        // SIMPLE INTEGERS
        // ─────────────
        ['src' => 1.0, 'expected' => '1.0'],
        ['src' => -1.0, 'expected' => '-1.0'],
        ['src' => +1.0, 'expected' => '1.0'],

        ['src' => 2.0, 'expected' => '2.0'],
        ['src' => 10.0, 'expected' => '10.0'],

        // ─────────────
        // NORMAL DECIMALS
        // ─────────────
        ['src' => 0.5, 'expected' => '0.5'],
        ['src' => 0.25, 'expected' => '0.25'],
        ['src' => 0.75, 'expected' => '0.75'],
        ['src' => 1.5, 'expected' => '1.5'],
        ['src' => 3.75, 'expected' => '3.75'],
        ['src' => 0.1, 'expected' => '0.10000000000000001'],
        ['src' => 0.2, 'expected' => '0.20000000000000001'],
        ['src' => 0.3, 'expected' => '0.29999999999999999'],
        ['src' => 0.15, 'expected' => '0.14999999999999999'],
        ['src' => 0.3333333333333333, 'expected' => '0.33333333333333331'],

        // ─────────────
        // LONG FLOATS (originally exponential)
        // ─────────────
        ['src' => 1e10, 'expected' => '10000000000.0'],
        ['src' => 1.0e10, 'expected' => '10000000000.0'],
        ['src' => 1e16, 'expected' => '10000000000000000.0'],
        ['src' => 5e-324, 'expected' => 'SUB_EXCEPTION'],
        ['src' => 1e-323, 'expected' => 'SUB_EXCEPTION'],
        ['src' => 0.1111111116789012345678911111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111234567890, 'expected' => '0.11111111167890124'],

        // ─────────────
        // FRACTIONS / IRRATIONALS
        // ─────────────
        ['src' => 2 / 3, 'expected' => '0.66666666666666663'],
        ['src' => \M_PI, 'expected' => '3.14159265358979312'],

        // ─────────────
        // INVALID SPECIALS
        // ─────────────
        ['src' => \INF, 'expected' => 'SPECIAL_STRING_EXCEPTION'],
        ['src' => -\INF, 'expected' => 'SPECIAL_STRING_EXCEPTION'],
        ['src' => \NAN, 'expected' => 'SPECIAL_STRING_EXCEPTION'],
    ]);

    it('stringToFloat method converts string to float correctly', function (string $src, ?float $expected) {
        if ($expected === null) {
            expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat($src))
                ->toThrow(StringTypeException::class);

            return;
        }

        expect(PrimitiveTypeAbstractTest::callStringToFloat($src))->toBe($expected);
    })->with([
        // ─────────────
        // INVALID: non-numeric
        // ─────────────
        'NaN' => ['src' => 'NaN', 'expected' => null],
        'INF' => ['src' => 'INF', 'expected' => null],
        '-INF' => ['src' => '-INF', 'expected' => null],
        'pi()' => ['src' => 'pi()', 'expected' => null],

        // ─────────────
        // VALID: minimal floats
        // ─────────────
        '0.0' => ['src' => '0.0', 'expected' => 0.0],
        '1.0' => ['src' => '1.0', 'expected' => 1.0],
        '-1.0' => ['src' => '-1.0', 'expected' => -1.0],
        '0' => ['src' => '0.0', 'expected' => 0.0],
        '1' => ['src' => '1.0', 'expected' => 1.0],
        '-1' => ['src' => '-1.0', 'expected' => -1.0],
        // ─────────────
        // VALID: lossy decimals (now strictly failing if they don't round-trip exactly)
        // ─────────────
        '0.1' => ['src' => '0.1', 'expected' => null],
        '0.2' => ['src' => '0.2', 'expected' => null],
        '0.3' => ['src' => '0.3', 'expected' => null],
        '0.15' => ['src' => '0.15', 'expected' => null],
        '0.3333333333333333' => ['src' => '0.3333333333333333', 'expected' => null],

        // ─────────────
        // VALID: canonical decimals
        // ─────────────
        '0.1c' => ['src' => '0.10000000000000001', 'expected' => 0.1],
        '0.2c' => ['src' => '0.20000000000000001', 'expected' => 0.2],
        '0.3c' => ['src' => '0.29999999999999999', 'expected' => 0.3],
        '0.15c' => ['src' => '0.14999999999999999', 'expected' => 0.15],
        '0.3333c' => ['src' => '0.33333333333333331', 'expected' => 0.3333333333333333],

        // ─────────────
        // INVALID: malformed decimals
        // ─────────────
        '+0.0' => ['src' => '+0.0', 'expected' => null],
        '.' => ['src' => '.', 'expected' => null],
        '.0' => ['src' => '.0', 'expected' => null],
        '0.' => ['src' => '0.', 'expected' => null],
        '+.0' => ['src' => '+.0', 'expected' => null],
        '1.' => ['src' => '1.', 'expected' => null],
        '00.0' => ['src' => '00.0', 'expected' => null],
        '01.0' => ['src' => '01.0', 'expected' => null],

        // ─────────────
        // VALID: normal decimals (exact)
        // ─────────────
        '2.0' => ['src' => '2.0', 'expected' => 2.0],
        '10.5' => ['src' => '10.5', 'expected' => 10.5],
        '-10.5' => ['src' => '-10.5', 'expected' => -10.5],
        '0.25' => ['src' => '0.25', 'expected' => 0.25],
        '0.5' => ['src' => '0.5', 'expected' => 0.5],
        '0.125' => ['src' => '0.125', 'expected' => 0.125],
        '0.0625' => ['src' => '0.0625', 'expected' => 0.0625],

        // ─────────────
        // VALID: edge exact fractions
        // ─────────────
        '0.75' => ['src' => '0.75', 'expected' => 0.75],
        '1.5' => ['src' => '1.5', 'expected' => 1.5],
        '3.75' => ['src' => '3.75', 'expected' => 3.75],

        // ─────────────
        // INVALID: special exponential form (unless they match PHP's default cast)
        // ─────────────
        '1e10' => ['src' => '10000000000.0', 'expected' => 10000000000.0],
        '1.0e+10' => ['src' => '1.0e+10', 'expected' => null],
        '1e16' => ['src' => '1.0E+16', 'expected' => null],
        '5e-324' => ['src' => '5.0E-324', 'expected' => null],
        '1e-323' => ['src' => '1.0E-323', 'expected' => null],
        '1e-1' => ['src' => '0.1', 'expected' => null],
        '1e-1c' => ['src' => '0.10000000000000001', 'expected' => 0.1],
        '1.1e1' => ['src' => '11.0', 'expected' => 11.0],
        '1e0' => ['src' => '1.0', 'expected' => 1.0],
        '1.0e0' => ['src' => '1.0', 'expected' => 1.0],
        '5e-1' => ['src' => '0.5', 'expected' => 0.5],
        '125e-3' => ['src' => '0.125', 'expected' => 0.125],
        '1e1' => ['src' => '10.0', 'expected' => 10.0],
        '1e2' => ['src' => '100.0', 'expected' => 100.0],
        '9.007199254740992e15' => ['src' => '9007199254740992.0', 'expected' => 9007199254740992.0],
        '4.503599627370496e15' => ['src' => '4503599627370496.0', 'expected' => 4503599627370496.0],
        '2.2250738585072014e-308' => ['src' => '2.2250738585072014e-308', 'expected' => null],
        '1.7976931348623157e308' => ['src' => '1.7976931348623157e308', 'expected' => null],

        // ─────────────
        // INVALID: whitespace / junk
        // ─────────────
        ' 1.0' => ['src' => ' 1.0', 'expected' => null],
        '1.0 ' => ['src' => '1.0 ', 'expected' => null],
        "1.0\n" => ['src' => "1.0\n", 'expected' => null],
        '1.0foo' => ['src' => '1.0foo', 'expected' => null],
    ]);

    it('toString method returns string representation', function ($value, $expected) {
        $primitive = new PrimitiveTypeAbstractTest($value);

        expect($primitive->toString())->toBe($expected)
            ->and((string) $primitive)->toBe($expected);
    })->with([
        ['value' => 'test', 'expected' => 'test'],
        ['value' => 123, 'expected' => '123'],
        ['value' => 3.14, 'expected' => '3.14'],
        ['value' => true, 'expected' => '1'],
        ['value' => false, 'expected' => ''],
        ['value' => null, 'expected' => ''],
    ]);

    it('__toString magic method works correctly', function () {
        $primitive = new PrimitiveTypeAbstractTest('magic string');

        expect((string) $primitive)->toBe('magic string')
            ->and($primitive->__toString())->toBe('magic string');
    });

    it('decimalToBool works correctly', function (string $value, bool|string $expected) {
        if (\is_string($expected)) {
            expect(fn() => PrimitiveTypeAbstractTest::callDecimalToBool($value))
                ->toThrow(DecimalTypeException::class, $expected);
        } else {
            expect(PrimitiveTypeAbstractTest::callDecimalToBool($value))->toBe($expected);
        }
    })->with([
        ['1.0', true],
        ['0.0', false],
        ['2.0', 'Decimal "2.0" has no valid strict bool value'],
    ]);

    it('floatToBool works correctly', function (float $value, bool|string $expected) {
        if (\is_string($expected)) {
            expect(fn() => PrimitiveTypeAbstractTest::callFloatToBool($value))
                ->toThrow(FloatTypeException::class, $expected);
        } else {
            expect(PrimitiveTypeAbstractTest::callFloatToBool($value))->toBe($expected);
        }
    })->with([
        [1.0, true],
        [0.0, false],
        [0.5, 'Float "0.5" has no valid strict bool value'],
    ]);

    it('intToBool works correctly', function (int $value, bool|string $expected) {
        if (\is_string($expected)) {
            expect(fn() => PrimitiveTypeAbstractTest::callIntToBool($value))
                ->toThrow(IntegerTypeException::class, $expected);
        } else {
            expect(PrimitiveTypeAbstractTest::callIntToBool($value))->toBe($expected);
        }
    })->with([
        [1, true],
        [0, false],
        [2, 'Integer "2" has no valid strict bool value'],
    ]);

    it('jsonSerialize returns value for JSON encoding', function () {
        $data = ['key' => 'value'];
        $primitive = new PrimitiveTypeAbstractTest($data);

        expect($primitive->jsonSerialize())->toBe($data)
            ->and(json_encode($primitive))->toBe(json_encode($data));
    });

    it('Undefined type works correctly', function () {
        $undefined = new Undefined();

        expect($undefined->isEmpty())->toBeTrue()
            ->and($undefined->isUndefined())->toBeTrue();
    });
});

describe('Equality and comparison', function () {
    it('Different instances with same value should not be equal', function () {
        $primitive1 = new PrimitiveTypeAbstractTest('test');
        $primitive2 = new PrimitiveTypeAbstractTest('test');

        expect($primitive1)->not->toBe($primitive2)
            ->and($primitive1->toString())->toBe($primitive2->toString());
    });

    it('String casting works in concatenation', function () {
        $primitive = new PrimitiveTypeAbstractTest('world');
        $result = 'Hello ' . $primitive;

        expect($result)->toBe('Hello world');
    });
});

describe('Static utility methods coverage', function () {
    it('covers stringToDateTimeZone exception (lines 75-76)', function (): void {
        try {
            PrimitiveTypeAbstractTest::stringToDateTimeZone('Invalid/Timezone');
            $this->fail('Expected ZoneDateTimeTypeException was not thrown');
        } catch (ZoneDateTimeTypeException $e) {
            expect($e->getCode())->toBe(0);
        }
    });

    it('covers floatToString normalization (lines 178, 181)', function (): void {
        expect(PrimitiveTypeAbstractTest::callFloatToString(0.5))->toBe('0.5')
            ->and(PrimitiveTypeAbstractTest::callFloatToString(-0.5))->toBe('-0.5');

        // Shadowing sprintf in the namespace of the class under test.
        // This MUST be done before any calls that might trigger it if it's already cached.
        if (!\function_exists('PhpTypedValues\Base\Primitive\sprintf')) {
            eval('namespace PhpTypedValues\Base\Primitive { function sprintf($f, ...$args) { 
                if ($f === "%.17f" && ($args[0] ?? null) === 0.123456781) return ".12345678100000000"; 
                if ($f === "%.17f" && ($args[0] ?? null) === -0.123456781) return "-.12345678100000000";
                return \sprintf($f, ...$args); 
            } }');
        }

        expect(PrimitiveTypeAbstractTest::callFloatToString(0.123456781))->toBe('0.123456781')
            ->and(PrimitiveTypeAbstractTest::callFloatToString(-0.123456781))->toBe('-0.123456781');
    });

    it('covers intToString protective check', function (): void {
        expect(PrimitiveTypeAbstractTest::callIntToString(0))->toBe('0')
            ->and(PrimitiveTypeAbstractTest::callIntToString(\PHP_INT_MAX))->toBe((string) \PHP_INT_MAX)
            ->and(PrimitiveTypeAbstractTest::callIntToString(\PHP_INT_MIN))->toBe((string) \PHP_INT_MIN);

        for ($i = 0; $i < 63; ++$i) {
            $val = 1 << $i;
            expect(PrimitiveTypeAbstractTest::callIntToString($val))->toBe((string) $val);
            $negVal = -$val;
            expect(PrimitiveTypeAbstractTest::callIntToString($negVal))->toBe((string) $negVal);
        }
    });

    it('covers stringToFloat with success and error paths', function (): void {
        expect(PrimitiveTypeAbstractTest::callStringToFloat('1.5'))->toBe(1.5);

        // Exercise the default value for $roundTripConversion
        expect(PrimitiveTypeAbstractTest::callStringToFloat('1.5', null))->toBe(1.5);

        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat('0.100000000'))
            ->toThrow(StringTypeException::class);

        expect(PrimitiveTypeAbstractTest::callStringToFloat('0.10000000000000001'))
            ->toBe(0.1); // as it stored in memory 0.10000000000000001

        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat('abc'))
            ->toThrow(StringTypeException::class);

        // Try to trigger line 275 (precision loss)
        $precisionLoss = '0.1234567890123456789';
        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat($precisionLoss))
            ->toThrow(StringTypeException::class);

        // Exercise $roundTripConversion = false
        expect(PrimitiveTypeAbstractTest::callStringToFloat('0.1', false))->toBe(0.1);

        // Exercise explicit $roundTripConversion = true
        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat('0.1', true))
            ->toThrow(StringTypeException::class);
    });

    it('covers floatToString with roundTripConversion false', function (): void {
        // Exercise $roundTripConversion = false
        expect(PrimitiveTypeAbstractTest::callFloatToString(0.1, false))->toBe('0.10000000000000001');

        // Exercise the default value for $roundTripConversion
        expect(PrimitiveTypeAbstractTest::callFloatToString(0.1, null))->toBe('0.10000000000000001');

        // Exercise explicit $roundTripConversion = true
        expect(PrimitiveTypeAbstractTest::callFloatToString(0.1, true))->toBe('0.10000000000000001');
    });
});
