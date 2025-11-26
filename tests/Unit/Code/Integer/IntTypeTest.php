<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerStandart;

it('fromInt returns exact value and toString matches', function (): void {
    $i1 = IntegerStandart::fromInt(-10);
    expect($i1->value())->toBe(-10)
        ->and($i1->toString())->toBe('-10');

    $i2 = IntegerStandart::fromInt(0);
    expect($i2->value())->toBe(0)
        ->and($i2->toString())->toBe('0');
});

it('fromString parses valid integer strings including negatives and leading zeros', function (): void {
    expect(IntegerStandart::fromString('-15')->value())->toBe(-15)
        ->and(IntegerStandart::fromString('0')->toString())->toBe('0')
        ->and(IntegerStandart::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-integer strings', function (): void {
    $invalid = ['5a', 'a5', '', 'abc', ' 5', '5 ', '+5', '05', '--5', '3.14'];
    foreach ($invalid as $str) {
        expect(fn() => IntegerStandart::fromString($str))->toThrow(IntegerTypeException::class);
    }
});
