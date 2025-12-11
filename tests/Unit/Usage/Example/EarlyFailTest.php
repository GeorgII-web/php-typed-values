<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Usage\Example\EarlyFail;

it('constructs EarlyFail from scalars and exposes typed values', function (): void {
    $vo = EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: 170);

    expect($vo->getId()->toString())->toBe('1');
    expect($vo->getFirstName()->toString())->toBe('Foobar');
    expect($vo->getHeight()->toString())->toBe('170');
});

it('fails early when firstName is empty', function (): void {
    expect(fn() => EarlyFail::fromScalars(id: 1, firstName: '', height: 10.0))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('fails early when height is negative', function (): void {
    expect(fn() => EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: -10.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-10"');
});
