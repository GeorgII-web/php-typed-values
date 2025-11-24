<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatBasic;

it('fromFloat returns exact value and toString matches', function (): void {
    $f1 = FloatBasic::fromFloat(-10.5);
    expect($f1->value())->toBe(-10.5)
        ->and($f1->toString())->toBe('-10.5');

    $f2 = FloatBasic::fromFloat(0.0);
    expect($f2->value())->toBe(0.0)
        ->and($f2->toString())->toBe('0');
});

it('fromString parses valid float strings including negatives, decimals, and scientific', function (): void {
    expect(FloatBasic::fromString('-15.25')->value())->toBe(-15.25)
        ->and(FloatBasic::fromString('0007.5')->value())->toBe(7.5)
        ->and(FloatBasic::fromString('+5.0')->value())->toBe(5.0)
        ->and(FloatBasic::fromString('1e3')->value())->toBe(1000.0)
        ->and(FloatBasic::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-numeric strings', function (): void {
    $invalid = ['5a', 'a5', '', 'abc', '--5', '5,5'];
    foreach ($invalid as $str) {
        expect(fn() => FloatBasic::fromString($str))->toThrow(FloatTypeException::class);
    }
});
