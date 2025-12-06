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

it('parses extended true/false string aliases', function (): void {
    // true-like
    expect(BoolStandard::fromString('1')->value())->toBeTrue()
        ->and(BoolStandard::fromString('yes')->value())->toBeTrue()
        ->and(BoolStandard::fromString('on')->value())->toBeTrue()
        ->and(BoolStandard::fromString('Y')->value())->toBeTrue()
        // false-like
        ->and(BoolStandard::fromString('0')->value())->toBeFalse()
        ->and(BoolStandard::fromString('no')->value())->toBeFalse()
        ->and(BoolStandard::fromString('off')->value())->toBeFalse()
        ->and(BoolStandard::fromString('N')->value())->toBeFalse();
});

it('throws on invalid string values', function (): void {
    expect(fn() => BoolStandard::fromString('yes1'))
        ->toThrow(BoolTypeException::class, 'Expected string "true" or "false", got "yes1"');
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

it('tryFromString returns Undefined on invalid input and BoolStandard on valid', function (): void {
    $ok = BoolStandard::tryFromString('true');
    $fail = BoolStandard::tryFromString('maybe');

    expect($ok)->toBeInstanceOf(BoolStandard::class)
        ->and($ok->value())->toBeTrue();

    // Undefined type instance indicates failure without throwing
    expect($fail::class)->toBe(PhpTypedValues\Undefined\UndefinedStandard::class);
});

it('tryFromInt returns Undefined on invalid input and BoolStandard on valid', function (): void {
    $one = BoolStandard::tryFromInt(1);
    $zero = BoolStandard::tryFromInt(0);
    $bad = BoolStandard::tryFromInt(3);

    expect($one)->toBeInstanceOf(BoolStandard::class)
        ->and($one->value())->toBeTrue()
        ->and($zero)->toBeInstanceOf(BoolStandard::class)
        ->and($zero->value())->toBeFalse()
        ->and($bad::class)->toBe(PhpTypedValues\Undefined\UndefinedStandard::class);
});

it('parses string values with surrounding whitespace', function (): void {
    expect(BoolStandard::fromString('  true  ')->value())->toBeTrue()
        ->and(BoolStandard::fromString("\tFALSE \n")->value())->toBeFalse()
        ->and(BoolStandard::fromString('  on ')->value())->toBeTrue()
        ->and(BoolStandard::fromString(' off  ')->value())->toBeFalse();
});
