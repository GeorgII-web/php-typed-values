<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\String;

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;

/**
 * Base implementation for string-typed values.
 *
 * Provides common formatting helpers for value objects backed by strings.
 * Concrete string types extend this class and add domain-specific
 * validation/normalization.
 *
 * Example
 *  - $v = MyString::fromString('hello');
 *  - $v->toString(); // "hello"
 *
 * @psalm-immutable
 */
abstract readonly class StrType extends PrimitiveType implements StrTypeInterface
{
    abstract public function value(): string;

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): mixed {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                //                ($value instanceof self) => static::fromString($value->value()),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                null === $value => static::fromString(''),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (TypeException) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): mixed {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (TypeException) {
            /** @var T */
            return $default;
        }
    }
}
