<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Undefined\Alias\Undefined;

it('kills the bool decrement mutant in tryFromMixed', function () {
    expect(FloatPositive::tryFromMixed(true))->toBeInstanceOf(FloatPositive::class)
        ->and(FloatPositive::tryFromMixed(true)->value())->toBe(1.0)
        ->and(FloatPositive::tryFromMixed(false))
        ->toBeInstanceOf(Undefined::class);
});

it('constructs positive float via constructor', function (): void {
    $v = new FloatPositive(0.1);
    expect($v->value())->toBe(0.1)
        ->and($v->toString())->toBe('0.10000000000000001');
});

it('creates from float factory', function (): void {
    $v = FloatPositive::fromFloat(1.5);
    expect($v->value())->toBe(1.5);
});

it('creates from string factory', function (): void {
    $v = FloatPositive::fromString('2.5');
    expect($v->value())->toBe(2.5)
        ->and($v->toString())->toBe('2.5');
});

it('throws on zero via constructor', function (): void {
    expect(fn() => new FloatPositive(0.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0.0"');
});

it('throws on zero via fromString', function (): void {
    expect(fn() => FloatPositive::fromString('0.0'))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0.0"');
});

it('throws on negative via constructor', function (): void {
    expect(fn() => new FloatPositive(-0.1))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-0.10000000000000001"');
});

it('throws on negative via fromString', function (): void {
    expect(fn() => FloatPositive::fromString('-1.23'))
        ->toThrow(StringTypeException::class, 'String "-1.23" has no valid strict float value');
});

it('throws on string not float', function (): void {
    expect(fn() => FloatPositive::fromString('unknown'))
        ->toThrow(StringTypeException::class, 'String "unknown" has no valid float value');
});

it('FloatPositive::tryFromString returns value for > 0.0 and Undefined otherwise', function (): void {
    $ok = FloatPositive::tryFromString('0.10000000000000001');
    $badZero = FloatPositive::tryFromString('0.0');
    $badNeg = FloatPositive::tryFromString('-0.10000000000000001');
    $badStr = FloatPositive::tryFromString('abc');

    expect($ok)
        ->toBeInstanceOf(FloatPositive::class)
        ->and($ok->value())->toBe(0.1)
        ->and($badZero)->toBeInstanceOf(Undefined::class)
        ->and($badNeg)->toBeInstanceOf(Undefined::class)
        ->and($badStr)->toBeInstanceOf(Undefined::class)
        ->and(FloatPositive::tryFromString('0.0', Undefined::create()))->toBeInstanceOf(Undefined::class);
});

it('FloatPositive::tryFromFloat returns value for positive int and Undefined otherwise', function (): void {
    $ok = FloatPositive::tryFromFloat(2);
    $bad = FloatPositive::tryFromFloat(0);

    expect($ok)
        ->toBeInstanceOf(FloatPositive::class)
        ->and($ok->value())
        ->toBe(2.0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('FloatPositive throws on non-positive values in ctor and fromFloat', function (): void {
    expect(fn() => new FloatPositive(0.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0.0"')
        ->and(fn() => FloatPositive::fromFloat(-1.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-1.0"');
});

it('triggers FloatTypeException with non-strict floatToString in ctor', function (): void {
    // 1e-324 is non-zero but will round to 0.0 in floatToString, failing strict check if it was enabled.
    // By using it here, we ensure that FalseToTrue mutant is killed.
    expect(fn() => new FloatPositive(-1e-308))
        ->toThrow(FloatTypeException::class);
});

it('FloatPositive::fromString enforces numeric and positivity', function (): void {
    // Non-numeric
    expect(fn() => FloatPositive::fromString('abc'))
        ->toThrow(StringTypeException::class, 'String "abc" has no valid float value');

    // Positivity
    expect(fn() => FloatPositive::fromString('0.0'))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0.0"');

    // Success path
    $v = FloatPositive::fromString('1.25');
    expect($v->value())->toBe(1.25);
});

it('jsonSerialize returns float', function (): void {
    expect(FloatPositive::tryFromString('1.10000000000000009')->jsonSerialize())->toBeFloat();
});

it('__toString mirrors toString and value', function (): void {
    $v = FloatPositive::fromFloat(3.14);

    expect((string) $v)
        ->toBe('3.14000000000000012')
        ->and($v->toString())
        ->toBe('3.14000000000000012')
        ->and($v->value())
        ->toBe(3.14);
});

it('converts mixed values to correct float state', function (mixed $input, float $expected): void {
    $result = FloatPositive::tryFromMixed($input);

    expect($result)->toBeInstanceOf(FloatPositive::class)
        ->and($result->value())->toBe($expected);
})->with([
    // Floats
    ['input' => 1.5, 'expected' => 1.5],
    ['input' => \PHP_FLOAT_MAX, 'expected' => \PHP_FLOAT_MAX],
    ['input' => 1.234567890123456789, 'expected' => 1.234567890123456789],
    ['input' => 2 / 3, 'expected' => 2 / 3],
    //    ['input' => (string) (2 / 3), 'expected' => (float) (string) (2 / 3)],
    // Type class
    [
        'input' => FloatPositive::fromFloat(1.234567890123456789),
        'expected' => 1.234567890123456789,
    ],
    // Self instance input
    [
        'input' => FloatPositive::fromFloat(4.5),
        'expected' => 4.5,
    ],
    // Integers
    ['input' => 1, 'expected' => 1.0],
    ['input' => 111, 'expected' => 111.0],
    // Booleans
    ['input' => true, 'expected' => 1.0],
    // Strings
    ['input' => '1.5', 'expected' => 1.5],
    ['input' => '0.5', 'expected' => 0.5],
    ['input' => '0.10000000000000001', 'expected' => 0.1],
    ['input' => '3.14000000000000012', 'expected' => 3.14],
    // Stringable Object
    ['input' => new class {
        public function __toString(): string
        {
            return '2.5';
        }
    }, 'expected' => 2.5],
]);

it('returns Undefined for invalid mixed inputs', function (mixed $input): void {
    $result = FloatPositive::tryFromMixed($input);

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
    ['input' => ['FloatPositive', 'fromInt']], // Callable array
    ['input' => fopen('php://memory', 'r')],   // Resource
    ['input' => [new stdClass()]],             // Array of objects
    ['input' => \INF],                          // Infinite value
    ['input' => \NAN],                          // Not a Number
    ['input' => "\0"],                         // Null byte string
    ['input' => 0.0],                          // Zero float
    ['input' => 0],                            // Zero integer
    ['input' => false],                        // False boolean (0.0)
    ['input' => '0'],                          // Zero string
    ['input' => -3.14],                        // Negative float
    ['input' => -42],                          // Negative integer
    ['input' => '-10.5'],                      // Negative string
]);

it('isEmpty returns false for FloatPositive', function (): void {
    $a = new FloatPositive(0.1);
    $b = FloatPositive::fromFloat(2.5);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Valid instances should report isUndefined() = false
    $v1 = new FloatPositive(0.1);
    $v2 = FloatPositive::fromFloat(2.5);

    // Invalid inputs via tryFrom* produce Undefined which should report true
    $u1 = FloatPositive::tryFromString('0.0');
    $u2 = FloatPositive::tryFromMixed('abc');
    $u3 = FloatPositive::tryFromFloat(0.0);

    expect($v1->isUndefined())->toBeFalse()
        ->and($v2->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue()
        ->and($u3->isUndefined())->toBeTrue();
});

it('covers conversions for FloatPositive', function (): void {
    $f = FloatPositive::fromFloat(1.0);
    expect($f->toBool())->toBeTrue()
        ->and($f->toInt())->toBe(1)
        ->and($f->toFloat())->toBe(1.0)
        ->and($f->toString())->toBe('1.0');

    expect(fn() => FloatPositive::fromFloat(0.5)->toBool())->toThrow(FloatTypeException::class)
        ->and(fn() => FloatPositive::fromFloat(0.5)->toInt())->toThrow(FloatTypeException::class);

    expect(FloatPositive::tryFromBool(true))->toBeInstanceOf(FloatPositive::class)
        ->and(FloatPositive::tryFromBool(false))->toBeInstanceOf(Undefined::class)
        ->and(FloatPositive::tryFromInt(5))->toBeInstanceOf(FloatPositive::class)
        ->and(FloatPositive::tryFromInt(0))->toBeInstanceOf(Undefined::class);
});

it('isTypeOf returns true when class matches', function (): void {
    $v = FloatPositive::fromFloat(1.5);
    expect($v->isTypeOf(FloatPositive::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = FloatPositive::fromFloat(1.5);
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = FloatPositive::fromFloat(1.5);
    expect($v->isTypeOf('NonExistentClass', FloatPositive::class, 'AnotherClass'))->toBeTrue();
});
