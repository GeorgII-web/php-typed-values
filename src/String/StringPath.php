<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\PathStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * Path string (file or directory path).
 *
 * Validates basic path syntax and rejects control characters and invalid symbols.
 * Allows both forward and backslash separators for cross-platform compatibility.
 *
 * Example
 *  - $p = StringPath::fromString('/src/String');
 *    $p->toString(); // '/src/String'
 *  - StringPath::fromString(''); // throws PathStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringPath extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws PathStringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new PathStringTypeException('Expected non-empty path');
        }

        // Basic validation for common invalid characters in paths: ? % * : | " < > and null byte
        // We allow / and \ as separators.
        // We also need to be careful about \ in regex.
        if (preg_match('/[\x00-\x1f\x7f?%*:|"<>]/', $value)) {
            throw new PathStringTypeException(sprintf('Expected valid path, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws PathStringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /** @return non-empty-string */
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

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

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
    ): static|PrimitiveType {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                //                ($value instanceof self) => static::fromString($value->value()),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
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
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }
}
