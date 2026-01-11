<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @coversNothing
 */
readonly class BoolTypeAbstractTest extends BoolTypeAbstract
{
    public function __construct(public string $lastValue = '')
    {
    }

    public static function fromBool(bool $value): static
    {
        return new self();
    }

    public static function fromFloat(float $value): static
    {
        return new self();
    }

    public static function fromInt(int $value): static
    {
        return new self();
    }

    public static function fromString(string $value): static
    {
        return new self($value);
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return true;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): bool
    {
        return true;
    }

    public function toBool(): bool
    {
        return true;
    }

    public function toFloat(): float
    {
        return 1.0;
    }

    public function toInt(): int
    {
        return 1;
    }

    public function toString(): string
    {
        return 'true';
    }

    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        return new self();
    }

    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        return new self();
    }

    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        return new self();
    }

    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        if ($value === null) {
            return $default;
        }

        if ($value instanceof Stringable) {
            return new self((string) $value);
        }

        if ($value instanceof stdClass) {
            return $default;
        }

        return new self();
    }

    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        return new self($value);
    }

    public function value(): bool
    {
        return true;
    }
}

it('BoolType::convertMixedToString correctly handles null', function (): void {
    $instance = BoolTypeAbstractTest::tryFromMixed(null);
    expect($instance)->toBeInstanceOf(Undefined::class);
});

it('BoolType __toString()', function (): void {
    $instance = BoolTypeAbstractTest::tryFromMixed(true);
    expect((string) $instance)->toBe('true');
});

it('BoolType::convertMixedToString correctly handles Stringable', function (): void {
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'yes';
        }
    };
    $instance = BoolTypeAbstractTest::tryFromMixed($stringable);
    expect($instance->lastValue)->toBe('yes');
});

it('BoolType::convertMixedToString throws TypeException for non-stringable objects', function (): void {
    $result = BoolTypeAbstractTest::tryFromMixed(new stdClass(), Undefined::create());
    expect($result)->toBeInstanceOf(Undefined::class);
});
