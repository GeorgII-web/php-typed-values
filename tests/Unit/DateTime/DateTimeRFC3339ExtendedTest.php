<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeRFC3339Extended;
use PhpTypedValues\Undefined\Alias\Undefined;

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = DateTimeRFC3339Extended::fromDateTime($dt);

    expect($vo->value()->format(\DATE_RFC3339_EXTENDED))->toBe('2025-01-02T03:04:05.000+00:00')
        ->and($vo->toString())->toBe('2025-01-02T03:04:05.000+00:00');
});

it('fromString parses valid RFC3339 and preserves timezone offset', function (): void {
    $vo = DateTimeRFC3339Extended::fromString('2030-12-31T23:59:59.000+03:00');

    expect($vo->toString())->toBe('2030-12-31T23:59:59.000+03:00')
        ->and($vo->value()->format(\DATE_RFC3339_EXTENDED))->toBe('2030-12-31T23:59:59.000+03:00');
});

it('fromString throws on invalid date parts (errors path)', function (): void {
    // invalid month 13 produces errors
    $call = fn() => DateTimeRFC3339Extended::fromString('2025-13-02T03:04:05.000+00:00');
    expect($call)->toThrow(PhpTypedValues\Exception\DateTimeTypeException::class);
});

it('fromString throws on trailing data (warnings path)', function (): void {
    // trailing space should trigger a warning from DateTime::getLastErrors()
    try {
        DateTimeRFC3339Extended::fromString('2025-01-02T03:04:05.000+00:00 ');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Invalid date time value');
    }
});

it('getFormat returns RFC3339 format', function (): void {
    expect(DateTimeRFC3339Extended::getFormat())->toBe(\DATE_RFC3339_EXTENDED);
});

it('fromString with both parse error and warning includes both details in the exception message', function (): void {
    // invalid month (error) + trailing space (warning)
    $input = '2025-13-02T03:04:05.000+00:00 ';
    try {
        DateTimeRFC3339Extended::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Invalid date time value')
            ->and($e->getMessage())->toContain('Error at')
            ->and($e->getMessage())->toContain('Warning at');
    }
});

it('fromString aggregates multiple parse warnings with proper concatenation and line breaks', function (): void {
    // Multiple invalid components to force several distinct parse errors
    $input = '2025-13-40T25:61:61.000+00:00';
    try {
        DateTimeRFC3339Extended::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // Header should be present and terminated with a newline
        $expectedHeader = \sprintf(
            'Invalid date time value "%s", use format "%s"',
            $input,
            \DATE_RFC3339_EXTENDED
        ) . \PHP_EOL;

        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain($expectedHeader)
            ->and($msg)->toContain('Invalid date time value "2025-13-40T25:61:61.000+00:00", use format "Y-m-d\TH:i:s.vP"
Warning at 29: The parsed date was invalid
')
            // No injected garbage from the mutation should appear
            ->and($msg)->not->toContain('PEST Mutator was here!');
    }
});

it('errors-only path keeps newline after header and after error line', function (): void {
    $input = '2025-01-02T03:04:05.000+00:00 ';
    try {
        DateTimeRFC3339Extended::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // must contain the header + newline and at least one warning line ending with newline
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value "2025-01-02T03:04:05.000+00:00 ", use format "Y-m-d\TH:i:s.vP"
Error at 29: Trailing data
');

        // Count total newlines: one after header + one after the warning line => at least 2
        expect(substr_count($msg, \PHP_EOL))->toBeGreaterThanOrEqual(2);
    }
});

it('double error message', function (): void {
    $input = '2025-12-02T03:04:05.000+ 00:00';
    try {
        DateTimeRFC3339Extended::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // must contain the header + newline and at least one warning line ending with newline
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value "2025-12-02T03:04:05.000+ 00:00", use format "Y-m-d\TH:i:s.vP"
Error at 23: The timezone could not be found in the database
Error at 24: Trailing data
');
    }
});

it('DateTimeRFC3339Extended::tryFromString returns value for valid RFC3339_EXTENDED string', function (): void {
    // DATE_RFC3339_EXTENDED uses milliseconds precision (3 digits)
    $s = '2025-01-02T03:04:05.123+00:00';
    $v = DateTimeRFC3339Extended::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeRFC3339Extended::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeRFC3339Extended::tryFromString returns Undefined for invalid string', function (): void {
    $u = DateTimeRFC3339Extended::tryFromString('2025-01-02T03:04:05+00:00');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    expect(DateTimeRFC3339Extended::tryFromString('2025-01-02T03:04:05.000+00:00')->jsonSerialize())->toBeString();
});

it('__toString returns RFC3339_EXTENDED formatted string', function (): void {
    $vo = DateTimeRFC3339Extended::fromString('2025-01-02T03:04:05.123+00:00');

    expect((string) $vo)->toBe('2025-01-02T03:04:05.123+00:00')
        ->and($vo->__toString())->toBe('2025-01-02T03:04:05.123+00:00');
});

it('tryFromMixed handles valid RFC3339_EXTENDED strings and invalid mixed inputs', function (): void {
    // valid string
    $ok = DateTimeRFC3339Extended::tryFromMixed('2025-01-02T03:04:05.000+00:00');

    // invalid types
    $badArr = DateTimeRFC3339Extended::tryFromMixed(['x']);
    $badNull = DateTimeRFC3339Extended::tryFromMixed(null);

    // stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '2025-01-02T03:04:05.000+00:00';
        }
    };
    $okStr = DateTimeRFC3339Extended::tryFromMixed($stringable);

    expect($ok)->toBeInstanceOf(DateTimeRFC3339Extended::class)
        ->and($ok->toString())->toBe('2025-01-02T03:04:05.000+00:00')
        ->and($okStr)->toBeInstanceOf(DateTimeRFC3339Extended::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for DateTimeRFC3339Extended', function (): void {
    $vo = DateTimeRFC3339Extended::fromString('2025-01-02T03:04:05.000+00:00');
    expect($vo->isEmpty())->toBeFalse();
});

it('isUndefined is always false for DateTimeRFC3339Extended', function (): void {
    $vo = DateTimeRFC3339Extended::fromString('2025-01-02T03:04:05.000+00:00');
    expect($vo->isUndefined())->toBeFalse();
});
