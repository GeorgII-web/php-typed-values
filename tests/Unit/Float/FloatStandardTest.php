<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

it('FloatStandard::tryFromString returns value on valid float string', function (): void {
    $v = FloatStandard::tryFromString('1.5');

    expect($v)
        ->toBeInstanceOf(FloatStandard::class)
        ->and($v->value())
        ->toBe(1.5)
        ->and($v->toString())
        ->toBe('1.5');
});

it('FloatStandard::tryFromString returns Undefined on invalid float string', function (): void {
    $v = FloatStandard::tryFromString('abc');

    expect($v)->toBeInstanceOf(Undefined::class)
        ->and(FloatStandard::tryFromString('abc', Undefined::create()))->toBeInstanceOf(Undefined::class);
});

it('FloatStandard::tryFromFloat returns value for any int', function (): void {
    $v = FloatStandard::tryFromFloat(2);

    expect($v)
        ->toBeInstanceOf(FloatStandard::class)
        ->and($v->value())
        ->toBe(2.0);
});

it('FloatStandard::fromString throws on non-numeric strings', function (): void {
    expect(fn() => FloatStandard::fromString('NaN'))
        ->toThrow(FloatTypeException::class, 'String "NaN" has no valid float value');
});

it('FloatStandard::fromString throws exception for a long tail', function (): void {
    expect(fn() => FloatStandard::fromString('12.444144424443444044454446444744484449444'))
        ->toThrow(FloatTypeException::class, 'String "12.444144424443444044454446444744484449444" has no valid strict float value');
});

it('jsonSerialize returns float', function (): void {
    expect(FloatStandard::tryFromString('1.1')->jsonSerialize())->toBeFloat();
});

it('__toString mirrors toString and value', function (): void {
    $v = FloatStandard::fromFloat(3.14);

    expect((string) $v)
        ->toBe('3.14')
        ->and($v->toString())
        ->toBe('3.14')
        ->and($v->value())
        ->toBe(3.14);
});

it('tryFromMixed covers numeric, non-numeric, and stringable inputs', function (): void {
    // Numeric inputs
    $fromNumericString = FloatStandard::tryFromMixed('1.2');
    $fromInt = FloatStandard::tryFromMixed(3);
    $fromFloat = FloatStandard::tryFromMixed(2.5);

    // Non-numeric inputs
    $fromArray = FloatStandard::tryFromMixed([1]);
    $fromNull = FloatStandard::tryFromMixed(null);

    // Stringable object
    $stringable = new class {
        public function __toString(): string
        {
            return '1.23';
        }
    };
    $fromStringable = FloatStandard::tryFromMixed($stringable);

    expect($fromNumericString)->toBeInstanceOf(FloatStandard::class)
        ->and($fromNumericString->value())->toBe(1.2)
        ->and($fromInt)->toBeInstanceOf(FloatStandard::class)
        ->and($fromInt->value())->toBe(3.0)
        ->and($fromFloat)->toBeInstanceOf(FloatStandard::class)
        ->and($fromFloat->value())->toBe(2.5)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromStringable)->toBeInstanceOf(FloatStandard::class)
        ->and($fromStringable->value())->toBe(1.23)
        ->and(FloatStandard::tryFromMixed([1], Undefined::create()))->toBeInstanceOf(Undefined::class);
});

it('isEmpty returns false for FloatStandard', function (): void {
    $a = new FloatStandard(-1.0);
    $b = FloatStandard::fromFloat(0.0);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Instances should be defined
    $v1 = new FloatStandard(-1.0);
    $v2 = FloatStandard::fromFloat(0.0);

    // Undefined results via tryFrom*
    $u1 = FloatStandard::tryFromString('not-a-number');
    $u2 = FloatStandard::tryFromMixed([1]);

    expect($v1->isUndefined())->toBeFalse()
        ->and($v2->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue();
});

it('checks diff between string formatting and native float', function (): void {
    $a = new FloatStandard((float) (string) (2 / 3));
    $b = new FloatStandard(2 / 3);

    expect($a->value())->toBe(0.66666666666667)
        ->and($b->value())->toBe(0.6666666666666666);
});

it('converts mixed values to correct float state', function (mixed $input, float $expected): void {
    $result = FloatStandard::tryFromMixed($input);

    expect($result)->toBeInstanceOf(FloatStandard::class)
        ->and($result->value())->toBe($expected);
})->with([
    // Floats
    ['input' => 1.5, 'expected' => 1.5],
    ['input' => 0.0, 'expected' => 0.0],
    ['input' => -3.14, 'expected' => -3.14],
    ['input' => \PHP_FLOAT_MAX, 'expected' => \PHP_FLOAT_MAX],
    ['input' => 1.234567890123456789, 'expected' => 1.234567890123456789],
    ['input' => 2 / 3, 'expected' => 2 / 3],
    ['input' => (string) (2 / 3), 'expected' => (float) (string) (2 / 3)],
    // Type class
    [
        'input' => FloatStandard::fromFloat(1.234567890123456789),
        'expected' => 1.234567890123456789,
    ],
    // Integers
    ['input' => 1, 'expected' => 1.0],
    ['input' => 0, 'expected' => 0.0],
    ['input' => -42, 'expected' => -42.0],
    ['input' => 111, 'expected' => 111.0],
    // Booleans
    ['input' => true, 'expected' => 1.0],
    ['input' => false, 'expected' => 0.0],
    // Strings
    ['input' => '1.5', 'expected' => 1.5],
    ['input' => '0', 'expected' => 0.0],
    ['input' => '-10.5', 'expected' => -10.5],
    // Stringable Object
    ['input' => new class {
        public function __toString(): string
        {
            return '2.5';
        }
    }, 'expected' => 2.5],
]);

it('returns Undefined for invalid mixed inputs', function (mixed $input): void {
    $result = FloatStandard::tryFromMixed($input);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
})->with([
    ['input' => null],
    ['input' => []],
    ['input' => new stdClass()],
    ['input' => 'not-a-float'],
    ['input' => '1.2.3'],
    ['input' => '007'],
    ['input' => fn() => 1.5],                  // Closure
    ['input' => ['FloatStandard', 'fromInt']], // Callable array
    ['input' => fopen('php://memory', 'r')],   // Resource
    ['input' => [new stdClass()]],             // Array of objects
    ['input' => \INF],                          // Infinite value
    ['input' => \NAN],                          // Not a Number
    ['input' => "\0"],                         // Null byte string
]);
