<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\FileNameStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function pathinfo;
use function preg_match;
use function sprintf;

/**
 * File name string (no path separators).
 *
 * Validates that the string is a valid file name without path separators.
 * Rejects characters are typically invalid across major operating systems.
 *
 * Example
 *  - $f = StringFileName::fromString('image.jpg');
 *    $f->getFileNameOnly(); // 'image'
 *  - StringFileName::fromString('/path/file.txt'); // throws FileNameStringTypeException
 *
 * @psalm-immutable
 */
class StringFileName extends StringTypeAbstract
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws FileNameStringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new FileNameStringTypeException('Expected non-empty file name');
        }

        // Basic validation for common invalid characters in filenames: / \ ? % * : | " < > and null byte
        if (preg_match('/[\/\x00-\x1f\x7f\\\?%*:|"<>]/', $value)) {
            throw new FileNameStringTypeException(sprintf('Expected valid file name, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @throws StringTypeException
     * @throws FileNameStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws FileNameStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromDecimal(string $value)
    {
        return new static(static::decimalToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws FileNameStringTypeException
     * @throws StringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromFloat(float $value)
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws FileNameStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws FileNameStringTypeException
     *
     * @psalm-pure
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /**
     * Returns the file extension or an empty string if none exists.
     */
    public function getExtension(): string
    {
        return pathinfo($this->value, PATHINFO_EXTENSION);
    }

    /**
     * Returns the name of the file without the extension.
     */
    public function getFileNameOnly(): string
    {
        return pathinfo($this->value, PATHINFO_FILENAME);
    }

    public function isEmpty(): bool
    {
        return false;
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

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws StringTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws DecimalTypeException
     */
    public function toDecimal(): string
    {
        return static::stringToDecimal($this->value());
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value();
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception $exception) {
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
     *
     * @psalm-pure
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromDecimal($value);
        } catch (Exception $exception) {
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
     *
     * @psalm-pure
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception $exception) {
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
     *
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception $exception) {
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
     *
     * @psalm-pure
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
                case is_float($value):
                    return static::fromFloat($value);
                case is_int($value):
                    return static::fromInt($value);
                case is_bool($value):
                    return static::fromBool($value);
                case is_object($value) && method_exists($value, '__toString'):
                case is_scalar($value):
                    return static::fromString((string) $value);
                default:
                    throw new TypeException('Value cannot be cast to string');
            }
        } catch (Exception $exception) {
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
     *
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
