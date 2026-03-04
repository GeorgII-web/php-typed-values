<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\MimeTypeStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function explode;
use function is_scalar;
use function preg_match;
use function sprintf;

/**
 * MIME type string (e.g., "application/json", "image/png").
 *
 * Validates that the string is a valid MIME type format.
 *
 * @psalm-immutable
 */
readonly class StringMimeType extends StringTypeAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws MimeTypeStringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new MimeTypeStringTypeException('Expected non-empty MIME type');
        }

        // Basic validation for type/subtype format
        if (!preg_match('/^[a-z0-9.-]+\/[a-z0-9.+-]+$/i', $value)) {
            throw new MimeTypeStringTypeException(sprintf('Expected valid MIME type, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws StringTypeException
     * @throws MimeTypeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws MimeTypeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws MimeTypeStringTypeException
     * @throws StringTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws MimeTypeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws MimeTypeStringTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function getSubtype(): string
    {
        $parts = explode('/', $this->value);

        return $parts[1];
    }

    public function getType(): string
    {
        $parts = explode('/', $this->value);

        return $parts[0];
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
     * @throws StringTypeException
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
     * @throws StringTypeException
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value);
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromBool(bool $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromBool($value);
        } catch (StringTypeException|MimeTypeStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromDecimal(string $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromDecimal($value);
        } catch (MimeTypeStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromFloat(float $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromFloat($value);
        } catch (FloatTypeException|MimeTypeStringTypeException|StringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromInt(int $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromInt($value);
        } catch (MimeTypeStringTypeException) {
            return $default;
        }
    }

    /**
     * @psalm-pure
     */
    public static function tryFromMixed(mixed $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        if (is_scalar($value) || $value instanceof Stringable) {
            try {
                return static::fromString((string) $value);
            } catch (MimeTypeStringTypeException) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @psalm-pure
     */
    public static function tryFromString(string $value, PrimitiveTypeAbstract $default = new Undefined()): static|PrimitiveTypeAbstract
    {
        try {
            return static::fromString($value);
        } catch (MimeTypeStringTypeException) {
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
