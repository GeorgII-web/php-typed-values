<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use Exception;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstractAbstract;
use PhpTypedValues\Exception\String\UuidStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
use function preg_match;
use function sprintf;
use function strtolower;

/**
 * UUID version 7 (time‑ordered, Unix time‑based) string.
 *
 * Accepts an RFC 4122 UUID v7 in canonical dashed form. Input is treated
 * case‑insensitively and normalized to lowercase for consistent storage and
 * comparison.
 *
 * Example
 *  - $u = StringUuidV7::fromString('01890F2A-5BCD-7DEF-9ABC-1234567890AB');
 *    $u->toString(); // '01890f2a-5bcd-7def-9abc-1234567890ab'
 *  - StringUuidV7::fromString('not-a-uuid'); // throws UuidStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringUuidV7 extends StringTypeAbstractAbstract
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws UuidStringTypeException
     */
    public function __construct(string $value)
    {
        // Normalize to lowercase for consistent representation
        $normalized = strtolower($value);

        if ($normalized === '') {
            // Distinct message for empty input
            throw new UuidStringTypeException(sprintf('Expected non-empty UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        // RFC 4122-style UUID v7:
        // xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx (hex, case-insensitive)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $normalized) !== 1) {
            throw new UuidStringTypeException(sprintf('Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        $this->value = $normalized;
    }

    /**
     * @throws UuidStringTypeException
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

    /** @return non-empty-string */
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
