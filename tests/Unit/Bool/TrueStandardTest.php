<?php

declare(strict_types=1);

use PhpTypedValues\Bool\TrueStandard;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

it('constructs only with true and exposes value/toString', function (): void {
    $t = new TrueStandard(true);
    expect($t->value())->toBeTrue()
        ->and($t->toString())->toBe('true')
        ->and((string) $t)->toBe('true');

    expect(fn() => new TrueStandard(false))
        ->toThrow(BoolTypeException::class, 'Expected true literal, got "false"');
});

it('jsonSerialize returns native true', function (): void {
    $t = new TrueStandard(true);
    expect($t->jsonSerialize())->toBeTrue();
});

it('fromString accepts true-like values only', function (): void {
    expect(TrueStandard::fromString('true')->value())->toBeTrue()
        ->and(TrueStandard::fromString(' YES ')->value())->toBeTrue()
        ->and(TrueStandard::fromString('on')->value())->toBeTrue()
        ->and(TrueStandard::fromString('1')->value())->toBeTrue();

    expect(fn() => TrueStandard::fromString('false'))
        ->toThrow(BoolTypeException::class, 'Expected string representing true, got "false"');
});

it('fromInt accepts only 1', function (): void {
    expect(TrueStandard::fromInt(1)->value())->toBeTrue();

    expect(fn() => TrueStandard::fromInt(0))
        ->toThrow(BoolTypeException::class, 'Expected int "1" for true, got "0"');
});

it('tryFromString/tryFromInt return Undefined for non-true inputs', function (): void {
    $ok = TrueStandard::tryFromString('y');
    $badStr = TrueStandard::tryFromString('no');
    $okI = TrueStandard::tryFromInt(1);
    $badI = TrueStandard::tryFromInt(0);

    expect($ok)->toBeInstanceOf(TrueStandard::class)
        ->and($ok->value())->toBeTrue()
        ->and($okI)->toBeInstanceOf(TrueStandard::class)
        ->and($okI->value())->toBeTrue()
        ->and($badStr)->toBeInstanceOf(Undefined::class)
        ->and($badI)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns bool', function (): void {
    expect(TrueStandard::tryFromString('1')->jsonSerialize())->toBeBool();
});

it('fromBool accepts only true and throws on false', function (): void {
    $t = TrueStandard::fromBool(true);
    expect($t->value())->toBeTrue();

    expect(fn() => TrueStandard::fromBool(false))
        ->toThrow(BoolTypeException::class, 'Expected true literal, got "false"');
});

it('__toString returns "true"', function (): void {
    $t = new TrueStandard(true);
    expect((string) $t)->toBe('true')
        ->and($t->__toString())->toBe('true');
});
