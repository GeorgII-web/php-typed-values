<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\DateTimeTypeException;
use PhpTypedValues\DateTime\DateTimeBasic;

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = DateTimeBasic::fromDateTime($dt);

    expect($vo->value()->format(\DATE_ATOM))->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('fromString parses several common formats', function (): void {
    expect(DateTimeBasic::fromString('2025-01-02T03:04:05+00:00')->toString())
        ->toBe('2025-01-02T03:04:05+00:00');

    // Zulu timezone
    $z = DateTimeBasic::fromString('2025-01-02T03:04:05Z');
    expect($z->value()->format('Y-m-d\TH:i:s\Z'))->toBe('2025-01-02T03:04:05Z');

    // Date only defaults to midnight in current timezone; ensure parse succeeds
    $d = DateTimeBasic::fromString('2025-01-02');
    expect($d->value())->toBeInstanceOf(DateTimeImmutable::class);

    // Space-separated format
    $s = DateTimeBasic::fromString('2025-01-02 03:04:05');
    expect($s->value())->toBeInstanceOf(DateTimeImmutable::class);
});

it('fromString rejects invalid strings', function (): void {
    foreach (['', 'not-a-date', '2025-13-01', '2025-01-32', '2025-01-02T25:00:00'] as $invalid) {
        expect(fn() => DateTimeBasic::fromString($invalid))
            ->toThrow(DateTimeTypeException::class);
    }
});

it('parses ISO 8601 without colon in offset via fallback and normalizes output', function (): void {
    // Not DATE_ATOM due to +0000 (no colon); should be accepted by fallback and normalized to +00:00
    $vo = DateTimeBasic::fromString('2025-01-02T03:04:05+0000');
    expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('parses ISO 8601 with negative offset without colon via fallback and normalizes output', function (): void {
    // Example with -02:30 offset written without colon; fallback should accept and normalize to -02:30
    $vo = DateTimeBasic::fromString('2025-01-02T03:04:05-0230');
    expect($vo->toString())->toBe('2025-01-02T03:04:05-02:30');
});

it('parses ISO 8601 with positive offset without colon via fallback and normalizes output', function (): void {
    // Example with +02:30 offset written without colon; fallback should accept and normalize to +02:30
    $vo = DateTimeBasic::fromString('2025-01-02T03:04:05+0230');
    expect($vo->toString())->toBe('2025-01-02T03:04:05+02:30');
});

it('rejects RFC 2822 even though PHP can parse it (guarded fallback)', function (): void {
    // Parsed by DateTimeImmutable, but lacks the strict ISO-8601 heuristic (no "T" separator)
    expect(fn() => DateTimeBasic::fromString('Thu, 02 Jan 2025 03:04:05 +0000'))
        ->toThrow(DateTimeTypeException::class);
});

it('rejects strings that createFromFormat parses with warnings (trailing data)', function (): void {
    // Matches 'Y-m-d H:i:s' but with trailing timezone token causing warnings; fallback will also reject (no 'T')
    expect(fn() => DateTimeBasic::fromString('2025-01-02 03:04:05 UTC'))
        ->toThrow(DateTimeTypeException::class);
});

it('normalizes Zulu input to +00:00 via toString()', function (): void {
    // Parsed by the explicit 'Y-m-d\\TH:i:s\\Z' format, toString should output DATE_ATOM with +00:00
    $vo = DateTimeBasic::fromString('2025-01-02T03:04:05Z');
    expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('date-only input produces midnight instant and serializes via toString()', function (): void {
    $vo = DateTimeBasic::fromString('2025-01-02');
    // We cannot assert the exact offset because it depends on the environment timezone,
    // but we can assert that toString() returns an ISO-8601 string for the same day and midnight time.
    $str = $vo->toString();
    expect($str)->toStartWith('2025-01-02T00:00:00');
});
