<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayOfObjects;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Usage\Example\WithArrays;

it('builds from valid scalars and serializes to expected array', function (): void {
    $obj = WithArrays::fromScalars(
        id: 1,
        firstName: 'Alice',
        height: 170.0,
        nickNames: ['User1', 'Admin5'],
    );

    expect($obj->getId()->toString())->toBe('1')
        ->and($obj->getFirstName()->toString())->toBe('Alice')
        ->and($obj->getHeight()->toString())->toBe('170.0');

    $nick = $obj->getNickNames();
    expect($nick)->toBeInstanceOf(ArrayOfObjects::class)
        ->and($nick->toArray())->toBe(['User1', 'Admin5']);

    expect($obj->jsonSerialize())->toBe([
        'id' => '1',
        'firstName' => 'Alice',
        'height' => '170.0',
        'nickNames' => ['User1', 'Admin5'],
    ]);
});

it('handles Undefined for empty firstName and null height (late fail on access)', function (): void {
    $obj = WithArrays::fromScalars(id: 1, firstName: '', height: null);

    expect(fn() => $obj->getFirstName()->toString())
        ->toThrow(UndefinedTypeException::class);

    expect(fn() => $obj->getHeight()->toString())
        ->toThrow(UndefinedTypeException::class);

    // jsonSerialize also fails due to Undefineds
    expect(fn() => $obj->jsonSerialize())
        ->toThrow(UndefinedTypeException::class);
});

it('throws on invalid id (non-positive)', function (): void {
    expect(fn() => WithArrays::fromScalars(id: 0, firstName: 'X', height: 10.0))
        ->toThrow(IntegerTypeException::class);
});

it('throws on invalid height when provided (non-positive)', function (): void {
    expect(fn() => WithArrays::fromScalars(id: 1, firstName: 'X', height: -1.0)->getHeight()->value())
        ->toThrow(UndefinedTypeException::class);
});

it('transforms nickNames to ArrayOfObjects of non-empty strings', function (): void {
    $obj = WithArrays::fromScalars(id: 1, firstName: 'Bob', height: 10.0, nickNames: ['n1', 'n2']);
    $nn = $obj->getNickNames();

    expect($nn)->toBeInstanceOf(ArrayOfObjects::class)
        ->and($nn->toArray())->toBe(['n1', 'n2'])
        ->and($nn->isEmpty())->toBeFalse()
        ->and($nn->hasUndefined())->toBeFalse()
        ->and($nn->isUndefined())->toBeFalse();
});
