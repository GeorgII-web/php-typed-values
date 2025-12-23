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
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
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
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value')
            ->and((bool) preg_match('/(Error at|Warning at)/', $msg))->toBeTrue();
    }
});

it('round-trip mismatch produces Unexpected conversion error for leading zeros', function (): void {
    try {
        TimestampSeconds::fromString('0000000005');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Unexpected conversion')
            ->and($e->getMessage())->toContain('0000000005')
            ->and($e->getMessage())->toContain('5');
    }
});

it('fromString throws when seconds are above supported range (max + 1)', function (): void {
    try {
        // One second beyond 9999-12-31T23:59:59Z
        TimestampSeconds::fromString('253402300800');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\ReasonableRangeDateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Timestamp "253402300800" out of supported range "-62135596800"-"253402300799"')
            ->and($e->getMessage())->toContain('253402300800');
    }
});

it('fromString throws when seconds are below supported range (min - 1)', function (): void {
    try {
        // One second before 0001-01-01T00:00:00Z
        TimestampSeconds::fromString('-62135596801');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\ReasonableRangeDateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Timestamp "-62135596801" out of supported range "-62135596800"-"253402300799"')
            ->and($e->getMessage())->toContain('-62135596801');
    }
});

it('fromString accepts maximum supported seconds (max boundary)', function (): void {
    // Exactly 9999-12-31T23:59:59Z
    $vo = TimestampSeconds::fromString('253402300799');

    expect($vo->toString())->toBe('253402300799')
        ->and($vo->value()->format('U'))->toBe('253402300799');
});

it('fromString accepts minimum supported seconds (min boundary)', function (): void {
    // Exactly 0001-01-01T00:00:00Z
    $vo = TimestampSeconds::fromString('-62135596800');

    expect($vo->toString())->toBe('-62135596800')
        ->and($vo->value()->format('U'))->toBe('-62135596800');
});

it('TimestampSeconds::tryFromString returns value for valid numeric seconds', function (): void {
    $s = '1735787045';
    $v = TimestampSeconds::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(TimestampSeconds::class)
        ->and($v->toString())
        ->toBe($s);
});

it('TimestampSeconds::tryFromString returns Undefined for invalid string', function (): void {
    $u = TimestampSeconds::tryFromString('not-a-number');
    expect($u)->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class);
});

it('jsonSerialize and toInt return integer', function (): void {
    $vo = TimestampSeconds::tryFromString('1735787045');
    expect($vo->jsonSerialize())->toBe(1735787045)
        ->and($vo->toInt())->toBe(1735787045);
});

it('__toString returns the seconds string', function (): void {
    $vo = TimestampSeconds::fromString('1735787045');

    expect((string) $vo)->toBe('1735787045')
        ->and($vo->__toString())->toBe('1735787045');
});

it('tryFromMixed handles valid numeric strings/ints and invalid mixed inputs', function (): void {
    // valid inputs
    $fromString = TimestampSeconds::tryFromMixed('1735787045');
    $fromIntMixed = TimestampSeconds::tryFromMixed(1735787045);
    $fromInt = TimestampSeconds::fromInt(1735787045);

    // invalid inputs
    $fromArray = TimestampSeconds::tryFromMixed(['x']);
    $fromNull = TimestampSeconds::tryFromMixed(null);

    // stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '1735787045';
        }
    };
    $fromStringable = TimestampSeconds::tryFromMixed($stringable);

    expect($fromString)->toBeInstanceOf(TimestampSeconds::class)
        ->and($fromIntMixed)->toBeInstanceOf(TimestampSeconds::class)
        ->and($fromInt)->toBeInstanceOf(TimestampSeconds::class)
        ->and($fromInt->toString())->toBe('1735787045')
        ->and($fromStringable)->toBeInstanceOf(TimestampSeconds::class)
        ->and($fromArray)->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class)
        ->and($fromNull)->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class);
});

it('isEmpty is always false for TimestampSeconds', function (): void {
    $vo = TimestampSeconds::fromString('0');
    expect($vo->isEmpty())->toBeFalse();
});

it('withTimeZone returns a new instance with updated timezone', function (): void {
    $vo = TimestampSeconds::fromString('1735787045');
    $vo2 = $vo->withTimeZone('Europe/Berlin');

    expect($vo2)->toBeInstanceOf(TimestampSeconds::class)
        ->and($vo2->toString())->toBe('1735787045')
        ->and($vo2->value()->getTimezone()->getName())->toBe('Europe/Berlin');
});

it('fromString and fromInt accept custom timezone', function (): void {
    $vo1 = TimestampSeconds::fromString('1735787045', 'Europe/Berlin');
    expect($vo1->toString())->toBe('1735787045')
        ->and($vo1->value()->getOffset())->toBe(3600);

    $vo2 = TimestampSeconds::fromInt(1735787045, 'America/New_York');
    expect($vo2->toString())->toBe('1735787045')
        ->and($vo2->value()->getOffset())->toBe(-18000);
});

it('tryFromString and tryFromMixed accept custom timezone', function (): void {
    $s = '1735787045';
    $vo1 = TimestampSeconds::tryFromString($s, 'Europe/Berlin');
    expect($vo1)->toBeInstanceOf(TimestampSeconds::class)
        ->and($vo1->toString())->toBe($s)
        ->and($vo1->value()->getOffset())->toBe(3600);

    $vo2 = TimestampSeconds::tryFromMixed($s, 'Europe/Berlin');
    expect($vo2)->toBeInstanceOf(TimestampSeconds::class)
        ->and($vo2->toString())->toBe($s)
        ->and($vo2->value()->getOffset())->toBe(3600);
});

it('isUndefined is always false for TimestampSeconds', function (): void {
    $vo = TimestampSeconds::fromString('0');
    expect($vo->isUndefined())->toBeFalse();
});
