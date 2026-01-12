<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\DateTime;

use DateTimeImmutable;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for DateTime typed values.
 *
 * Declares the API for value objects backed by DateTimeImmutable, including
 * factories from strings and native DateTime, string formatting, and
 * a safe try-from factory returning Undefined on invalid input.
 *
 * Example
 *  - $v = MyDateTime::fromDateTime(new DateTimeImmutable('2025-01-02T03:04:05+00:00'));
 *  - $v->toString(); // '2025-01-02T03:04:05+00:00'
 *
 * @psalm-immutable
 */
interface DateTimeTypeInterface
{
    public const string DEFAULT_ZONE = 'UTC';
    public const string FORMAT = '';
    public const int MAX_TIMESTAMP_SECONDS = 253402300799; // 9999-12-31 23:59:59
    public const int MIN_TIMESTAMP_SECONDS = -62135596800; // 0001-01-01

    public static function fromDateTime(DateTimeImmutable $value): static;

    /**
     * @param non-empty-string $timezone
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static;

    public static function getFormat(): string;

    public function isTypeOf(string ...$classNames): bool;

    public function toString(): string;

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    public static function tryFromMixed(
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
    public static function tryFromString(
        string $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract;

    public function value(): DateTimeImmutable;

    /**
     * @param non-empty-string $timezone
     */
    public function withTimeZone(string $timezone): static;
}
