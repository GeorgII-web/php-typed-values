<?php

declare(strict_types=1);

use GeorgiiWeb\PhpTypedValues\Types\Integer\IntType;
use GeorgiiWeb\PhpTypedValues\Types\Integer\NonNegativeInt;
use GeorgiiWeb\PhpTypedValues\Types\Integer\Nullable\PositiveIntOrNull;
use GeorgiiWeb\PhpTypedValues\Types\Integer\PositiveInt;

it('creates IntType from int and string', function (): void {
    $i1 = new IntType(5);
    expect($i1->getValue())->toBe(5);
    $i2 = IntType::fromString('42');
    expect($i2->getValue())->toBe(42);
});

it('validates PositiveInt', function (): void {
    $p = new PositiveInt(7);
    expect($p->toString())->toBe('7');
    expect(fn() => new PositiveInt(0))->toThrow(InvalidArgumentException::class);
});

it('validates NonNegativeInt', function (): void {
    $n = new NonNegativeInt(0);
    expect($n->getValue())->toBe(0);
    expect(fn() => new NonNegativeInt(-1))->toThrow(InvalidArgumentException::class);
});

it('handles PositiveIntOrNull', function (): void {
    $n = new PositiveIntOrNull(null);
    expect($n->getValue())->toBeNull();
    $n2 = PositiveIntOrNull::fromString('null');
    expect($n2->getValue())->toBeNull();
    $n3 = PositiveIntOrNull::fromString('15');
    expect($n3->getValue())->toBe(15);
});
