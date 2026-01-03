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

it('fromBool accepts only false and throws on true', function (): void {
    $f = FalseStandard::fromBool(false);
    expect($f->value())->toBeFalse();

    expect(fn() => FalseStandard::fromBool(true))
        ->toThrow(BoolTypeException::class, 'Expected false literal, got "true"');
});

it('__toString returns "false"', function (): void {
    $f = new FalseStandard(false);
    expect((string) $f)->toBe('false')
        ->and($f->__toString())->toBe('false');
});

it('tryFromMixed handles various inputs returning FalseStandard or Undefined', function (): void {
    // valid inputs
    $fromString = FalseStandard::tryFromMixed('no');
    $fromInt = FalseStandard::tryFromMixed(0);
    $fromBool = FalseStandard::tryFromMixed(false);

    // invalid inputs
    $fromArray = FalseStandard::tryFromMixed(['x']);
    $fromNull = FalseStandard::tryFromMixed(null);
    $fromObject = FalseStandard::tryFromMixed(new stdClass());

    // stringable object
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'off';
        }
    };
    $fromStringable = FalseStandard::tryFromMixed($stringable);

    expect($fromString)->toBeInstanceOf(FalseStandard::class)
        ->and($fromString->value())->toBeFalse()
        ->and($fromInt)->toBeInstanceOf(FalseStandard::class)
        ->and($fromInt->value())->toBeFalse()
        // bool(false) is converted to empty string by convertMixedToString and is not accepted -> Undefined
        ->and($fromBool)->toBeInstanceOf(FalseStandard::class)
        ->and($fromStringable)->toBeInstanceOf(FalseStandard::class)
        ->and($fromStringable->value())->toBeFalse()
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromObject)->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed handles floats 0.0 and 1.0', function (): void {
    $zero = FalseStandard::tryFromMixed(0.0);
    $one = FalseStandard::tryFromMixed(1.0);
    $minusOne = FalseStandard::tryFromMixed(-1.0);
    $two = FalseStandard::tryFromMixed(2.0);
    $pointFive = FalseStandard::tryFromMixed(0.5);

    expect($zero)->toBeInstanceOf(FalseStandard::class)
        ->and($zero->value())->toBeFalse()
        ->and($one)->toBeInstanceOf(Undefined::class)
        ->and($minusOne)->toBeInstanceOf(Undefined::class)
        ->and($two)->toBeInstanceOf(Undefined::class)
        ->and($pointFive)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for FalseStandard', function (): void {
    expect(FalseStandard::fromString('no')->isEmpty())->toBeFalse();
});

it('isUndefined is always false for FalseStandard', function (): void {
    expect(FalseStandard::fromString('no')->isUndefined())->toBeFalse();
});

it('isTypeOf returns true when class matches', function (): void {
    $f = new FalseStandard(false);
    expect($f->isTypeOf(FalseStandard::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $f = new FalseStandard(false);
    expect($f->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $f = new FalseStandard(false);
    expect($f->isTypeOf('NonExistentClass', FalseStandard::class, 'AnotherClass'))->toBeTrue();
});
