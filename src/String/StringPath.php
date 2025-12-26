<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\PathStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function preg_match;
use function sprintf;

/**
 * Path string.
 *
 * Validates that the string is a valid path.
 *
 * Example
 *  - $p = StringPath::fromString('/src/String');
 *  - $p = StringPath::fromString('src\String\');
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
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return $default;
        }
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return $default;
        }
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

    public function toString(): string
    {
        return $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }
}
