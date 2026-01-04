<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeW3C;
use PhpTypedValues\Undefined\Alias\Undefined;

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = DateTimeW3C::fromDateTime($dt);

    expect($vo->value()->format(\DATE_RFC3339))->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('fromString parses valid RFC3339 and preserves timezone offset', function (): void {
    $vo = DateTimeW3C::fromString('2030-12-31T23:59:59+03:00');

    expect($vo->toString())->toBe('2030-12-31T20:59:59+00:00')
        ->and($vo->value()->format(\DATE_RFC3339))->toBe('2030-12-31T20:59:59+00:00');
});

it('fromString throws on invalid date parts (errors path)', function (): void {
    // invalid month 13 produces errors
    $call = fn() => DateTimeW3C::fromString('2025-13-02T03:04:05+00:00');
    expect($call)->toThrow(PhpTypedValues\Exception\DateTime\DateTimeTypeException::class);
});

it('fromString throws on trailing data (warnings path)', function (): void {
    // trailing space should trigger a warning from DateTime::getLastErrors()
    try {
        DateTimeW3C::fromString('2025-01-02T03:04:05+00:00 ');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Invalid date time value');
    }
});

it('getFormat returns RFC3339 format', function (): void {
    expect(DateTimeW3C::getFormat())->toBe(\DATE_RFC3339);
});

it('fromString with both parse error and warning includes both details in the exception message', function (): void {
    // invalid month (error) + trailing space (warning)
    $input = '2025-13-02T03:04:05+00:00 ';
    try {
        DateTimeW3C::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Invalid date time value')
            ->and($e->getMessage())->toContain('Error at')
            ->and($e->getMessage())->toContain('Warning at');
    }
});

it('fromString aggregates multiple parse warnings with proper concatenation and line breaks', function (): void {
    // Multiple invalid components to force several distinct parse errors
    $input = '2025-13-40T25:61:61+00:00';
    try {
        DateTimeW3C::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // Header should be present and terminated with a newline
        $expectedHeader = \sprintf(
            'Invalid date time value "%s", use format "%s"',
            $input,
            \DATE_RFC3339
        ) . \PHP_EOL;

        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
            ->and($msg)->toContain($expectedHeader)
            ->and($msg)->toContain('Invalid date time value "2025-13-40T25:61:61+00:00", use format "Y-m-d\TH:i:sP"
Warning at 25: The parsed date was invalid
')
            // No injected garbage from the mutation should appear
            ->and($msg)->not->toContain('PEST Mutator was here!');
    }
});

it('errors-only path keeps newline after header and after error line', function (): void {
    $input = '2025-01-02T03:04:05+00:00 ';
    try {
        DateTimeW3C::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // must contain the header + newline and at least one warning line ending with newline
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value "2025-01-02T03:04:05+00:00 ", use format "Y-m-d\TH:i:sP"
Error at 25: Trailing data
');

        // Count total newlines: one after header + one after the warning line => at least 2
        expect(substr_count($msg, \PHP_EOL))->toBeGreaterThanOrEqual(2);
    }
});

it('double error message', function (): void {
    $input = '2025-12-02T03:04:05+ 00:00';
    try {
        DateTimeW3C::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // must contain the header + newline and at least one warning line ending with newline
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTime\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value "2025-12-02T03:04:05+ 00:00", use format "Y-m-d\TH:i:sP"
Error at 19: The timezone could not be found in the database
Error at 20: Trailing data
');
    }
});

it('DateTimeW3C::tryFromString returns value for valid W3C string', function (): void {
    $s = '2025-01-02T03:04:05+00:00';
    $v = DateTimeW3C::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeW3C::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeW3C::tryFromString returns Undefined for invalid string', function (): void {
    $u = DateTimeW3C::tryFromString('2025-01-02 03:04:05Z');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    expect(DateTimeW3C::tryFromString('2025-01-02T03:04:05+00:00')->jsonSerialize())->toBeString();
});

it('__toString returns W3C formatted string', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');

    expect((string) $vo)->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->__toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('tryFromMixed handles valid W3C/RFC3339 strings and invalid mixed inputs', function (): void {
    // valid string
    $ok = DateTimeW3C::tryFromMixed('2025-01-02T03:04:05+00:00');

    // invalid types
    $badArr = DateTimeW3C::tryFromMixed(['x']);
    $badNull = DateTimeW3C::tryFromMixed(null);

    // stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '2025-01-02T03:04:05+00:00';
        }
    };
    $okStr = DateTimeW3C::tryFromMixed($stringable);

    expect($ok)->toBeInstanceOf(DateTimeW3C::class)
        ->and($ok->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($okStr)->toBeInstanceOf(DateTimeW3C::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for DateTimeW3C', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isEmpty())->toBeFalse();
});

it('withTimeZone returns a new instance with updated timezone', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
    $vo2 = $vo->withTimeZone('Europe/Berlin');

    expect($vo2)->toBeInstanceOf(DateTimeW3C::class)
        ->and($vo2->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');

    // original is immutable
    expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('fromString accepts custom timezone', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T04:04:05+01:00', 'Europe/Berlin');
    expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->value()->getOffset())->toBe(0);
});

it('tryFromString and tryFromMixed accept custom timezone', function (): void {
    $s = '2025-01-02T04:04:05+01:00';
    $vo1 = DateTimeW3C::tryFromString($s, 'Europe/Berlin');
    expect($vo1)->toBeInstanceOf(DateTimeW3C::class)
        ->and($vo1->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo1->value()->getOffset())->toBe(0);

    $vo2 = DateTimeW3C::tryFromMixed($s, 'Europe/Berlin');
    expect($vo2)->toBeInstanceOf(DateTimeW3C::class)
        ->and($vo2->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo2->value()->getOffset())->toBe(0);
});

it('isUndefined is always false for DateTimeW3C', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isUndefined())->toBeFalse();
});

it('tryFromMixed handles DateTimeImmutable instance', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $result = DateTimeW3C::tryFromMixed($dt);

    expect($result)->toBeInstanceOf(DateTimeW3C::class)
        ->and($result->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($result->isUndefined())->toBeFalse();
});

it('tryFromMixed handles Stringable object', function (): void {
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '2025-01-02T03:04:05+00:00';
        }
    };
    $result = DateTimeW3C::tryFromMixed($stringable);

    expect($result)->toBeInstanceOf(DateTimeW3C::class)
        ->and($result->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($result->isUndefined())->toBeFalse();
});

it('tryFromMixed returns Undefined for non-Stringable objects', function (): void {
    $obj = new stdClass();
    $result = DateTimeW3C::tryFromMixed($obj);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
});

it('isTypeOf returns true when class matches', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isTypeOf(DateTimeW3C::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $vo = DateTimeW3C::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isTypeOf('NonExistentClass', DateTimeW3C::class, 'AnotherClass'))->toBeTrue();
});
