<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeAtom;

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
