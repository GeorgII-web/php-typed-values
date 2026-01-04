<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\UndefinedStandard;

it('creates UndefinedStandard via factory', function (): void {
    $u = UndefinedStandard::create();
    expect($u)->toBeInstanceOf(UndefinedStandard::class);
});

it('creates UndefinedStandard via fromString factory', function (): void {
    $u = UndefinedStandard::fromString('anything');
    expect($u)->toBeInstanceOf(UndefinedStandard::class);
});

it('throws on toString for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on toInt for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toInt())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to integer.');
});

it('throws on toFloat for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toFloat())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to float.');
});

it('throws on toArray for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toArray())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to array.');
});

it('throws on value for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});

it('throws on __toString for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    // Call magic directly to avoid implicit casting behavior
    expect(fn() => $u->__toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on jsonSerialize for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->jsonSerialize())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be serialized for Json.');
});

it('tryFromMixed always returns itself', function (): void {
    $fromString = UndefinedStandard::tryFromMixed('hello');
    $fromInt = UndefinedStandard::tryFromMixed(123);
    $fromArray = UndefinedStandard::tryFromMixed([]);
    $fromNull = UndefinedStandard::tryFromMixed(null);

    expect($fromString)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromInt)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromArray)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromNull)
        ->toBeInstanceOf(UndefinedStandard::class);
});

it('cheks tryFrom"Any" methods', function (): void {
    $fromString = UndefinedStandard::tryFromString('hello');
    $fromInt = UndefinedStandard::tryFromInt(123);
    $fromArray = UndefinedStandard::tryFromArray([]);
    $fromFloat = UndefinedStandard::tryFromFloat(1.1);
    $fromBool = UndefinedStandard::tryFromBool(true);

    expect($fromString)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromInt)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromArray)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromBool)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and($fromFloat)
        ->toBeInstanceOf(UndefinedStandard::class)
        ->and(fn() => $fromBool->toBool())->toThrow(UndefinedTypeException::class);
});

it('tryFromString always returns Undefined', function (): void {
    $v = UndefinedStandard::tryFromString('anything');

    expect($v)->toBeInstanceOf(UndefinedStandard::class);
});

it('isEmpty returns true for UndefinedStandard', function (): void {
    $u1 = UndefinedStandard::create();
    $u2 = UndefinedStandard::fromString('ignored');

    expect($u1->isEmpty())->toBeTrue()
        ->and($u2->isEmpty())->toBeTrue();
});

it('isUndefined returns true for UndefinedStandard', function (): void {
    $u1 = UndefinedStandard::create();
    $u2 = UndefinedStandard::fromString('ignored');

    expect($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = UndefinedStandard::create();
    expect($v->isTypeOf(UndefinedStandard::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = UndefinedStandard::create();
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = UndefinedStandard::create();
    expect($v->isTypeOf('NonExistentClass', UndefinedStandard::class, 'AnotherClass'))->toBeTrue();
});
