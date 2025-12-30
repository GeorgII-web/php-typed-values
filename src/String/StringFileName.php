<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\FileNameStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
use function pathinfo;
use function preg_match;
use function sprintf;

/**
 * File name string.
 *
 * Validates that the string is a valid file name (not a path).
 * Rejects path separators and characters that are typically invalid in file names
 * across major operating systems.
 *
 * Example
 *  - $f = StringFileName::fromString('image.jpg');
 *    $f->getFileNameOnly(); // "image"
 *    $f->getExtension(); // "jpg"
 *
 * @psalm-immutable
 */
class StringFileName extends StrType
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
     * @throws FileNameStringTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /**
     * Returns the name of the file without the extension.
     */
    public function getFileNameOnly(): string
    {
        return pathinfo($this->value, PATHINFO_FILENAME);
    }

    /**
     * Returns the file extension or an empty string if none exists.
     */
    public function getExtension(): string
    {
        return pathinfo($this->value, PATHINFO_EXTENSION);
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

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
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
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = null
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
}
