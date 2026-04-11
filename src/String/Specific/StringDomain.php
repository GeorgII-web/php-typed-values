<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\DomainStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function preg_match;
use function sprintf;

/**
 * Domain name string.
 *
 * Validates that the string is a valid domain name.
 *
 * @psalm-immutable
 */
readonly class StringDomain extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws DomainStringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new DomainStringTypeException('Expected non-empty domain name');
        }

        // Basic domain regex validation
        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i', $value)) {
            throw new DomainStringTypeException(sprintf('Expected valid domain name, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws DomainStringTypeException
     * @throws DomainStringTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws DomainStringTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws DomainStringTypeException
     * @throws DomainStringTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws DomainStringTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws DomainStringTypeException
     */
    public static function fromNull(null $value): never
    {
        throw new DomainStringTypeException('StringDomain type cannot be created from null');
    }

    /**
     * @throws DomainStringTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @psalm-assert-if-true static $this
     */
    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * @throws DomainStringTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value);
    }

    /**
     * @throws DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value);
    }

    /**
     * @throws DomainStringTypeException
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value);
    }

    /**
     * @throws DomainStringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value);
    }

    /**
     * @throws DomainStringTypeException
     */
    public static function toNull(): never
    {
        throw new DomainStringTypeException('StringDomain type cannot be converted to null');
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromBool($value);
        } catch (DomainStringTypeException|StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromDecimal(string $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromDecimal($value);
        } catch (DomainStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromFloat($value);
        } catch (FloatTypeException|DomainStringTypeException|StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromInt(int $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromInt($value);
        } catch (DomainStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromMixed(mixed $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        if (is_scalar($value) || $value instanceof Stringable) {
            try {
                return static::fromString((string) $value);
            } catch (DomainStringTypeException) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = new Undefined()): PrimitiveTypeAbstract|static
    {
        try {
            return static::fromString($value);
        } catch (DomainStringTypeException) {
            return $default;
        }
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
