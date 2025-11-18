<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Base;

use GeorgiiWeb\PhpTypedValues\Contracts\TypedValueInterface;

/**
 * @template T
 *
 * @implements TypedValueInterface<T>
 *
 * @psalm-immutable
 */
abstract class TypedValue implements TypedValueInterface
{
    /**
     * @psalm-var T
     */
    protected readonly mixed $value;

    /**
     * @psalm-param T $value
     */
    final public function __construct(mixed $value)
    {
        $this->assertValid($value);
        $this->value = $value;
    }

    /**
     * @psalm-return T
     */
    final public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @psalm-param T $value
     * @psalm-external-mutation-free
     */
    final public function setValue(mixed $value): static
    {
        return new static($value);
    }

    /**
     * @psalm-external-mutation-free
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    public static function fromString(string $value): static
    {
        return new static(static::castFromString($value));
    }

    /**
     * @psalm-param T $value
     */
    abstract protected function assertValid(mixed $value): void;

    /**
     * Convert from string into underlying type before validation.
     * Default just returns the string; descendants override for specific types.
     *
     * @psalm-return T
     */
    protected static function castFromString(string $value): mixed
    {
        return $value;
    }
}
