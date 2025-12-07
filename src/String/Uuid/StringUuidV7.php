<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Uuid;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Exception\UuidStringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function preg_match;
use function sprintf;
use function strtolower;

/**
 * UUID version 7 (time-ordered, Unix time-based).
 *
 * Example "01890f2a-5bcd-7def-8abc-1234567890ab"
 *
 * @psalm-immutable
 */
class StringUuidV7 extends StrType
{
    /** @var non-empty-string
     * @readonly */
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
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @throws UuidStringTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
