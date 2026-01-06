<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\DateTime;

use DateTimeImmutable;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base implementation for DateTime typed values.
 *
 * Provides strict parsing with detailed error aggregation, round-trip
 * validation against the format, timezone normalization, and reasonable
 * timestamp range checks.
 *
 * Example
 *  - $v = MyDateTime::fromString('2025-01-02T03:04:05+00:00');
 *  - $v->toString(); // '2025-01-02T03:04:05+00:00'
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract readonly class DateTimeTypeAbstract extends PrimitiveTypeAbstract implements DateTimeTypeInterface
{
    protected const FORMAT = '';
    protected const MAX_TIMESTAMP_SECONDS = 253402300799; // 9999-12-31 23:59:59
    protected const MIN_TIMESTAMP_SECONDS = -62135596800; // 0001-01-01

    abstract public static function fromDateTime(DateTimeImmutable $value): static;

    /**
     * @param non-empty-string $timezone
     */
    abstract public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static;

    abstract public static function getFormat(): string;

    abstract public function isTypeOf(string ...$classNames): bool;

    abstract public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    abstract public static function tryFromMixed(
        mixed $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    abstract public static function tryFromString(
        string $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    abstract public function value(): DateTimeImmutable;

    /**
     * @param non-empty-string $timezone
     */
    abstract public function withTimeZone(string $timezone): static;

    public function __toString(): string
    {
        return $this->toString();
    }
}
