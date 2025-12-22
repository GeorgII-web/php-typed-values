<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\Undefined\Alias\Undefined;

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = DateTimeAtom::fromDateTime($dt);

    expect($vo->value()->format(\DATE_ATOM))->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('fromString parses valid ATOM and preserves timezone offset', function (): void {
    $vo = DateTimeAtom::fromString('2030-12-31T23:59:59+03:00');

    expect($vo->toString())->toBe('2030-12-31T23:59:59+03:00')
        ->and($vo->value()->format(\DATE_ATOM))->toBe('2030-12-31T23:59:59+03:00');
});

it('fromString throws on invalid date parts (errors path)', function (): void {
    // invalid month 13 produces errors
    $call = fn() => DateTimeAtom::fromString('2025-13-02T03:04:05+00:00');
    expect($call)->toThrow(PhpTypedValues\Exception\DateTimeTypeException::class);
});

it('fromString throws on trailing data (warnings path)', function (): void {
    // trailing space should trigger a warning from DateTime::getLastErrors()
    try {
        DateTimeAtom::fromString('2025-01-02T03:04:05+00:00 ');
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($e->getMessage())->toContain('Invalid date time value');
    }
});

it('getFormat returns DATE_ATOM format', function (): void {
    expect(DateTimeAtom::getFormat())->toBe(\DATE_ATOM);
});

it('casts to string via __toString and jsonSerialize equals toString (ATOM)', function (): void {
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

    expect((string) $vo)->toBe($vo->toString())
        ->and($vo->jsonSerialize())->toBe($vo->toString());
});

it('fromString with both parse error and warning includes both details in the exception message', function (): void {
    // invalid month (error) + trailing space (warning)
    $input = '2025-13-02T03:04:05+00:00 ';
    try {
        DateTimeAtom::fromString($input);
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
    $input = '2025-13-40T25:61:61+00:00';
    try {
        DateTimeAtom::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // Header should be present and terminated with a newline
        $expectedHeader = \sprintf(
            'Invalid date time value "%s", use format "%s"',
            $input,
            \DATE_ATOM
        ) . \PHP_EOL;

        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
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
        DateTimeAtom::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // must contain the header + newline and at least one warning line ending with newline
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
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
        DateTimeAtom::fromString($input);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // must contain the header + newline and at least one warning line ending with newline
        expect($e)->toBeInstanceOf(PhpTypedValues\Exception\DateTimeTypeException::class)
            ->and($msg)->toContain('Invalid date time value "2025-12-02T03:04:05+ 00:00", use format "Y-m-d\TH:i:sP"
Error at 19: The timezone could not be found in the database
Error at 20: Trailing data
');
    }
});

it('DateTimeAtom::tryFromString returns value for valid ATOM string', function (): void {
    $s = '2025-01-02T03:04:05+00:00';
    $v = DateTimeAtom::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeAtom::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeAtom::tryFromString returns Undefined for invalid string', function (): void {
    // Missing timezone offset
    $u = DateTimeAtom::tryFromString('2025-01-02T03:04:05');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    expect(DateTimeAtom::tryFromString('2025-01-02T03:04:05+00:00')->jsonSerialize())->toBeString();
});

it('tryFromMixed handles valid ATOM strings and invalid mixed inputs', function (): void {
    // valid string
    $ok = DateTimeAtom::tryFromMixed('2025-01-02T03:04:05+00:00');

    // invalid types
    $badArr = DateTimeAtom::tryFromMixed(['x']);
    $badNull = DateTimeAtom::tryFromMixed(null);

    // stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '2025-01-02T03:04:05+00:00';
        }
    };
    $okStr = DateTimeAtom::tryFromMixed($stringable);

    expect($ok)->toBeInstanceOf(DateTimeAtom::class)
        ->and($ok->toString())->toBe('2025-01-02T03:04:05+00:00')
        ->and($okStr)->toBeInstanceOf(DateTimeAtom::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for DateTimeAtom', function (): void {
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isEmpty())->toBeFalse();
});

it('withTimeZone returns a new instance with updated timezone', function (): void {
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
    $vo2 = $vo->withTimeZone('Europe/Berlin');

    expect($vo2)->toBeInstanceOf(DateTimeAtom::class)
        ->and($vo2->toString())->toBe('2025-01-02T04:04:05+01:00')
        ->and($vo2->value()->getTimezone()->getName())->toBe('Europe/Berlin');

    // original is immutable
    expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('isUndefined is always false for DateTimeAtom', function (): void {
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
    expect($vo->isUndefined())->toBeFalse();
});
