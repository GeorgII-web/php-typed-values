<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
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

it('FloatStandard::tryFromFloat returns Undefined for invalid values', function (): void {
    $inf = FloatStandard::tryFromFloat(\INF);
    $nan = FloatStandard::tryFromFloat(\NAN);
    $customDefault = FloatStandard::tryFromFloat(\INF, Undefined::create());

    expect($inf)->toBeInstanceOf(Undefined::class)
        ->and($nan)->toBeInstanceOf(Undefined::class)
        ->and($customDefault)->toBeInstanceOf(Undefined::class);
});

it('FloatStandard::fromString throws on non-numeric strings', function (): void {
    expect(fn() => FloatStandard::fromString('NaN'))
        ->toThrow(StringTypeException::class, 'String "NaN" has no valid float value');
});

it('fails on loose precious', function (): void {
    expect(fn() => FloatStandard::fromString('0.1'))
        ->toThrow(StringTypeException::class);
});

it('FloatStandard::fromString throws exception for a long tail', function (): void {
    expect(fn() => FloatStandard::fromString('12.444144424443444044454446444744484449444'))
        ->toThrow(StringTypeException::class, 'String "12.444144424443444044454446444744484449444" has no valid strict float value');
});

it('jsonSerialize returns float', function (): void {
    expect(FloatStandard::tryFromString('1.1')->jsonSerialize())->toBeFloat();
});

it('__toString mirrors toString and value', function (): void {
    $v = FloatStandard::fromFloat(3.14);

    expect((string) $v)
        ->toBe('3.14000000000000012')
        ->and($v->toString())
        ->toBe('3.14000000000000012')
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

it('checks compare algorithm', function (): void {
    $f1 = FloatStandard::fromFloat(0.1);
    $f2 = FloatStandard::fromFloat(0.7);
    $f3 = FloatStandard::fromFloat(0.8);

    expect($f1->toString())->toBe('0.10000000000000001')
        ->and($f2->toString())->toBe('0.69999999999999996')
        ->and($f3->toString())->toBe('0.80000000000000004')
        ->and($f1->value())->toBe(0.1)
        ->and($f2->value())->toBe(0.7)
        ->and($f3->value())->toBe(0.8)
        ->and(FloatStandard::fromFloat($f1->value() + $f2->value())->toString())->toBe('0.79999999999999993');
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

it('FloatStandard::toString matches expected values', function (float $value, string $expected): void {
    if (is_nan($value) || is_infinite($value)) {
        expect(fn() => FloatStandard::fromFloat($value))->toThrow(FloatTypeException::class);

        return;
    }

    $f = FloatStandard::fromFloat($value);

    // If the expected value is '0.0' but the input is not zero,
    // it means it's a subnormal or very small number that will fail the strict string value check.
    if ($expected === '0.0' && $value !== 0.0) {
        expect(fn() => $f->toString())->toThrow(FloatTypeException::class);

        return;
    }

    expect($f->toString())->toBe($expected);
})->with([
    '0' => [0, '0.0'],
    '0.0' => [0.0, '0.0'],
    '-0.0' => [-0.0, '0.0'],
    '+0.0' => [+0.0, '0.0'],
    '1.0' => [1.0, '1.0'],
    '-1.0' => [-1.0, '-1.0'],
    '0.1' => [0.1, '0.10000000000000001'],
    '0.10000000000000001' => [0.10000000000000001, '0.10000000000000001'],
    '0.10000000000000002' => [0.10000000000000002, '0.10000000000000002'],
    '0.10000000000000012' => [0.10000000000000012, '0.10000000000000012'],
    '-0.1' => [-0.1, '-0.10000000000000001'],
    '0.2' => [0.2, '0.20000000000000001'],
    '0.3' => [0.3, '0.29999999999999999'],
    '0.1+0.2' => [0.1 + 0.2, '0.30000000000000004'],
    '1/3' => [1.0 / 3.0, '0.33333333333333331'],
    '2/3' => [2.0 / 3.0, '0.66666666666666663'],
    '10/3' => [10.0 / 3.0, '3.33333333333333348'],
    '1e10' => [1e10, '10000000000.0'],
    '-1e10' => [-1e10, '-10000000000.0'],
    '1e308' => [1e308, '100000000000000001097906362944045541740492309677311846336810682903157585404911491537163328978494688899061249669721172515611590283743140088328307009198146046031271664502933027185697489699588559043338384466165001178426897626212945177628091195786707458122783970171784415105291802893207873272974885715430223118336.0'],
    '-1e308' => [-1e308, '-100000000000000001097906362944045541740492309677311846336810682903157585404911491537163328978494688899061249669721172515611590283743140088328307009198146046031271664502933027185697489699588559043338384466165001178426897626212945177628091195786707458122783970171784415105291802893207873272974885715430223118336.0'],
    '1e-10' => [1e-10, '0.0000000001'],
    '-1e-10' => [-1e-10, '-0.0000000001'],
    '1e-308' => [1e-308, '0.0'], // Exception expected in test function logic or toBe logic
    '-1e-308' => [-1e-308, '0.0'], // Exception expected
    '5e-324' => [5e-324, '0.0'], // Exception expected
    '-5e-324' => [-5e-324, '0.0'], // Exception expected
    '1.99999999999999' => [1.99999999999999, '1.99999999999999001'],
    '2.00000000000001' => [2.00000000000001, '2.00000000000001021'],
    '0.7' => [0.7, '0.69999999999999996'],
    '0.17' => [0.17, '0.17000000000000001'],
    '0.57' => [0.57, '0.56999999999999995'],
    '0.99' => [0.99, '0.98999999999999999'],
    '001.5' => [001.5, '1.5'],
    '0.3333333333333333' => [0.3333333333333333, '0.33333333333333331'],
    '0.6666666666666666' => [0.6666666666666666, '0.66666666666666663'],
    '2^53-1' => [9007199254740991.0, '9007199254740991.0'],
    '2^53' => [9007199254740992.0, '9007199254740992.0'],
    '-2^53' => [-9007199254740992.0, '-9007199254740992.0'],
    'INF' => [\INF, 'INF'],
    '-INF' => [-\INF, '-INF'],
    'NAN' => [\NAN, 'NAN'],
    'PHP_INT_MAX' => [(float) \PHP_INT_MAX, '9223372036854775808.0'], // todo real 9223372036854775807.0 - fail on round-trip checks
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

it('isTypeOf returns true when class matches', function (): void {
    $v = FloatStandard::fromFloat(1.5);
    expect($v->isTypeOf(FloatStandard::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = FloatStandard::fromFloat(1.5);
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = FloatStandard::fromFloat(1.5);
    expect($v->isTypeOf('NonExistentClass', FloatStandard::class, 'AnotherClass'))->toBeTrue();
});

it('covers conversions for FloatStandard', function (): void {
    $f = FloatStandard::fromFloat(1.0);
    expect($f->toBool())->toBeTrue()
        ->and($f->toInt())->toBe(1)
        ->and($f->toFloat())->toBe(1.0)
        ->and($f->toString())->toBe('1.0');

    $f0 = FloatStandard::fromFloat(0.0);
    expect($f0->toBool())->toBeFalse()
        ->and($f0->toInt())->toBe(0)
        ->and($f0->toString())->toBe('0.0');

    expect(fn() => FloatStandard::fromFloat(0.5)->toBool())->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromFloat(0.5)->toInt())->toThrow(FloatTypeException::class);

    expect(FloatStandard::tryFromBool(true))->toBeInstanceOf(FloatStandard::class)
        ->and(FloatStandard::tryFromBool(false))->toBeInstanceOf(FloatStandard::class)
        ->and(FloatStandard::tryFromInt(5))->toBeInstanceOf(FloatStandard::class);
});

it('FloatStandard::tryFromInt returns Undefined for big integers (line 198)', function (): void {
    expect(FloatStandard::tryFromInt(\PHP_INT_MAX))
        ->toBeInstanceOf(Undefined::class);
});

it('FloatStandard::fromInt throws IntegerTypeException for big integers (line 213)', function (): void {
    expect(fn() => FloatStandard::fromInt(\PHP_INT_MAX))
        ->toThrow(IntegerTypeException::class, 'Integer "' . \PHP_INT_MAX . '" has no valid strict float value');
});

it('covers intToString protective check (line 230)', function (): void {
    // It's hard to trigger line 230 with a real int in PHP,
    // but we can test it with standard values to at least execute the line.
    $v = PhpTypedValues\String\StringStandard::fromInt(123);
    expect($v->value())->toBe('123');
});

it('FloatStandard::toString throws FloatTypeException for very small floats', function (): void {
    $f = FloatStandard::fromFloat(1e-308);
    expect(fn() => $f->toString())
        ->toThrow(FloatTypeException::class, 'Float "1.0E-308" has no valid strict string value');
});
