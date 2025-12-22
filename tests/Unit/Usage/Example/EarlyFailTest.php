<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Usage\Example\EarlyFail;

it('constructs EarlyFail from scalars and exposes typed values', function (): void {
    $vo = EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);

    expect($vo->getId()->value())->toBe(1);
    expect($vo->getFirstName()->value())->toBe('Foobar');
    expect($vo->getHeight()->value())->toBe(170.5);
});

it('fails early when id is zero or negative', function (): void {
    expect(fn() => EarlyFail::fromScalars(id: 0, firstName: 'Foobar', height: 10.0))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('fails early when firstName is empty', function (): void {
    expect(fn() => EarlyFail::fromScalars(id: 1, firstName: '', height: 10.0))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('fails early when height is negative', function (): void {
    expect(fn() => EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: -10.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-10"');
});

it('returns false for isEmpty and isUndefined', function (): void {
    $vo = EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    expect($vo->isEmpty())->toBeFalse()
        ->and($vo->isUndefined())->toBeFalse();
});

it('converts to array', function (): void {
    $vo = EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    expect($vo->toArray())->toBe([
        'id' => 1,
        'firstName' => 'Foobar',
        'height' => 170.5,
    ]);
});

it('can call fromArray', function (): void {
    $data = [
        'id' => 1,
        'firstName' => 'Foobar',
        'height' => 170.5,
    ];
    $vo = EarlyFail::fromArray($data);
    expect($vo->getId()->value())->toBe(1)
        ->and($vo->getFirstName()->value())->toBe('Foobar')
        ->and($vo->getHeight()->value())->toBe(170.5);
});

it('fails in fromArray when id is missing (defaults to 0)', function (): void {
    expect(fn() => EarlyFail::fromArray(['firstName' => 'A', 'height' => 1.0]))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('fails in fromArray when firstName is missing (defaults to empty)', function (): void {
    expect(fn() => EarlyFail::fromArray(['id' => 1, 'height' => 1.0]))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('fails in fromArray when height is missing (defaults to 0.0)', function (): void {
    expect(fn() => EarlyFail::fromArray(['id' => 1, 'firstName' => 'A']))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0"');
});

it('serializes to JSON correctly', function (): void {
    $vo = EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    expect(json_encode($vo))->toBe('{"id":1,"firstName":"Foobar","height":170.5}');
});
