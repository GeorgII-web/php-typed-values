<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts non-negative floats via fromFloat and toString matches', function (): void {
    $f0 = FloatNonNegative::fromFloat(0.0);
    expect($f0->value())->toBe(0.0)
        ->and($f0->toString())->toBe('0.0');

    $f1 = FloatNonNegative::fromFloat(1.5);
    expect($f1->value())->toBe(1.5)
        ->and($f1->toString())->toBe('1.5');
});

it('parses non-negative numeric strings via fromString', function (): void {
    expect(FloatNonNegative::fromString('0.0')->value())->toBe(0.0)
        ->and(FloatNonNegative::fromString('3.14000000000000012')->value())->toBe(3.14)
        ->and(FloatNonNegative::fromString('42.0')->toString())->toBe('42.0');
});

it('rejects negative values', function (): void {
    expect(fn() => new FloatNonNegative(-0.001))
        ->toThrow(FloatTypeException::class);
    expect(fn() => FloatNonNegative::fromFloat(-0.001))
        ->toThrow(FloatTypeException::class);
});

it('rejects non-numeric or negative strings', function (): void {
    // Non-numeric
    foreach (['', 'abc', '5,5'] as $str) {
        expect(fn() => FloatNonNegative::fromString($str))
            ->toThrow(StringTypeException::class);
    }

    // Numeric but negative
    foreach (['-1.0', '-0.10000000000000001'] as $str) {
        expect(fn() => FloatNonNegative::fromString($str))
            ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "');
    }
});

it('FloatNonNegative::tryFromString returns value for >= 0.0 and Undefined otherwise', function (): void {
    $ok0 = FloatNonNegative::tryFromString('0.0');
    $ok = FloatNonNegative::tryFromString('0.5');
    $bad = FloatNonNegative::tryFromString('-0.10000000000000001');
    $badStr = FloatNonNegative::tryFromString('abc');

    expect($ok0)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok0->value())->toBe(0.0)
        ->and($ok)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok->value())->toBe(0.5)
        ->and($bad)->toBeInstanceOf(Undefined::class)
        ->and($badStr)->toBeInstanceOf(Undefined::class)
        ->and(FloatNonNegative::tryFromString('-0.10000000000000001', Undefined::create()))->toBeInstanceOf(Undefined::class);
});

it('FloatNonNegative::tryFromFloat returns value for >= 0 and Undefined otherwise', function (): void {
    $ok = FloatNonNegative::tryFromFloat(0);
    $bad = FloatNonNegative::tryFromFloat(-1);

    expect($ok)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok->value())
        ->toBe(0.0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('FloatNonNegative throws on negative values in ctor and fromFloat', function (): void {
    expect(fn() => new FloatNonNegative(-0.1))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-0.10000000000000001"')
        ->and(fn() => FloatNonNegative::fromFloat(-1.0))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-1.0"');
});

it('triggers FloatTypeException with non-strict floatToString in ctor', function (): void {
    // 1e-308 will fail strict conversion in floatToString if the second parameter was true.
    // But since it's false in the ctor's exception message generation, it should not throw another exception
    // during the generation of the exception message.
    expect(fn() => new FloatNonNegative(-1e-308))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "0.0"');
});

it('FloatNonNegative::fromString enforces numeric and non-negativity', function (): void {
    // Non-numeric
    expect(fn() => FloatNonNegative::fromString('abc'))
        ->toThrow(StringTypeException::class, 'String "abc" has no valid float value');

    // Non-negativity
    expect(fn() => FloatNonNegative::fromString('-0.5'))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-0.5"');

    // Success path
    $v = FloatNonNegative::fromString('0.75');
    expect($v->value())->toBe(0.75);
});

it('jsonSerialize returns float', function (): void {
    expect(FloatNonNegative::tryFromString('1.10000000000000009')->jsonSerialize())->toBeFloat();
});

it('__toString casts same as toString and equals string representation', function (): void {
    $v = FloatNonNegative::fromFloat(2.5);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('2.5');
});

it('accepts negative zero and normalizes to "-0" in toString', function (): void {
    // fromFloat with -0.0 must be treated as non-negative
    $v = FloatNonNegative::fromFloat(-0.0);

    expect($v->value())
        ->toBe(0.0)
        ->and($v->toString())
        ->toBe('0.0');
});

it('converts mixed values to correct float state', function (mixed $input, float $expected): void {
    $result = FloatNonNegative::tryFromMixed($input);

    expect($result)->toBeInstanceOf(FloatNonNegative::class)
        ->and($result->value())->toBe($expected);
})->with([
    // Floats
    ['input' => 1.5, 'expected' => 1.5],
    ['input' => 0.0, 'expected' => 0.0],
    ['input' => \PHP_FLOAT_MAX, 'expected' => \PHP_FLOAT_MAX],
    ['input' => 1.234567890123456789, 'expected' => 1.234567890123456789],
    ['input' => 2 / 3, 'expected' => 2 / 3],
    //    ['input' => (string) (2 / 3), 'expected' => (float) (string) (2 / 3)],
    // Type class
    [
        'input' => FloatNonNegative::fromFloat(1.234567890123456789),
        'expected' => 1.234567890123456789,
    ],
    // Self instance input
    [
        'input' => FloatNonNegative::fromFloat(4.5),
        'expected' => 4.5,
    ],
    // Integers
    ['input' => 1, 'expected' => 1.0],
    ['input' => 0, 'expected' => 0.0],
    ['input' => 111, 'expected' => 111.0],
    // Booleans
    ['input' => true, 'expected' => 1.0],
    ['input' => false, 'expected' => 0.0],
    // Strings
    ['input' => '1.5', 'expected' => 1.5],
    ['input' => '0.0', 'expected' => 0.0],
    // Stringable Object
    ['input' => new class {
        public function __toString(): string
        {
            return '2.5';
        }
    }, 'expected' => 2.5],
]);

it('returns Undefined for invalid mixed inputs', function (mixed $input): void {
    $result = FloatNonNegative::tryFromMixed($input);

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
    ['input' => ['FloatNonNegative', 'fromInt']], // Callable array
    ['input' => fopen('php://memory', 'r')],   // Resource
    ['input' => [new stdClass()]],             // Array of objects
    ['input' => \INF],                          // Infinite value
    ['input' => \NAN],                          // Not a Number
    ['input' => "\0"],                         // Null byte string
    ['input' => -3.14],                        // Negative float
    ['input' => -42],                          // Negative integer
    ['input' => '-10.5'],                      // Negative string
]);

it('jsonSerialize equals value() for valid instances', function (): void {
    $v = FloatNonNegative::fromString('10.5');
    expect($v->jsonSerialize())->toBe($v->value());
});

it('isEmpty returns false for FloatNonNegative', function (): void {
    $a = new FloatNonNegative(0.0);
    $b = FloatNonNegative::fromFloat(3.14);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Valid instances should report isUndefined() = false
    $v1 = new FloatNonNegative(0.0);
    $v2 = FloatNonNegative::fromFloat(1.0);

    // Invalid inputs via tryFrom* produce Undefined which should report true
    $u1 = FloatNonNegative::tryFromString('-0.10000000000000001');
    $u2 = FloatNonNegative::tryFromMixed('abc');
    $u3 = FloatNonNegative::tryFromFloat(-1.0);

    expect($v1->isUndefined())->toBeFalse()
        ->and($v2->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue()
        ->and($u3->isUndefined())->toBeTrue();
});

it('covers conversions for FloatNonNegative', function (): void {
    $f = FloatNonNegative::fromFloat(1.0);
    expect($f->toBool())->toBeTrue()
        ->and($f->toInt())->toBe(1)
        ->and($f->toFloat())->toBe(1.0)
        ->and($f->toString())->toBe('1.0');

    $f0 = FloatNonNegative::fromFloat(0.0);
    expect($f0->toBool())->toBeFalse()
        ->and($f0->toInt())->toBe(0)
        ->and($f0->toString())->toBe('0.0');

    expect(fn() => FloatNonNegative::fromFloat(0.5)->toBool())->toThrow(FloatTypeException::class)
        ->and(fn() => FloatNonNegative::fromFloat(0.5)->toInt())->toThrow(FloatTypeException::class);

    expect(FloatNonNegative::tryFromBool(true))->toBeInstanceOf(FloatNonNegative::class)
        ->and(FloatNonNegative::tryFromBool(false))->toBeInstanceOf(FloatNonNegative::class)
        ->and(FloatNonNegative::tryFromInt(5))->toBeInstanceOf(FloatNonNegative::class)
        ->and(FloatNonNegative::tryFromInt(-5))->toBeInstanceOf(Undefined::class);
});

it('isTypeOf returns true when class matches', function (): void {
    $v = FloatNonNegative::fromFloat(1.5);
    expect($v->isTypeOf(FloatNonNegative::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = FloatNonNegative::fromFloat(1.5);
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = FloatNonNegative::fromFloat(1.5);
    expect($v->isTypeOf('NonExistentClass', FloatNonNegative::class, 'AnotherClass'))->toBeTrue();
});
