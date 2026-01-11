<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeAbstract;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Mock implementation of UndefinedTypeAbstract to test the abstract class.
 */
readonly class UndefinedTypeAbstractMock extends UndefinedTypeAbstract
{
    public static function create(): static
    {
        return new static();
    }

    public static function fromArray(array $value): static
    {
        return new static();
    }

    public static function fromBool(bool $value): static
    {
        return new static();
    }

    public static function fromFloat(float $value): static
    {
        return new static();
    }

    public static function fromInt(int $value): static
    {
        return new static();
    }

    public static function fromString(string $value): static
    {
        return new static();
    }

    public function isEmpty(): bool
    {
        return true;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return true;
    }

    public function isUndefined(): bool
    {
        return true;
    }

    public function jsonSerialize(): never
    {
        throw new UndefinedTypeException('Mock');
    }

    public function toArray(): never
    {
        throw new UndefinedTypeException('Mock');
    }

    public function toBool(): never
    {
        throw new UndefinedTypeException('Mock');
    }

    public function toFloat(): never
    {
        throw new UndefinedTypeException('Mock');
    }

    public function toInt(): never
    {
        throw new UndefinedTypeException('Mock');
    }

    public function toString(): string
    {
        throw new UndefinedTypeException('Mock: toString');
    }

    public static function tryFromArray(array $value, PrimitiveTypeAbstract $default = new Undefined()): static
    {
        return new static();
    }

    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = new Undefined()): static
    {
        return new static();
    }

    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = new Undefined()): static
    {
        return new static();
    }

    public static function tryFromInt(int $value, PrimitiveTypeAbstract $default = new Undefined()): static
    {
        return new static();
    }

    public static function tryFromMixed(mixed $value, PrimitiveTypeAbstract $default = new Undefined()): static
    {
        return new static();
    }

    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = new Undefined()): static
    {
        return new static();
    }

    public function value(): string
    {
        throw new UndefinedTypeException('Mock');
    }
}

it('UndefinedTypeAbstract implements UndefinedTypeInterface', function (): void {
    expect(is_subclass_of(UndefinedTypeAbstract::class, PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeInterface::class))->toBeTrue();
});

it('__toString calls toString and throws if toString throws', function (): void {
    $mock = UndefinedTypeAbstractMock::create();

    expect(fn() => (string) $mock)
        ->toThrow(UndefinedTypeException::class, 'Mock: toString');
});

/**
 * Mock implementation that returns a string for toString.
 */
readonly class UndefinedTypeSuccessMock extends UndefinedTypeAbstractMock
{
    public function toString(): string
    {
        return 'success';
    }
}

it('__toString returns value from toString if it does not throw', function (): void {
    $mock = UndefinedTypeSuccessMock::create();

    expect((string) $mock)->toBe('success');
});
