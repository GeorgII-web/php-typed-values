<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\DateTime;

use DateTimeImmutable;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Exception\DateTimeTypeException;
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
    public const DEFAULT_ZONE = 'UTC';

    public function value(): DateTimeImmutable;

    public static function fromDateTime(DateTimeImmutable $value): static;

    /**
     * @template T of PrimitiveType
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;

    /**
     * @template T of PrimitiveType
     *
     * @param T                $default
     * @param non-empty-string $timezone
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        string $timezone = self::DEFAULT_ZONE,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType;

    /**
     * @param non-empty-string $timezone
     *
     * @throws DateTimeTypeException
     */
    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static;

    /**
     * @param non-empty-string $timezone
     */
    public function withTimeZone(string $timezone): static;

    public static function getFormat(): string;

    public function toString(): string;
}
