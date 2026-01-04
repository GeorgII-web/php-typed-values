<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use const FILTER_VALIDATE_URL;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\String\UrlStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function filter_var;
use function is_scalar;
use function is_string;
use function sprintf;

/**
 * Absolute URL string.
 *
 * Uses PHP's FILTER_VALIDATE_URL for pragmatic validation. The original
 * string is preserved on success and must be non-empty.
 *
 * Example
 *  - $u = StringUrl::fromString('https://example.com/path?x=1');
 *    (string) $u; // "https://example.com/path?x=1"
 *  - StringUrl::fromString('not a url'); // throws UrlStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringUrl extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws UrlStringTypeException
     */
    public function __construct(string $value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new UrlStringTypeException(sprintf('Expected valid URL, got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws UrlStringTypeException
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
