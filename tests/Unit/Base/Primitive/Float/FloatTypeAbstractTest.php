<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Float\FloatTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(FloatTypeAbstract::class);

/**
 * @internal
 *
 * @covers \PhpTypedValues\Base\Primitive\Float\FloatTypeAbstract
 */
readonly class FloatTypeAbstractTest extends FloatTypeAbstract
{
    public function __construct(private float $val)
    {
    }

    public static function fromString(string $value): static
    {
        return new static(parent::stringToFloat($value));
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return true;
    }

    public static function fromFloat(float $value): static
    {
        return new static($value);
    }

    public function value(): float
    {
        return $this->val;
    }

    public function toString(): string
    {
        return (string) $this->val;
    }

    public function jsonSerialize(): float
    {
        return $this->val;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromFloat($value);
        } catch (Exception) {
            return $default;
        }
    }

    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return match (true) {
                \is_float($value), \is_int($value) => static::fromFloat($value),
                ($value instanceof self) => static::fromFloat($value->value()),
                \is_bool($value) => static::fromFloat($value ? 1.0 : 0.0),
                \is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to float'),
            };
        } catch (Exception) {
            return $default;
        }
    }

    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromString($value);
        } catch (Exception) {
            return $default;
        }
    }

    public static function fromInt(int $value): static
    {
        return new static($value);
    }

    public static function fromBool(bool $value): static
    {
        return new static((float) $value);
    }

    public static function tryFromInt(int $value,
        PrimitiveTypeAbstract $default = new Undefined(), ): static|PrimitiveTypeAbstract
    {
        return new static($value);
    }

    public static function tryFromBool(bool $value,
        PrimitiveTypeAbstract $default = new Undefined(), ): static|PrimitiveTypeAbstract
    {
        return new static((float) $value);
    }

    public function toFloat(): float
    {
        return $this->value();
    }

    public function toInt(): int
    {
        return (int) $this->value();
    }

    public function toBool(): bool
    {
        return (bool) $this->value();
    }
}

it('exercises FloatType through a concrete stub', function (): void {
    expect(FloatTypeAbstractTest::tryFromMixed('1.5'))->toBeInstanceOf(FloatTypeAbstractTest::class)
        ->and(FloatTypeAbstractTest::tryFromMixed('1.5')->value())->toBe(1.5)
        ->and(FloatTypeAbstractTest::tryFromMixed(['invalid']))->toBeInstanceOf(Undefined::class)
        ->and(FloatTypeAbstractTest::tryFromMixed(['invalid'], Undefined::create()))->toBeInstanceOf(Undefined::class)
        ->and(FloatTypeAbstractTest::tryFromString('2.5'))->toBeInstanceOf(FloatTypeAbstractTest::class)
        ->and(FloatTypeAbstractTest::tryFromString('2.5')->value())->toBe(2.5)
        ->and(FloatTypeAbstractTest::tryFromString('invalid'))->toBeInstanceOf(Undefined::class)
        ->and(FloatTypeAbstractTest::tryFromString('invalid', Undefined::create()))->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed covers null and Stringable inputs', function (): void {
    // covers line 83: $value === null
    $fromNull = FloatTypeAbstractTest::tryFromMixed(null);
    expect($fromNull)->toBeInstanceOf(Undefined::class);

    // covers line 87: $value instanceof Stringable
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '3.14';
        }
    };
    $fromStringable = FloatTypeAbstractTest::tryFromMixed($stringable);
    expect($fromStringable)->toBeInstanceOf(FloatTypeAbstractTest::class)
        ->and($fromStringable->value())->toBe(3.14);

    // Cover Line 91: throw new TypeException
    $fromArray = FloatTypeAbstractTest::tryFromMixed([1]);
    expect($fromArray)->toBeInstanceOf(Undefined::class);
});

it('fromString parses valid float strings including negatives, decimals, and scientific', function (): void {
    expect(FloatStandard::fromString('-15.25')->value())->toBe(-15.25)
        ->and(FloatStandard::fromString('5')->value())->toBe(5.0)
        ->and(FloatStandard::fromString('5.0')->value())->toBe(5.0)
        ->and(FloatStandard::fromString('0.0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('-0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('-0.0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('-0.0')->toString())->toBe('-0')
        ->and(FloatStandard::fromString('1.2345678912345')->toString())->toBe('1.2345678912345')
        ->and(FloatStandard::fromString('42')->value())->toBe(42.0)
        ->and(FloatStandard::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-numeric strings and magic conversions', function (): void {
    expect(fn() => FloatStandard::fromString('5a'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('a5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString(''))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('abc'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('--5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('5,5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('5 5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('1.23456789012345678'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0.666666666666666629'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0005'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('5.00000'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0005.0'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0005.000'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('1e3'))->toThrow(FloatTypeException::class);
});

it('fromString precious for string and float difference', function (): void {
    expect(FloatStandard::fromFloat(2 / 3)->value())->toBe(0.6666666666666666) // accepts "messy" real float value
        ->and(FloatStandard::fromString((string) (2 / 3))->value())->toBe(0.66666666666667); // "string cast" uses serialize_precision to have a precious value
});

it('__toString proxies to toString for FloatType', function (): void {
    $v = new FloatStandard(1.5);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('1.5');
});

it('fromFloat returns exact value and toString matches', function (): void {
    $f1 = FloatStandard::fromFloat(-10.5);
    expect($f1->value())->toBe(-10.5)
        ->and($f1->toString())->toBe('-10.5');

    $f2 = FloatStandard::fromFloat(0.0);
    expect($f2->value())->toBe(0.0)
        ->and($f2->toString())->toBe('0');
});
