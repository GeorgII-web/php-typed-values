<?php

declare(strict_types=1);

use PhpTypedValues\Bool\FalseStandard;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @coversNothing
 */
readonly class FalseStandardTest extends FalseStandard
{
    public function __toString(): string
    {
        return 'not-a-boolean';
    }
}

covers(FalseStandard::class);

it('constructs only with false and exposes value/toString', function (): void {
    $f = new FalseStandard(false);
    expect($f->value())->toBeFalse()
        ->and($f->toString())->toBe('false')
        ->and((string) $f)->toBe('false');

    expect(fn() => new FalseStandard(true))
        ->toThrow(BoolTypeException::class, 'Expected "false" literal, got "true"');
});

it('jsonSerialize returns native false', function (): void {
    $f = new FalseStandard(false);
    expect($f->jsonSerialize())->toBeFalse();
});

it('fromString accepts false-like values only', function (): void {
    // Only 'false' string is accepted (case-insensitive)
    expect(FalseStandard::fromString('false')->value())->toBeFalse()
        ->and(FalseStandard::tryFromString('FALSE'))->toBeInstanceOf(Undefined::class);

    expect(fn() => FalseStandard::fromString('true'))
        ->toThrow(BoolTypeException::class, 'Expected "false" literal, got "true"');
    expect(fn() => FalseStandard::fromString(' NO '))
        ->toThrow(IntegerTypeException::class, 'Integer " NO " has no valid strict bool value');
});

it('fromInt accepts only 0', function (): void {
    expect(FalseStandard::fromInt(0)->value())->toBeFalse();

    expect(fn() => FalseStandard::fromInt(1))
        ->toThrow(BoolTypeException::class, 'Expected "false" literal, got "true"');
});

it('tryFromString/tryFromInt return Undefined for non-false inputs', function (): void {
    // Only 'false' string is accepted, not 'n'
    $ok = FalseStandard::tryFromString('false');
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
    // Check that tryFromString returns proper type
    $valid = FalseStandard::tryFromString('false');
    expect($valid)->toBeInstanceOf(FalseStandard::class)
        ->and($valid->jsonSerialize())->toBeFalse();
});

it('fromBool accepts only false and throws on true', function (): void {
    $f = FalseStandard::fromBool(false);
    expect($f->value())->toBeFalse();

    expect(fn() => FalseStandard::fromBool(true))
        ->toThrow(BoolTypeException::class, 'Expected "false" literal, got "true"');
});

it('__toString returns "false"', function (): void {
    $f = new FalseStandard(false);
    expect((string) $f)->toBe('false')
        ->and($f->__toString())->toBe('false');
});

it('tryFromMixed handles various inputs returning FalseStandard or Undefined', function (): void {
    // valid inputs for false - only 'false' string and 0 are accepted
    $fromString = FalseStandard::tryFromMixed('false');
    $fromInt = FalseStandard::tryFromMixed(0);

    // bool(false) should be accepted
    $fromBool = FalseStandard::tryFromMixed(false);

    // invalid inputs (should produce true or invalid)
    $fromArray = FalseStandard::tryFromMixed(['x']);
    $fromNull = FalseStandard::tryFromMixed(null);
    $fromObject = FalseStandard::tryFromMixed(new stdClass());
    $fromTrueString = FalseStandard::tryFromMixed('true');
    $fromTrueInt = FalseStandard::tryFromMixed(1);

    // stringable object with 'false'
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'false';
        }
    };
    $fromStringable = FalseStandard::tryFromMixed($stringable);

    expect($fromString)->toBeInstanceOf(FalseStandard::class)
        ->and($fromString->value())->toBeFalse()
        ->and($fromInt)->toBeInstanceOf(FalseStandard::class)
        ->and($fromInt->value())->toBeFalse()
        ->and($fromBool)->toBeInstanceOf(FalseStandard::class)
        ->and($fromBool->value())->toBeFalse()
        ->and(FalseStandard::tryFromMixed(new FalseStandard(false))->value())->toBeFalse()
        ->and($fromStringable)->toBeInstanceOf(FalseStandard::class)
        ->and($fromStringable->value())->toBeFalse()
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromObject)->toBeInstanceOf(Undefined::class)
        ->and($fromTrueString)->toBeInstanceOf(Undefined::class)
        ->and($fromTrueInt)->toBeInstanceOf(Undefined::class);
});

it('kills InstanceOfToFalse mutant in tryFromMixed for FalseStandard', function (): void {
    $subclass = new FalseStandardTest(false);

    // If mutant replaces ($value instanceof self) with false,
    // it will fall through to Stringable check and call fromString('not-a-boolean'),
    // which throws IntegerTypeException, and tryFromMixed will catch it and return Undefined.
    // If it correctly uses ($value instanceof self), it will call fromBool(false) and succeed.
    $result = FalseStandard::tryFromMixed($subclass);
    expect($result)->toBeInstanceOf(FalseStandard::class)
        ->and($result->value())->toBeFalse();
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
    // Note: can't use 'no' string, only 'false'
    expect(FalseStandard::fromString('false')->isEmpty())->toBeFalse();
});

it('isUndefined is always false for FalseStandard', function (): void {
    // Note: can't use 'no' string, only 'false'
    expect(FalseStandard::fromString('false')->isUndefined())->toBeFalse();
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

it('toBool returns false', function (): void {
    expect(FalseStandard::fromBool(false)->toBool())->toBeFalse();
});

it('toInt returns 0', function (): void {
    expect(FalseStandard::fromBool(false)->toInt())->toBe(0);
});

it('toFloat returns 0.0', function (): void {
    expect(FalseStandard::fromBool(false)->toFloat())->toBe(0.0);
});

it('tryFromBool handles boolean values', function (): void {
    $falseResult = FalseStandard::tryFromBool(false);
    $trueResult = FalseStandard::tryFromBool(true);

    expect($falseResult)->toBeInstanceOf(FalseStandard::class)
        ->and($falseResult->value())->toBeFalse()
        ->and($trueResult)->toBeInstanceOf(Undefined::class);
});

it('tryFromFloat handles float values', function (): void {
    $validFalse = FalseStandard::tryFromFloat(0.0);
    $invalid = FalseStandard::tryFromFloat(1.0);

    expect($validFalse)->toBeInstanceOf(FalseStandard::class)
        ->and($validFalse->value())->toBeFalse()
        ->and($invalid)->toBeInstanceOf(Undefined::class);
});

it('tryFromInt handles integer values', function (): void {
    $validFalse = FalseStandard::tryFromInt(0);
    $invalid = FalseStandard::tryFromInt(1);

    expect($validFalse)->toBeInstanceOf(FalseStandard::class)
        ->and($validFalse->value())->toBeFalse()
        ->and($invalid)->toBeInstanceOf(Undefined::class);
});

it('tryFromString handles string values', function (): void {
    $validFalse = FalseStandard::tryFromString('false');
    $invalid = FalseStandard::tryFromString('true');

    expect($validFalse)->toBeInstanceOf(FalseStandard::class)
        ->and($validFalse->value())->toBeFalse()
        ->and($invalid)->toBeInstanceOf(Undefined::class);
});
