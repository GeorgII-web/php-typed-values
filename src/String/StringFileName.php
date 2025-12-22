<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

use PhpTypedValues\Exception\FileNameStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\String\StrType;
use PhpTypedValues\Undefined\Alias\Undefined;

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
readonly class StringFileName extends StrType
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

    public static function tryFromMixed(mixed $value): static|Undefined
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    /**
     * @throws FileNameStringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
        }
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
}
