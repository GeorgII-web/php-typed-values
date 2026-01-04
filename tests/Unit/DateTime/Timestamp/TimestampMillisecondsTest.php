<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;
use PhpTypedValues\Undefined\Alias\Undefined;

it('fromString returns same instant and toString is milliseconds', function (): void {
    // 1,000,000,000,000 ms == 1,000,000,000 sec == 2001-09-09 01:46:40 UTC
    $dt = new DateTimeImmutable('2001-09-09 01:46:40');
    $vo = TimestampMilliseconds::fromString('1000000000000');

    expect($vo->value()->format('U'))->toBe($dt->format('U'))
        ->and($vo->toString())->toBe('1000000000000')
        ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
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
        expect($e)->toBeInstanceOf(\PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Expected milliseconds timestamp as digits');
    }
});

it('fromString throws on trailing data (non-digits)', function (): void {
    try {
        TimestampMilliseconds::fromString('1000000000000 ');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(\PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
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
        expect($e)->toBeInstanceOf(\PhpTypedValues\Exception\DateTime\ReasonableRangeDateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Timestamp "253402300800" out of supported range "-62135596800"-"253402300799"')
            ->and($e->getMessage())->toContain('253402300800');
    }
});

it('TimestampMilliseconds::tryFromString returns value for numeric milliseconds', function (): void {
    $s = '1735787045678';
    $v = TimestampMilliseconds::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($v->toString())
        ->toBe($s);
});

it('TimestampMilliseconds::tryFromString returns Undefined for non-digit input', function (): void {
    $u = TimestampMilliseconds::tryFromString('abc');
    expect($u)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize and toInt return integer', function (): void {
    $vo = TimestampMilliseconds::tryFromString('1732445696999');
    expect($vo->jsonSerialize())->toBe(1732445696999)
        ->and($vo->toInt())->toBe(1732445696999);
});

it('__toString returns the milliseconds string', function (): void {
    $vo = TimestampMilliseconds::fromString('1732445696123');

    expect((string) $vo)->toBe('1732445696123')
        ->and($vo->__toString())->toBe('1732445696123');
});

it('getFormat returns internal seconds.microseconds pattern', function (): void {
    expect(TimestampMilliseconds::getFormat())->toBe('U.u');
});

it('fromString accepts zero and round-trips to "0"', function (): void {
    $vo = TimestampMilliseconds::fromString('0');

    expect($vo->toString())->toBe('0')
        ->and($vo->value()->format('U.u'))->toBe('0.000000');
});

it('kills Stringable mutation in tryFromMixed', function () {
    // Create a unique default value to distinguish between "successful catch" and "incorrect branch"
    $customDefault = Undefined::create();

    // 1. Pass a value that is NOT Stringable, NOT string, and NOT int
    $invalidValue = [];

    // Original: Should hit 'default', throw TypeException, return $customDefault
    // Mutated: Hits 'true', tries (string)[], which might throw a TypeError
    // depending on PHP version/strict types, or just fail in fromString.

    expect(TimestampMilliseconds::tryFromMixed($invalidValue, 'UTC', $customDefault))
        ->toBe($customDefault);

    // 2. Explicitly test a Stringable object to ensure the branch works when NOT mutated
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '1704067200000';
        }
    };

    expect(TimestampMilliseconds::tryFromMixed($stringable))
        ->toBeInstanceOf(TimestampMilliseconds::class);
});

it('tryFromMixed handles valid numeric strings/ints and invalid mixed inputs', function (): void {
    // valid inputs
    $fromString = TimestampMilliseconds::tryFromMixed('1732445696123');
    $fromIntMixed = TimestampMilliseconds::tryFromMixed(1732445696123);
    $fromInt = TimestampMilliseconds::fromInt(1732445696123);

    // invalid inputs
    $fromArray = TimestampMilliseconds::tryFromMixed(['x']);
    $fromNull = TimestampMilliseconds::tryFromMixed(null);

    // stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '1732445696123';
        }
    };
    $fromStringable = TimestampMilliseconds::tryFromMixed($stringable);

    expect($fromString)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($fromIntMixed)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($fromInt)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($fromInt->toString())->toBe('1732445696123')
        ->and($fromStringable)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for TimestampMilliseconds', function (): void {
    $vo = TimestampMilliseconds::fromString('0');
    expect($vo->isEmpty())->toBeFalse();
});

it('withTimeZone returns a new instance with updated timezone', function (): void {
    $vo = TimestampMilliseconds::fromString('1732445696123');
    $vo2 = $vo->withTimeZone('Europe/Berlin');

    expect($vo2)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($vo2->toString())->toBe('1732445696123')
        ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');
});

it('fromString and fromInt accept custom timezone', function (): void {
    $vo1 = TimestampMilliseconds::fromString('1732445696123', 'Europe/Berlin');
    expect($vo1->toString())->toBe('1732445696123')
        ->and($vo1->value()->getOffset())->toBe(0);

    $vo2 = TimestampMilliseconds::fromInt(1732445696123, 'America/New_York');
    expect($vo2->toString())->toBe('1732445696123')
        ->and($vo2->value()->getOffset())->toBe(0);
});

it('tryFromString and tryFromMixed accept custom timezone', function (): void {
    $s = '1732445696123';
    $vo1 = TimestampMilliseconds::tryFromString($s, 'Europe/Berlin');
    expect($vo1)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($vo1->toString())->toBe($s)
        ->and($vo1->value()->getOffset())->toBe(0);

    $vo2 = TimestampMilliseconds::tryFromMixed($s, 'Europe/Berlin');
    expect($vo2)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($vo2->toString())->toBe($s)
        ->and($vo2->value()->getOffset())->toBe(0);
});

it('tryFromMixed handles Stringable object correctly', function (): void {
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '1732445696123';
        }
    };
    $result = TimestampMilliseconds::tryFromMixed($stringable);

    expect($result)->toBeInstanceOf(TimestampMilliseconds::class)
        ->and($result->toString())->toBe('1732445696123')
        ->and($result->isUndefined())->toBeFalse();
});

it('tryFromMixed returns Undefined for non-Stringable non-numeric objects', function (): void {
    $obj = new stdClass();
    $result = TimestampMilliseconds::tryFromMixed($obj);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
});

it('isUndefined is always false for TimestampMilliseconds', function (): void {
    $vo = TimestampMilliseconds::fromString('0');
    expect($vo->isUndefined())->toBeFalse();
});

it('isTypeOf returns true when class matches', function (): void {
    $vo = TimestampMilliseconds::fromString('1732445696123');
    expect($vo->isTypeOf(TimestampMilliseconds::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $vo = TimestampMilliseconds::fromString('1732445696123');
    expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $vo = TimestampMilliseconds::fromString('1732445696123');
    expect($vo->isTypeOf('NonExistentClass', TimestampMilliseconds::class, 'AnotherClass'))->toBeTrue();
});
