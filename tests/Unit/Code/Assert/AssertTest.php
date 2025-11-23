<?php

declare(strict_types=1);

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Exception\StringTypeException;

it('greaterThanEq throws with default message when empty', function (): void {
    expect(fn() => Assert::greaterThanEq(0, 1, ''))
        ->toThrow(NumericTypeException::class, 'Expected a value greater than or equal to the minimum');
});

it('greaterThanEq throws with custom message when provided', function (): void {
    expect(fn() => Assert::greaterThanEq(0, 1, 'custom gt= message'))
        ->toThrow(NumericTypeException::class, 'custom gt= message');
});

it('greaterThanEq does not throw when valid', function (): void {
    Assert::greaterThanEq(1, 1, '');
    Assert::greaterThanEq(2, 1, 'anything');
    expect(true)->toBeTrue();
});

it('lessThanEq throws with default message when empty', function (): void {
    expect(fn() => Assert::lessThanEq(2, 1, ''))
        ->toThrow(NumericTypeException::class, 'Expected a value less than or equal to the maximum');
});

it('lessThanEq throws with custom message when provided', function (): void {
    expect(fn() => Assert::lessThanEq(3, 1, 'custom lt= message'))
        ->toThrow(NumericTypeException::class, 'custom lt= message');
});

it('lessThanEq does not throw when valid', function (): void {
    Assert::lessThanEq(1, 1, '');
    Assert::lessThanEq(1, 2, 'anything');
    expect(true)->toBeTrue();
});

it('integer throws with default message when empty', function (): void {
    expect(fn() => Assert::integer('5.5', ''))
        ->toThrow(NumericTypeException::class, 'Unexpected conversions possible, "5.5" !== "5"');
});

it('integerish throws with custom message when provided', function (): void {
    expect(fn() => Assert::integer('foo', 'custom integerish message'))
        ->toThrow(NumericTypeException::class, 'custom integerish message');
});

it('integer does not throw for integer values', function (): void {
    Assert::integer('5', '');
    Assert::integer('-5', '');
    Assert::integer(5, '');
    expect(true)->toBeTrue();
});

it('nonEmptyString throws with default message when empty', function (): void {
    expect(fn() => Assert::nonEmptyString('', ''))
        ->toThrow(StringTypeException::class, 'Value must be a non-empty string');
});

it('nonEmptyString throws with custom message when provided', function (): void {
    expect(fn() => Assert::nonEmptyString('', 'custom non-empty message'))
        ->toThrow(StringTypeException::class, 'custom non-empty message');
});

it('nonEmptyString does not throw for non-empty strings', function (): void {
    Assert::nonEmptyString('a', '');
    Assert::nonEmptyString('  ', '');
    Assert::nonEmptyString("\u{1F600}", '');
    expect(true)->toBeTrue();
});

it('numeric throws with default message when empty', function (): void {
    expect(fn() => Assert::numeric('abc', ''))
        ->toThrow(NumericTypeException::class, 'Expected a numeric value');
});

it('numeric throws with custom message when provided', function (): void {
    expect(fn() => Assert::numeric('foo', 'custom numeric message'))
        ->toThrow(NumericTypeException::class, 'custom numeric message');
});

it('numeric does not throw for numeric inputs', function (): void {
    Assert::numeric(5, '');
    Assert::numeric(3.14, '');
    Assert::numeric('2', '');
    Assert::numeric('2.5', '');
    Assert::numeric('1e-3', '');
    expect(true)->toBeTrue();
});
