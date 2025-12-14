<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatStandard;

it('__toString proxies to toString for FloatType', function (): void {
    $v = new FloatStandard(1.5);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('1.5');
});

it('fromFloat returns exact value and toString matches', function (): void {
    $f1 = FloatStandard::fromFloat(-10.5);
    expect($f1->value())->toBe(-10.5)
        ->and($f1->toString())->toBe('-10.5');

    $f2 = FloatStandard::fromFloat(0.0);
    expect($f2->value())->toBe(0.0)
        ->and($f2->toString())->toBe('0');
});

it('fromString parses valid float strings including negatives, decimals, and scientific', function (): void {
    expect(FloatStandard::fromString('-15.25')->value())->toBe(-15.25)
        ->and(FloatStandard::fromString('0007.5')->value())->toBe(7.5)
        ->and(FloatStandard::fromString('+5.0')->value())->toBe(5.0)
        ->and(FloatStandard::fromString('1e3')->value())->toBe(1000.0)
        ->and(FloatStandard::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-numeric strings', function (): void {
    $invalid = ['5a', 'a5', '', 'abc', '--5', '5,5'];
    foreach ($invalid as $str) {
        expect(fn() => FloatStandard::fromString($str))->toThrow(FloatTypeException::class);
    }
});
