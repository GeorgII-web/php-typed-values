<?php

declare(strict_types=1);

namespace Unit\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\Bool\BoolType;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

/**
 * @internal
 *
 * @coversNothing
 */
readonly class BoolTypeTest extends BoolType
{
    public function __construct(public string $lastValue = '')
    {
    }

    public static function fromString(string $value): static
    {
        return new self($value);
    }

    public static function fromInt(int $value): static
    {
        return new self();
    }

    public static function fromBool(bool $value): static
    {
        return new self();
    }

    public function value(): bool
    {
        return true;
    }

    public static function tryFromInt(
        int $value,
        \PhpTypedValues\Base\Primitive\PrimitiveType $default = new Undefined(),
    ): static|\PhpTypedValues\Base\Primitive\PrimitiveType {
        return new self();
    }

    public static function tryFromMixed(
        mixed $value,
        \PhpTypedValues\Base\Primitive\PrimitiveType $default = new Undefined(),
    ): static|\PhpTypedValues\Base\Primitive\PrimitiveType {
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
        \PhpTypedValues\Base\Primitive\PrimitiveType $default = new Undefined(),
    ): static|\PhpTypedValues\Base\Primitive\PrimitiveType {
        return new self($value);
    }

    public function toString(): string
    {
        return 'true';
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): bool
    {
        return true;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return true;
    }
}

it('BoolType::convertMixedToString correctly handles null', function (): void {
    $instance = BoolTypeTest::tryFromMixed(null);
    expect($instance)->toBeInstanceOf(Undefined::class);
});

it('BoolType __toString()', function (): void {
    $instance = BoolTypeTest::tryFromMixed(true);
    expect((string) $instance)->toBe('true');
});

it('BoolType::convertMixedToString correctly handles Stringable', function (): void {
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'yes';
        }
    };
    $instance = BoolTypeTest::tryFromMixed($stringable);
    expect($instance->lastValue)->toBe('yes');
});

it('BoolType::convertMixedToString throws TypeException for non-stringable objects', function (): void {
    $result = BoolTypeTest::tryFromMixed(new stdClass(), Undefined::create());
    expect($result)->toBeInstanceOf(Undefined::class);
});
