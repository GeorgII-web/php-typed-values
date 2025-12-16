<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

it('FloatStandard::tryFromString returns value on valid float string', function (): void {
    $v = FloatStandard::tryFromString('1.5');

    expect($v)
        ->toBeInstanceOf(FloatStandard::class)
        ->and($v->value())
        ->toBe(1.5)
        ->and($v->toString())
        ->toBe('1.5');
});

it('FloatStandard::tryFromString returns Undefined on invalid float string', function (): void {
    $v = FloatStandard::tryFromString('abc');

    expect($v)->toBeInstanceOf(Undefined::class);
});

it('FloatStandard::tryFromFloat returns value for any int', function (): void {
    $v = FloatStandard::tryFromFloat(2);

    expect($v)
        ->toBeInstanceOf(FloatStandard::class)
        ->and($v->value())
        ->toBe(2.0);
});

it('FloatStandard::fromString throws on non-numeric strings', function (): void {
    expect(fn() => FloatStandard::fromString('NaN'))
        ->toThrow(FloatTypeException::class, 'String "NaN" has no valid float value');
});

it('jsonSerialize returns float', function (): void {
    expect(FloatStandard::tryFromString('1.1')->jsonSerialize())->toBeFloat();
});

it('__toString mirrors toString and value', function (): void {
    $v = FloatStandard::fromFloat(3.14);

    expect((string) $v)
        ->toBe('3.14')
        ->and($v->toString())
        ->toBe('3.14')
        ->and($v->value())
        ->toBe(3.14);
});

it('tryFromMixed covers numeric, non-numeric, and stringable inputs', function (): void {
    // Numeric inputs
    $fromNumericString = FloatStandard::tryFromMixed('1.2');
    $fromInt = FloatStandard::tryFromMixed(3);
    $fromFloat = FloatStandard::tryFromMixed(2.5);

    // Non-numeric inputs
    $fromArray = FloatStandard::tryFromMixed([1]);
    $fromNull = FloatStandard::tryFromMixed(null);

    // Stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '1.23';
        }
    };
    $fromStringable = FloatStandard::tryFromMixed($stringable);

    expect($fromNumericString)->toBeInstanceOf(FloatStandard::class)
        ->and($fromNumericString->value())->toBe(1.2)
        ->and($fromInt)->toBeInstanceOf(FloatStandard::class)
        ->and($fromInt->value())->toBe(3.0)
        ->and($fromFloat)->toBeInstanceOf(FloatStandard::class)
        ->and($fromFloat->value())->toBe(2.5)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromStringable)->toBeInstanceOf(FloatStandard::class)
        ->and($fromStringable->value())->toBe(1.23);
});

it('isEmpty returns false for FloatStandard', function (): void {
    $a = new FloatStandard(-1.0);
    $b = FloatStandard::fromFloat(0.0);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Instances should be defined
    $v1 = new FloatStandard(-1.0);
    $v2 = FloatStandard::fromFloat(0.0);

    // Undefined results via tryFrom*
    $u1 = FloatStandard::tryFromString('not-a-number');
    $u2 = FloatStandard::tryFromMixed([1]);

    expect($v1->isUndefined())->toBeFalse()
        ->and($v2->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue();
});
