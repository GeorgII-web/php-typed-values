<?php

declare(strict_types=1);

use PhpTypedValues\Bool\FalseStandard;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

it('constructs only with false and exposes value/toString', function (): void {
    $f = new FalseStandard(false);
    expect($f->value())->toBeFalse()
        ->and($f->toString())->toBe('false')
        ->and((string) $f)->toBe('false');

    expect(fn() => new FalseStandard(true))
        ->toThrow(BoolTypeException::class, 'Expected false literal, got "true"');
});

it('jsonSerialize returns native false', function (): void {
    $f = new FalseStandard(false);
    expect($f->jsonSerialize())->toBeFalse();
});

it('fromString accepts false-like values only', function (): void {
    expect(FalseStandard::fromString('false')->value())->toBeFalse()
        ->and(FalseStandard::fromString(' NO ')->value())->toBeFalse()
        ->and(FalseStandard::fromString('off')->value())->toBeFalse()
        ->and(FalseStandard::fromString('0')->value())->toBeFalse();

    expect(fn() => FalseStandard::fromString('true'))
        ->toThrow(BoolTypeException::class, 'Expected string representing false, got "true"');
});

it('fromInt accepts only 0', function (): void {
    expect(FalseStandard::fromInt(0)->value())->toBeFalse();

    expect(fn() => FalseStandard::fromInt(1))
        ->toThrow(BoolTypeException::class, 'Expected int "0" for false, got "1"');
});

it('tryFromString/tryFromInt return Undefined for non-false inputs', function (): void {
    $ok = FalseStandard::tryFromString('n');
    $badStr = FalseStandard::tryFromString('yes');
    $okI = FalseStandard::tryFromInt(0);
    $badI = FalseStandard::tryFromInt(2);

    expect($ok)->toBeInstanceOf(FalseStandard::class)
        ->and($ok->value())->toBeFalse()
        ->and($okI)->toBeInstanceOf(FalseStandard::class)
        ->and($okI->value())->toBeFalse()
        ->and($badStr)->toBeInstanceOf(Undefined::class)
        ->and($badI)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns bool', function (): void {
    expect(FalseStandard::tryFromString('0')->jsonSerialize())->toBeBool();
});
