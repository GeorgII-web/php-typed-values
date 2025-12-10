<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Usage\Example\NullableType;

it('constructs NullableType from scalars and exposes typed values', function (): void {
    $vo = NullableType::fromScalars(id: 1, firstName: 'Foobar', height: 170);

    expect($vo->getId()->toString())->toBe('1');
    expect($vo->getFirstName()->toString())->toBe('Foobar');
    expect($vo->getHeight()->toString())->toBe('170');
});

it('checks string cast', function (): void {
    $voInt = NullableType::fromScalars(id: 1, firstName: 'Test', height: 99);
    expect($voInt->getHeight()->value())->toBe(99.0);
});

it('treats empty firstName as Undefined (late-fail semantics)', function (): void {
    $vo = NullableType::fromScalars(id: 1, firstName: '', height: 10.0);
    expect($vo->getFirstName())->toBeInstanceOf(Undefined::class);
});

it('fails early when height is negative', function (): void {
    expect(fn() => NullableType::fromScalars(id: 1, firstName: 'Foobar', height: -10.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-10"');
});

it('accepts int/float/numeric-string heights and preserves string formatting via fromString casting', function (): void {
    $asInt = NullableType::fromScalars(id: 1, firstName: 'Foobar', height: 170);
    $asFloat = NullableType::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    $asString = NullableType::fromScalars(id: 1, firstName: 'Foobar', height: '42.25');

    expect($asInt->getHeight())->toBeInstanceOf(FloatPositive::class)
        ->and($asInt->getHeight()->toString())->toBe('170')
        ->and($asFloat->getHeight())->toBeInstanceOf(FloatPositive::class)
        ->and($asFloat->getHeight()->toString())->toBe('170.5')
        ->and($asString->getHeight())->toBeInstanceOf(FloatPositive::class)
        ->and($asString->getHeight()->toString())->toBe('42.25');
});

it('treats null height as Undefined (late-fail semantics)', function (): void {
    $obj = NullableType::fromScalars(id: 1, firstName: 'Foobar', height: null);
    expect($obj->getHeight())->toBeInstanceOf(Undefined::class);
});

it('null firstName produces Undefined via tryFromMixed while height succeeds', function (): void {
    $obj = NullableType::fromScalars(id: 1, firstName: null, height: 180);
    expect($obj->getFirstName())->toBeInstanceOf(Undefined::class)
        ->and($obj->getHeight())->toBeInstanceOf(FloatPositive::class);
});

it('invalid id throws IntegerTypeException with exact message', function (): void {
    expect(fn() => NullableType::fromScalars(id: 0, firstName: 'Name', height: 100))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('non-numeric height string throws FloatTypeException from assertFloatString', function (): void {
    expect(fn() => NullableType::fromScalars(id: 1, firstName: 'Name', height: 'abc'))
        ->toThrow(FloatTypeException::class, 'String "abc" has no valid float value');
});
