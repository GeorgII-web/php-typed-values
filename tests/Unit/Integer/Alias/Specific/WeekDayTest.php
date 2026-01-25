<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\Alias\Positive;

describe('Positive', function () {
    it('creates PositiveInt', function (): void {
        expect(Positive::fromInt(1)->value())->toBe(1);
    });

    it('fails on 0', function (): void {
        expect(fn() => Positive::fromInt(0))->toThrow(IntegerTypeException::class);
    });

    it('fails on negatives', function (): void {
        expect(fn() => Positive::fromInt(-1))->toThrow(IntegerTypeException::class);
    });

    it('creates PositiveInt from string', function (): void {
        expect(Positive::fromString('1')->value())->toBe(1);
    });

    it('fails PositiveInt from integerish string', function (): void {
        expect(fn() => Positive::fromString('5.0'))->toThrow(StringTypeException::class);
    });

    it('fails creating PositiveInt from string 0', function (): void {
        expect(fn() => Positive::fromString('0'))->toThrow(IntegerTypeException::class);
    });

    it('fails creating PositiveInt from negative string', function (): void {
        expect(fn() => Positive::fromString('-3'))->toThrow(IntegerTypeException::class);
    });

    it('toString returns scalar string for PositiveInt', function (): void {
        expect((new Positive(3))->toString())->toBe('3');
    });

    it('fails creating PositiveInt from float string', function (): void {
        expect(fn() => Positive::fromString('5.5'))->toThrow(StringTypeException::class);
    });
});
