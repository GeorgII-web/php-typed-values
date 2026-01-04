<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstractAbstract;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;

/**
 * Generic string typed value.
 *
 * Wraps any PHP string without additional validation and provides
 * convenient factory and formatting helpers.
 *
 * Example
 *  - $v = StringStandard::fromString('hello');
 *    $v->toString(); // "hello"
 *  - (string) StringStandard::fromString('x'); // "x"
 *
 * @psalm-immutable
 */
readonly class StringStandard extends StringTypeAbstractAbstract
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function toString(): string
    {
        return $this->value();
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                null === $value => static::fromString(''),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }
}
