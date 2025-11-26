<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;

it('fromString returns same instant and toString is milliseconds', function (): void {
    // 1,000,000,000,000 ms == 1,000,000,000 sec == 2001-09-09 01:46:40 UTC
    $dt = new DateTimeImmutable('2001-09-09 01:46:40');
    $vo = TimestampMilliseconds::fromString('1000000000000');

    expect($vo->value()->format('U'))->toBe($dt->format('U'))
        ->and($vo->toString())->toBe('1000000000000')
        ->and($vo->value()->getTimezone()->getName())->toBe('+00:00');
});

it('fromString maps remainder milliseconds to microseconds exactly (123 -> 123000)', function (): void {
    // 1732445696123 ms -> seconds=1732445696, remainder=123 ms => microseconds=123000
    $vo = TimestampMilliseconds::fromString('1732445696123');

    expect($vo->value()->format('U.u'))->toBe('1732445696.123000')
        ->and($vo->toString())->toBe('1732445696123');
});

it('fromString maps 999 remainder correctly to 999000 microseconds (no off-by-one)', function (): void {
    // 1732445696999 ms -> seconds=1732445696, remainder=999 ms => microseconds=999000
    $vo = TimestampMilliseconds::fromString('1732445696999');

    expect($vo->value()->format('U.u'))->toBe('1732445696.999000')
        ->and($vo->toString())->toBe('1732445696999');
});

it('fromDateTime preserves instant and renders milliseconds (truncates microseconds)', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05.678+00:00');
    $vo = TimestampMilliseconds::fromDateTime($dt);

    // Expected milliseconds: 1735787045 seconds and 678 ms
    expect($vo->value()->format('U'))->toBe('1735787045')
        ->and($vo->toString())->toBe('1735787045678')
        ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
});

it('toString truncates microseconds using divisor 1000, not 999 or 1001', function (): void {
    // Use a datetime with 999999 microseconds at a known second.
    // Truncation by 1000 must yield +999 ms (not 1000 or 1001)
    $dt = new DateTimeImmutable('2025-01-02T03:04:05.999999+00:00');
    $vo = TimestampMilliseconds::fromDateTime($dt);

    // Seconds part for this instant
    expect($vo->value()->format('U'))->toBe('1735787045')
        // Milliseconds should be 1735787045*1000 + 999
        ->and($vo->toString())->toBe('1735787045999');
});

it('fromDateTime normalizes timezone to UTC while preserving the instant', function (): void {
    // Source datetime has +03:00 offset, should be normalized to UTC internally
    $dt = new DateTimeImmutable('2025-01-02T03:04:05.123+03:00');
    $vo = TimestampMilliseconds::fromDateTime($dt);

    // Unix timestamp in seconds must be equal regardless of timezone
    expect($vo->value()->format('U'))->toBe($dt->format('U'))
        ->and($vo->value()->getTimezone()->getName())->toBe('UTC')
        // Milliseconds should reflect the source microseconds truncated to ms
        ->and($vo->toString())->toBe((string) ((int) $dt->format('U') * 1000 + (int) ((int) $dt->format('u') / 1000)));
});

it('fromString throws on non-digit input', function (): void {
    try {
        TimestampMilliseconds::fromString('not-a-number');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Code\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Expected milliseconds timestamp as digits');
    }
});

it('fromString throws on trailing data (non-digits)', function (): void {
    try {
        TimestampMilliseconds::fromString('1000000000000 ');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Code\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Expected milliseconds timestamp as digits');
    }
});

it('fromString throws when value is out of supported range (createFromFormat returns false)', function (): void {
    // 253402300800000 ms corresponds to 253402300800 seconds,
    // which is just beyond 9999-12-31T23:59:59Z (the typical max supported date).
    // This should make DateTimeImmutable::createFromFormat fail and return FALSE.
    $tooLargeMs = '253402300800000';

    try {
        TimestampMilliseconds::fromString($tooLargeMs);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Code\Exception\ReasonableRangeDateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Timestamp "253402300800" out of supported range "-62135596800"-"253402300799"')
            ->and($e->getMessage())->toContain('253402300800');
    }
});
