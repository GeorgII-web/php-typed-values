<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\Undefined\Alias\Undefined;

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

    expect($dt->format(\DATE_ATOM))->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('DateTimeImmutable has false and throws an exception', function (): void {
    expect(
        fn() => DateTimeAtom::fromString('')
    )->toThrow(PhpTypedValues\Exception\DateTimeTypeException::class);
});

it('throws DateTimeTypeException on unexpected conversion when input uses Z instead of +00:00', function (): void {
    $call = fn() => DateTimeAtom::fromString('2025-01-02T03:04:05Z');
    expect($call)->toThrow(PhpTypedValues\Exception\DateTimeTypeException::class);
});

it('__toString proxies to toString for DateTimeType', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');

    // Ensure UTC timezone as used by DateTimeType::fromDateTime by default
    $dt = $dt->setTimezone(new DateTimeZone('UTC'));

    $typed = DateTimeAtom::fromDateTime($dt);

    expect((string) $typed)
        ->toBe($typed->toString())
        ->and((string) $typed)
        ->toBe($dt->format(DateTimeAtom::getFormat()));
});

it('handles null value by creating empty string DateTime', function () {
    $result = DateTimeAtom::tryFromMixed(null);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue()
        ->and($result->isEmpty())->toBeTrue();
});

it('returns Undefined for invalid mixed datetime inputs', function (mixed $input): void {
    $result = DateTimeAtom::tryFromMixed($input);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
})->with([
    // Arrays
    ['input' => []],
    ['input' => ['invalid']],
    ['input' => ['year' => 2024, 'month' => 1]],

    // Objects without __toString
    ['input' => new stdClass()],
    ['input' => (object) ['date' => '2024-01-01']],

    // Invalid date strings
    ['input' => 'not-a-date'],
    ['input' => '2024-13-01'], // Invalid month
    ['input' => '2024-01-32'], // Invalid day
    ['input' => '2024-02-30'], // Invalid day for February
    ['input' => '25:61:61'], // Invalid time
    ['input' => '2024-01-01 25:00:00'], // Invalid hour

    // Resources
    ['input' => fopen('php://memory', 'r')],

    // Callables/Closures
    ['input' => fn() => '2024-01-01'],
    ['input' => 'DateTimeImmutable'],

    // Invalid numeric strings
    ['input' => '1e100'], // Scientific notation
    ['input' => '999999999999999999999999999999'], // Too large

    // Special values
    ['input' => \INF],
    ['input' => \NAN],

    // Binary data
    ['input' => "\x00\x01\x02"],
]);
