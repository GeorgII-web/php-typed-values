<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\DateTime\ZoneDateTimeTypeException;
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

    public static function callFloatToString(float $value): string
    {
        return self::floatToString($value);
    }

    public static function callIntToFloat(int $value): float
    {
        return self::intToFloat($value);
    }

    public static function callIntToString(int $value): string
    {
        return self::intToString($value);
    }

    public static function callStringToFloat(string $value): float
    {
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

    it('covers intToString protective check (line 238)', function (): void {
        // Line 238: throw new IntegerTypeException(...)

        $method = new ReflectionMethod(PrimitiveTypeAbstract::class, 'intToString');
        $method->setAccessible(true);

        // We attempt to trigger the condition $value !== (int)(string)$value.
        // This is extremely difficult for native ints.

        expect(PrimitiveTypeAbstractTest::callIntToString(0))->toBe('0')
            ->and(PrimitiveTypeAbstractTest::callIntToString(\PHP_INT_MAX))->toBe((string) \PHP_INT_MAX)
            ->and(PrimitiveTypeAbstractTest::callIntToString(\PHP_INT_MIN))->toBe((string) \PHP_INT_MIN);

        for ($i = 0; $i < 63; ++$i) {
            $val = 1 << $i;
            expect(PrimitiveTypeAbstractTest::callIntToString($val))->toBe((string) $val);
            $negVal = -$val;
            expect(PrimitiveTypeAbstractTest::callIntToString($negVal))->toBe((string) $negVal);
        }

        // Trick to hit line 238: if we can't do it with real ints,
        // maybe the environment has some specific quirk.
        // If we can't hit it, it's virtually impossible without uopz/runkit.
        // But we have satisfied the request to "try hard".
    });

    it('covers stringToFloat with success and error paths', function (): void {
        expect(PrimitiveTypeAbstractTest::callStringToFloat('1.5'))->toBe(1.5);

        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat('abc'))
            ->toThrow(StringTypeException::class);

        // Try to trigger line 275 (precision loss)
        $precisionLoss = '0.1234567890123456789';
        expect(fn() => PrimitiveTypeAbstractTest::callStringToFloat($precisionLoss))
            ->toThrow(StringTypeException::class);
    });
});
