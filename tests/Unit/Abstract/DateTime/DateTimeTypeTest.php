<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeAtom;

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
