<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Usage\Example\LateFail;

it('constructs LateFail from scalars/mixed and exposes typed values', function (): void {
    $vo = LateFail::fromScalars(id: 1, firstName: 'Foobar', height: 170);

    expect($vo->getId()->toString())->toBe('1');
    expect($vo->getFirstName()->toString())->toBe('Foobar');
    expect($vo->getHeight()->toString())->toBe('170.0');
});

it('coerces mixed valid values via tryFromMixed', function (): void {
    $vo = LateFail::fromScalars(id: 2, firstName: 123, height: '170.5');

    expect($vo->getId()->toString())->toBe('2');
    expect($vo->getFirstName()->toString())->toBe('123');
    expect($vo->getHeight()->toString())->toBe('170.5');
});

it('keeps Undefined for invalid optional firstName (late fail)', function (): void {
    $vo = LateFail::fromScalars(id: 1, firstName: '', height: 10);

    expect($vo->getFirstName())->toBeInstanceOf(Undefined::class);
    // height remains valid
    expect($vo->getHeight()->toString())->toBe('10.0');
});

it('keeps Undefined for invalid optional height values (late fail)', function (): void {
    $vo1 = LateFail::fromScalars(id: 1, firstName: 'Foo', height: -10);
    expect($vo1->getHeight())->toBeInstanceOf(Undefined::class);

    $vo2 = LateFail::fromScalars(id: 1, firstName: 'Foo', height: null);
    expect($vo2->getHeight())->toBeInstanceOf(Undefined::class);

    $vo3 = LateFail::fromScalars(id: 1, firstName: 'Foo', height: 'abc');
    expect($vo3->getHeight())->toBeInstanceOf(Undefined::class);
});

it('fails early on invalid id', function (): void {
    expect(fn() => LateFail::fromScalars(id: 0, firstName: 'Foo', height: 10))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});
