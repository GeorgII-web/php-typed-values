<?php

declare(strict_types=1);

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\TypeException;

it('greaterThanEq throws with default message when empty', function (): void {
    expect(fn() => Assert::greaterThanEq(0, 1, ''))
        ->toThrow(TypeException::class, 'Expected a value greater than or equal to the minimum');
});

it('greaterThanEq throws with custom message when provided', function (): void {
    expect(fn() => Assert::greaterThanEq(0, 1, 'custom gt= message'))
        ->toThrow(TypeException::class, 'custom gt= message');
});

it('greaterThanEq does not throw when valid', function (): void {
    Assert::greaterThanEq(1, 1, '');
    Assert::greaterThanEq(2, 1, 'anything');
    expect(true)->toBeTrue();
});

it('lessThanEq throws with default message when empty', function (): void {
    expect(fn() => Assert::lessThanEq(2, 1, ''))
        ->toThrow(TypeException::class, 'Expected a value less than or equal to the maximum');
});

it('lessThanEq throws with custom message when provided', function (): void {
    expect(fn() => Assert::lessThanEq(3, 1, 'custom lt= message'))
        ->toThrow(TypeException::class, 'custom lt= message');
});

it('lessThanEq does not throw when valid', function (): void {
    Assert::lessThanEq(1, 1, '');
    Assert::lessThanEq(1, 2, 'anything');
    expect(true)->toBeTrue();
});

it('integerish throws with default message when empty', function (): void {
    expect(fn() => Assert::integerish('5.5', ''))
        ->toThrow(TypeException::class, 'Expected an "integerish" value');
});

it('integerish throws with custom message when provided', function (): void {
    expect(fn() => Assert::integerish('foo', 'custom integerish message'))
        ->toThrow(TypeException::class, 'custom integerish message');
});

it('integerish does not throw for integerish values', function (): void {
    Assert::integerish('5', '');
    Assert::integerish('5.0', '');
    Assert::integerish(5, '');
    expect(true)->toBeTrue();
});

it('nonEmptyString throws with default message when empty', function (): void {
    expect(fn() => Assert::nonEmptyString('', ''))
        ->toThrow(TypeException::class, 'Value must be a non-empty string');
});

it('nonEmptyString throws with custom message when provided', function (): void {
    expect(fn() => Assert::nonEmptyString('', 'custom non-empty message'))
        ->toThrow(TypeException::class, 'custom non-empty message');
});

it('nonEmptyString does not throw for non-empty strings', function (): void {
    Assert::nonEmptyString('a', '');
    Assert::nonEmptyString('  ', '');
    Assert::nonEmptyString("\u{1F600}", '');
    expect(true)->toBeTrue();
});
