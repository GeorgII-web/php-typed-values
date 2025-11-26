<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\IntegerTypeException;
use PhpTypedValues\Integer\Alias\Id;

it('creates Id', function (): void {
    expect(Id::fromInt(1)->value())->toBe(1);
});

it('fails on 0', function (): void {
    expect(fn() => Id::fromInt(0))->toThrow(IntegerTypeException::class);
});

it('fails on negatives', function (): void {
    expect(fn() => Id::fromInt(-1))->toThrow(IntegerTypeException::class);
});

it('creates Id from string', function (): void {
    expect(Id::fromString('1')->value())->toBe(1);
});

it('fails Id from integerish string', function (): void {
    expect(fn() => Id::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating Id from string 0', function (): void {
    expect(fn() => Id::fromString('0'))->toThrow(IntegerTypeException::class);
});

it('fails creating Id from negative string', function (): void {
    expect(fn() => Id::fromString('-3'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for Id', function (): void {
    expect((new Id(3))->toString())->toBe('3');
});

it('fails creating Id from float string', function (): void {
    expect(fn() => Id::fromString('5.5'))->toThrow(IntegerTypeException::class);
});
