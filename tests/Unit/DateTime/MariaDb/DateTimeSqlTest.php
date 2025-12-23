<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\MariaDb\DateTimeSql;
use PhpTypedValues\Undefined\Alias\Undefined;

it('fromDateTime returns same instant and toString is SQL format', function (): void {
    $dt = new DateTimeImmutable('2025-01-02 03:04:05');
    $vo = DateTimeSql::fromDateTime($dt);

    expect($vo->value()->format('Y-m-d H:i:s'))->toBe('2025-01-02 03:04:05')
        ->and($vo->toString())->toBe('2025-01-02 03:04:05');
});

it('fromString parses valid SQL string', function (): void {
    $vo = DateTimeSql::fromString('2030-12-31 23:59:59');

    expect($vo->toString())->toBe('2030-12-31 23:59:59')
        ->and($vo->value()->format('Y-m-d H:i:s'))->toBe('2030-12-31 23:59:59');
});

it('fromString throws on invalid date parts', function (): void {
    $call = fn() => DateTimeSql::fromString('2025-13-02 03:04:05');
    expect($call)->toThrow(PhpTypedValues\Exception\DateTimeTypeException::class);
});

it('getFormat returns SQL format', function (): void {
    expect(DateTimeSql::getFormat())->toBe('Y-m-d H:i:s');
});

it('casts to string via __toString and jsonSerialize equals toString', function (): void {
    $vo = DateTimeSql::fromString('2025-01-02 03:04:05');

    expect((string) $vo)->toBe($vo->toString())
        ->and($vo->jsonSerialize())->toBe($vo->toString());
});

it('DateTimeSql::tryFromString returns value for valid SQL string', function (): void {
    $s = '2025-01-02 03:04:05';
    $v = DateTimeSql::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeSql::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeSql::tryFromString returns Undefined for invalid string', function (): void {
    $u = DateTimeSql::tryFromString('2025-01-02T03:04:05+00:00');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed handles valid SQL strings and invalid mixed inputs', function (): void {
    $ok = DateTimeSql::tryFromMixed('2025-01-02 03:04:05');
    $badArr = DateTimeSql::tryFromMixed(['x']);
    $badNull = DateTimeSql::tryFromMixed(null);

    expect($ok)->toBeInstanceOf(DateTimeSql::class)
        ->and($ok->toString())->toBe('2025-01-02 03:04:05')
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty and isUndefined are always false for DateTimeSql', function (): void {
    $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
    expect($vo->isEmpty())->toBeFalse()
        ->and($vo->isUndefined())->toBeFalse();
});

it('withTimeZone returns a new instance with updated timezone', function (): void {
    $vo = DateTimeSql::fromString('2025-01-02 03:04:05', 'UTC');
    $vo2 = $vo->withTimeZone('Europe/Berlin');

    expect($vo2)->toBeInstanceOf(DateTimeSql::class)
        ->and($vo2->toString())->toBe('2025-01-02 03:04:05')
        ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');
});

it('tryFromString and tryFromMixed accept custom timezone', function (): void {
    $s = '2025-01-02 04:04:05';
    $vo1 = DateTimeSql::tryFromString($s, 'Europe/Berlin');
    expect($vo1)->toBeInstanceOf(DateTimeSql::class)
        ->and($vo1->toString())->toBe('2025-01-02 03:04:05')
        ->and($vo1->value()->getOffset())->toBe(0);

    $vo2 = DateTimeSql::tryFromMixed($s, 'Europe/Berlin');
    expect($vo2)->toBeInstanceOf(DateTimeSql::class)
        ->and($vo2->toString())->toBe('2025-01-02 03:04:05')
        ->and($vo2->value()->getOffset())->toBe(0);
});
