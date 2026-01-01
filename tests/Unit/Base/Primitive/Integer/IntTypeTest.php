<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerStandard;

it('__toString proxies to toString for IntType', function (): void {
    $v = new IntegerStandard(123);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('123');
});

it('fromInt returns exact value and toString matches', function (): void {
    $i1 = IntegerStandard::fromInt(-10);
    expect($i1->value())->toBe(-10)
        ->and($i1->toString())->toBe('-10');

    $i2 = IntegerStandard::fromInt(0);
    expect($i2->value())->toBe(0)
        ->and($i2->toString())->toBe('0');
});

it('fromString parses valid integer strings including negatives and leading zeros', function (): void {
    expect(IntegerStandard::fromString('-15')->value())->toBe(-15)
        ->and(IntegerStandard::fromString('0')->toString())->toBe('0')
        ->and(IntegerStandard::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-integer or non-canonical strings', function (string $input): void {
    expect(fn() => IntegerStandard::fromString($input))->toThrow(IntegerTypeException::class);
})->with(['5a', 'a5', '', 'abc', ' 5', '5 ', '+5', '05', '--5', '3.14']);
