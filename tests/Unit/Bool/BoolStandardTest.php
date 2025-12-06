<?php

declare(strict_types=1);

use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\Exception\BoolTypeException;

it('constructs with boolean and exposes value and toString', function (): void {
    $t = new BoolStandard(true);
    $f = new BoolStandard(false);

    expect($t->value())->toBeTrue()
        ->and($t->toString())->toBe('true')
        ->and($f->value())->toBeFalse()
        ->and($f->toString())->toBe('false');
});

it('creates fromBool correctly', function (): void {
    expect(BoolStandard::fromBool(true)->value())->toBeTrue()
        ->and(BoolStandard::fromBool(false)->toString())->toBe('false');
});

it('parses valid string values case-insensitively', function (): void {
    expect(BoolStandard::fromString('true')->value())->toBeTrue()
        ->and(BoolStandard::fromString('FALSE')->value())->toBeFalse()
        ->and(BoolStandard::fromString('TrUe')->toString())->toBe('true');
});

it('throws on invalid string values', function (): void {
    expect(fn() => BoolStandard::fromString('yes'))
        ->toThrow(BoolTypeException::class, 'Expected string "true"\"1" or "false"\"0", got "yes"');
});

it('parses valid integer values 1/0', function (): void {
    expect(BoolStandard::fromInt(1)->value())->toBeTrue()
        ->and(BoolStandard::fromInt(0)->toString())->toBe('false');
});

it('throws on invalid integer values', function (): void {
    expect(fn() => BoolStandard::fromInt(2))
        ->toThrow(BoolTypeException::class, 'Expected int "1" or "0", got "2"');
    expect(fn() => BoolStandard::fromInt(-1))
        ->toThrow(BoolTypeException::class, 'Expected int "1" or "0", got "-1"');
});
