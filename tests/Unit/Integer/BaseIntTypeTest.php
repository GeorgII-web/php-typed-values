<?php

declare(strict_types=1);

use GeorgiiWeb\PhpTypedValues\Exception\IntegerTypeException;
use GeorgiiWeb\PhpTypedValues\Types\Integer\Integer;

it('fromInt returns exact value and toString matches', function (): void {
    $i1 = Integer::fromInt(-10);
    expect($i1->value())->toBe(-10)
        ->and($i1->toString())->toBe('-10');

    $i2 = Integer::fromInt(0);
    expect($i2->value())->toBe(0)
        ->and($i2->toString())->toBe('0');
});

it('fromString parses valid integer strings including negatives and leading zeros', function (): void {
    expect(Integer::fromString('-15')->value())->toBe(-15)
        ->and(Integer::fromString('0007')->value())->toBe(7)
        ->and(Integer::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-integer strings', function (): void {
    $invalid = ['+5', ' 5', '5 ', '5a', '', 'abc', '--5', '3.14'];
    foreach ($invalid as $str) {
        expect(fn() => Integer::fromString($str))->toThrow(IntegerTypeException::class);
    }
});
