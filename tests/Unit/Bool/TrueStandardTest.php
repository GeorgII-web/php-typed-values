<?php

declare(strict_types=1);

use PhpTypedValues\Bool\TrueStandard;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @coversNothing
 */
readonly class TrueStandardTest extends TrueStandard
{
    public function __toString(): string
    {
        return 'not-a-boolean';
    }
}

covers(TrueStandard::class);

describe('TrueStandard', function () {
    it('constructs only with true and exposes value/toString', function (): void {
        $t = new TrueStandard(true);
        expect($t->value())->toBeTrue()
            ->and($t->toString())->toBe('true')
            ->and((string) $t)->toBe('true');

        expect(fn() => new TrueStandard(false))
            ->toThrow(BoolTypeException::class, 'Expected "true" literal, got "false"');
    });

    it('jsonSerialize returns native true', function (): void {
        $t = new TrueStandard(true);
        expect($t->jsonSerialize())->toBeTrue();
    });

    it('fromString accepts true-like values only', function (): void {
        // Only 'true' string is accepted (case-insensitive)
        expect(TrueStandard::fromString('true')->value())->toBeTrue()
            ->and(TrueStandard::tryFromString('TRUE'))->toBeInstanceOf(Undefined::class);

        expect(fn() => TrueStandard::fromString('false'))
            ->toThrow(BoolTypeException::class, 'Expected "true" literal, got "false"');
        expect(fn() => TrueStandard::fromString(' YES '))
            ->toThrow(StringTypeException::class, 'String " YES " has no valid strict bool value');
    });

    it('fromInt accepts only 1', function (): void {
        expect(TrueStandard::fromInt(1)->value())->toBeTrue();

        expect(fn() => TrueStandard::fromInt(0))
            ->toThrow(BoolTypeException::class, 'Expected "true" literal, got "false"');
    });

    it('tryFromString/tryFromInt return Undefined for non-true inputs', function (): void {
        // Only 'true' string is accepted, not 'y'
        $ok = TrueStandard::tryFromString('true');
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
        // Check that tryFromString returns proper type
        $valid = TrueStandard::tryFromString('true');
        expect($valid)->toBeInstanceOf(TrueStandard::class)
            ->and($valid->jsonSerialize())->toBeTrue();
    });

    it('fromBool accepts only true and throws on false', function (): void {
        $t = TrueStandard::fromBool(true);
        expect($t->value())->toBeTrue();

        expect(fn() => TrueStandard::fromBool(false))
            ->toThrow(BoolTypeException::class, 'Expected "true" literal, got "false"');
    });

    it('__toString returns "true"', function (): void {
        $t = new TrueStandard(true);
        expect((string) $t)->toBe('true')
            ->and($t->__toString())->toBe('true');
    });

    it('tryFromMixed handles various inputs returning TrueStandard or Undefined', function (): void {
        // valid inputs for true - only 'true' string and 1 are accepted
        $fromString = TrueStandard::tryFromMixed('true');
        $fromInt = TrueStandard::tryFromMixed(1);

        // bool(true) should be accepted
        $fromBool = TrueStandard::tryFromMixed(true);

        // invalid inputs (should produce false or invalid)
        $fromArray = TrueStandard::tryFromMixed(['x']);
        $fromNull = TrueStandard::tryFromMixed(null);
        $fromObject = TrueStandard::tryFromMixed(new stdClass());
        $fromFalseString = TrueStandard::tryFromMixed('false');
        $fromFalseInt = TrueStandard::tryFromMixed(0);

        // stringable object with 'true'
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'true';
            }
        };
        $fromStringable = TrueStandard::tryFromMixed($stringable);

        expect($fromString)->toBeInstanceOf(TrueStandard::class)
            ->and($fromString->value())->toBeTrue()
            ->and($fromInt)->toBeInstanceOf(TrueStandard::class)
            ->and($fromInt->value())->toBeTrue()
            ->and($fromBool)->toBeInstanceOf(TrueStandard::class)
            ->and($fromBool->value())->toBeTrue()
            ->and(TrueStandard::tryFromMixed(new TrueStandard(true))->value())->toBeTrue()
            ->and($fromStringable)->toBeInstanceOf(TrueStandard::class)
            ->and($fromStringable->value())->toBeTrue()
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class)
            ->and($fromFalseString)->toBeInstanceOf(Undefined::class)
            ->and($fromFalseInt)->toBeInstanceOf(Undefined::class);
    });

    it('kills InstanceOfToFalse mutant in tryFromMixed for TrueStandard', function (): void {
        $subclass = new TrueStandardTest(true);

        // If mutant replaces ($value instanceof self) with false,
        // it will fall through to Stringable check and call fromString('not-a-boolean'),
        // which throws IntegerTypeException, and tryFromMixed will catch it and return Undefined.
        // If it correctly uses ($value instanceof self), it will call fromBool(true) and succeed.
        $result = TrueStandard::tryFromMixed($subclass);
        expect($result)->toBeInstanceOf(TrueStandard::class)
            ->and($result->value())->toBeTrue();
    });

    it('tryFromMixed handles floats 0.0 and 1.0', function (): void {
        $one = TrueStandard::tryFromMixed(1.0);
        $zero = TrueStandard::tryFromMixed(0.0);
        $minusOne = TrueStandard::tryFromMixed(-1.0);
        $two = TrueStandard::tryFromMixed(2.0);
        $pointFive = TrueStandard::tryFromMixed(0.5);

        expect($one)->toBeInstanceOf(TrueStandard::class)
            ->and($one->value())->toBeTrue()
            ->and($zero)->toBeInstanceOf(Undefined::class)
            ->and($minusOne)->toBeInstanceOf(Undefined::class)
            ->and($two)->toBeInstanceOf(Undefined::class)
            ->and($pointFive)->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for TrueStandard', function (): void {
        // Note: can't use 'yes' string, only 'true'
        expect(TrueStandard::fromString('true')->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for TrueStandard', function (): void {
        // Note: can't use 'yes' string, only 'true'
        expect(TrueStandard::fromString('true')->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $t = new TrueStandard(true);
        expect($t->isTypeOf(TrueStandard::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $t = new TrueStandard(true);
        expect($t->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $t = new TrueStandard(true);
        expect($t->isTypeOf('NonExistentClass', TrueStandard::class, 'AnotherClass'))->toBeTrue();
    });

    it('toBool returns true', function (): void {
        expect(TrueStandard::fromBool(true)->toBool())->toBeTrue();
    });

    it('toInt returns 1', function (): void {
        expect(TrueStandard::fromBool(true)->toInt())->toBe(1);
    });

    it('toFloat returns 1.0', function (): void {
        expect(TrueStandard::fromBool(true)->toFloat())->toBe(1.0);
    });

    it('tryFromBool handles boolean values', function (): void {
        $trueResult = TrueStandard::tryFromBool(true);
        $falseResult = TrueStandard::tryFromBool(false);

        expect($trueResult)->toBeInstanceOf(TrueStandard::class)
            ->and($trueResult->value())->toBeTrue()
            ->and($falseResult)->toBeInstanceOf(Undefined::class);
    });

    it('tryFromFloat handles float values', function (): void {
        $validTrue = TrueStandard::tryFromFloat(1.0);
        $invalid = TrueStandard::tryFromFloat(0.0);

        expect($validTrue)->toBeInstanceOf(TrueStandard::class)
            ->and($validTrue->value())->toBeTrue()
            ->and($invalid)->toBeInstanceOf(Undefined::class);
    });

    it('tryFromInt handles integer values', function (): void {
        $validTrue = TrueStandard::tryFromInt(1);
        $invalid = TrueStandard::tryFromInt(0);

        expect($validTrue)->toBeInstanceOf(TrueStandard::class)
            ->and($validTrue->value())->toBeTrue()
            ->and($invalid)->toBeInstanceOf(Undefined::class);
    });

    it('tryFromString handles string values', function (): void {
        $validTrue = TrueStandard::tryFromString('true');
        $invalid = TrueStandard::tryFromString('false');

        expect($validTrue)->toBeInstanceOf(TrueStandard::class)
            ->and($validTrue->value())->toBeTrue()
            ->and($invalid)->toBeInstanceOf(Undefined::class);
    });
});
