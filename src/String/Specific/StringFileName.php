<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstractAbstract;
use PhpTypedValues\Exception\String\FileNameStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
use function pathinfo;
use function preg_match;
use function sprintf;

/**
 * File name string (no path separators).
 *
 * Validates that the string is a valid file name without path separators.
 * Rejects characters typically invalid across major operating systems.
 *
 * Example
 *  - $f = StringFileName::fromString('image.jpg');
 *    $f->getFileNameOnly(); // 'image'
 *  - StringFileName::fromString('/path/file.txt'); // throws FileNameStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringFileName extends StringTypeAbstractAbstract
{
    /** @var non-empty-string */
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
     */
    public static function fromString(string $value): static
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
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }
}
