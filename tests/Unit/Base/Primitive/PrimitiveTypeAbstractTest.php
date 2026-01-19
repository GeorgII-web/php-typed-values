<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
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

    public static function callFloatToString(float $value, bool $roundTripConversion = true): string
    {
        return self::floatToString($value, $roundTripConversion);
    }

    public static function callIntToFloat(int $value): float
    {
        return self::intToFloat($value);
    }

    public static function callIntToString(int $value): string
    {
        return self::intToString($value);
    }

    public static function callStringToFloat(string $value, bool $roundTripConversion = true): float
    {
        return self::stringToFloat($value, $roundTripConversion);
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

it('PrimitiveType is abstract and cannot be instantiated', function () {
    expect(PrimitiveTypeAbstract::class)
        ->toBeAbstract()
        ->and(class_exists(PrimitiveTypeAbstractTest::class))
        ->toBeTrue();
});

describe('Concrete PrimitiveType implementation', function () {
    beforeEach(function () {
        $this->primitive = new PrimitiveTypeAbstractTest('test value');
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
        ['src' => 5e-324, 'expected' => null],
        ['src' => 1e-323, 'expected' => null],
        ['src' => 0.1111111116789012345678911111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111234567890, 'expected' => '0.11111111167890124'],

        // ─────────────
        // FRACTIONS / IRRATIONALS
        // ─────────────
        ['src' => 2 / 3, 'expected' => '0.66666666666666663'],
        ['src' => \M_PI, 'expected' => '3.14159265358979312'],

        // ─────────────
        // INVALID SPECIALS
        // ─────────────
        ['src' => \INF, 'expected' => null],
        ['src' => -\INF, 'expected' => null],
        ['src' => \NAN, 'expected' => null],
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
        // INVALID: integers (no decimal / no exponent)
        // ─────────────
        '0' => ['src' => '0', 'expected' => null],
        '1' => ['src' => '1', 'expected' => null],
        '-1' => ['src' => '-1', 'expected' => null],
        '+1' => ['src' => '+1', 'expected' => null],

        // ─────────────
        // VALID: minimal floats
        // ─────────────
        '0.0' => ['src' => '0.0', 'expected' => 0.0],
        '-0.0' => ['src' => '-0.0', 'expected' => null],
        '1.0' => ['src' => '1.0', 'expected' => 1.0],
        '-1.0' => ['src' => '-1.0', 'expected' => -1.0],

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
        // VALID: lossy decimals
        // ─────────────
        '0.1' => ['src' => '0.1', 'expected' => null],
        '0.2' => ['src' => '0.2', 'expected' => null],
        '0.3' => ['src' => '0.3', 'expected' => null],
        '0.15' => ['src' => '0.15', 'expected' => null],
        '0.3333333333333333' => ['src' => '0.3333333333333333', 'expected' => null],

        // ─────────────
        // VALID: edge exact fractions
        // ─────────────
        '0.75' => ['src' => '0.75', 'expected' => 0.75],
        '1.5' => ['src' => '1.5', 'expected' => 1.5],
        '3.75' => ['src' => '3.75', 'expected' => 3.75],

        // ─────────────
        // INVALID: special exponential form
        // ─────────────
        '1e10' => ['src' => '1e10', 'expected' => null],
        '1.0e+10' => ['src' => '1.0e+10', 'expected' => null],
        '1e16' => ['src' => '1e16', 'expected' => null],
        '5e-324' => ['src' => '5e-324', 'expected' => null],
        '1e-323' => ['src' => '1e-323', 'expected' => null],
        '1e-1' => ['src' => '1e-1', 'expected' => null],
        '3e-1' => ['src' => '3e-1', 'expected' => null],
        '1.1e1' => ['src' => '1.1e1', 'expected' => null],
        '1e0' => ['src' => '1e0', 'expected' => null],
        '1.0e0' => ['src' => '1.0e0', 'expected' => null],
        '5e-1' => ['src' => '5e-1', 'expected' => null],
        '125e-3' => ['src' => '125e-3', 'expected' => null],
        '1e1' => ['src' => '1e1', 'expected' => null],
        '1e2' => ['src' => '1e2', 'expected' => null],
        '9.007199254740992e15' => ['src' => '9.007199254740992e15', 'expected' => null],
        '4.503599627370496e15' => ['src' => '4.503599627370496e15', 'expected' => null],
        '2.2250738585072014e-308' => ['src' => '2.2250738585072014e-308', 'expected' => null],
        '1.7976931348623157e308' => ['src' => '1.7976931348623157e308', 'expected' => null],

        // ─────────────
        // VALID: beyond integer precision (rounded by IEEE-754)
        // ─────────────
        '9007199254740993.0' => ['src' => '9007199254740993.0', 'expected' => null],

        // ─────────────
        // VALID: beyond float precision
        // ─────────────
        '179769313486231...0.0' => [
            'src' => '1797693134862310000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000.0',
            'expected' => null,
        ],

        // ─────────────
        // INVALID: special values
        // ─────────────
        'NaN' => ['src' => 'NaN', 'expected' => null],
        'INF' => ['src' => 'INF', 'expected' => null],
        '-INF' => ['src' => '-INF', 'expected' => null],
        'pi()' => ['src' => 'pi()', 'expected' => null],

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
        expect(fn() => PrimitiveTypeAbstractTest::stringToDateTimeZone('Invalid/Timezone'))
            ->toThrow(ZoneDateTimeTypeException::class);
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

        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat('0.1'))
            ->toThrow(StringTypeException::class);

        expect(PrimitiveTypeAbstractTest::callStringToFloat('0.10000000000000001'))
            ->toBe(0.1); // as it stored in memory 0.10000000000000001

        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat('abc'))
            ->toThrow(StringTypeException::class);

        // Try to trigger line 275 (precision loss)
        $precisionLoss = '0.1234567890123456789';
        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat($precisionLoss))
            ->toThrow(StringTypeException::class);
    });
});
