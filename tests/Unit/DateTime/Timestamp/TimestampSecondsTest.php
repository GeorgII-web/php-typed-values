<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;

it('fromDateTime returns same instant and toString is unix timestamp', function (): void {
    $dt = new DateTimeImmutable('2001-09-09 01:46:40');
    $vo = TimestampSeconds::fromString('1000000000');

    expect($vo->value()->format('U'))->toBe($dt->format('U'))
        ->and($vo->toString())->toBe($dt->format('U'));
});

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = TimestampSeconds::fromDateTime($dt);

    expect($vo->value()->format('U'))->toBe('1735787045')
        ->and($vo->toString())->toBe('1735787045');
});

it('getFormat returns unix timestamp format U', function (): void {
    expect(TimestampSeconds::getFormat())->toBe('U');
});

it('fromDateTime normalizes timezone to UTC while preserving the instant', function (): void {
    // Source datetime has +03:00 offset, should be normalized to UTC internally
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+03:00');
    $vo = TimestampSeconds::fromDateTime($dt);

    expect($vo->value()->format('U'))->toBe($dt->format('U'))
        ->and($vo->toString())->toBe($dt->format('U'))
        ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
});

it('fromString throws on non-numeric input and reports details', function (): void {
    try {
        TimestampSeconds::fromString('not-a-number');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        expect($e)->toBeInstanceOf(PhpTypedValues\Code\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value')
            ->and((bool) preg_match('/(Error at|Warning at)/', $msg))->toBeTrue();
    }
});

it('fromString throws on trailing data (warnings/errors path)', function (): void {
    try {
        TimestampSeconds::fromString('1000000000 ');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        expect($e)->toBeInstanceOf(PhpTypedValues\Code\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value')
            ->and((bool) preg_match('/(Error at|Warning at)/', $msg))->toBeTrue();
    }
});

it('round-trip mismatch produces Unexpected conversion error for leading zeros', function (): void {
    try {
        TimestampSeconds::fromString('0000000005');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Code\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Unexpected conversion')
            ->and($e->getMessage())->toContain('0000000005')
            ->and($e->getMessage())->toContain('5');
    }
});
