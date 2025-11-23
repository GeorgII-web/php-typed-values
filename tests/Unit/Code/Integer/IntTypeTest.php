<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Integer\IntegerBasic;

it('fromInt returns exact value and toString matches', function (): void {
    $i1 = IntegerBasic::fromInt(-10);
    expect($i1->value())->toBe(-10)
        ->and($i1->toString())->toBe('-10');

    $i2 = IntegerBasic::fromInt(0);
    expect($i2->value())->toBe(0)
        ->and($i2->toString())->toBe('0');
});

it('fromString parses valid integer strings including negatives and leading zeros', function (): void {
    expect(IntegerBasic::fromString('-15')->value())->toBe(-15)
        ->and(IntegerBasic::fromString('0')->toString())->toBe('0')
        ->and(IntegerBasic::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-integer strings', function (): void {
    $invalid = ['5a', 'a5', '', 'abc', ' 5', '5 ', '+5', '05', '--5', '3.14'];
    foreach ($invalid as $str) {
        expect(fn() => IntegerBasic::fromString($str))->toThrow(NumericTypeException::class);
    }
});
