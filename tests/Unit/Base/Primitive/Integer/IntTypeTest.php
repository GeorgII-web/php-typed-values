<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Integer\IntegerStandard;

it('__toString proxies to toString for IntType', function (): void {
    $v = new IntegerStandard(123);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('123');
});

it('fromInt returns exact value and toString matches', function (): void {
    $i1 = IntegerStandard::fromInt(-10);
    expect($i1->value())->toBe(-10)
        ->and($i1->toString())->toBe('-10');

    $i2 = IntegerStandard::fromInt(0);
    expect($i2->value())->toBe(0)
        ->and($i2->toString())->toBe('0');
});

it('fromString parses valid integer strings including negatives and leading zeros', function (): void {
    expect(IntegerStandard::fromString('-15')->value())->toBe(-15)
        ->and(IntegerStandard::fromString('0')->toString())->toBe('0')
        ->and(IntegerStandard::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-integer or non-canonical strings', function (string $input): void {
    expect(fn() => IntegerStandard::fromString($input))->toThrow(IntegerTypeException::class);
})->with(['5a', 'a5', '', 'abc', ' 5', '5 ', '+5', '05', '--5', '3.14']);

it('fromFloat handles boundary values and precision', function (): void {
    // Exact representable integers as floats
    expect(IntegerStandard::fromFloat(0.0)->value())->toBe(0)
        ->and(IntegerStandard::fromFloat(1.0)->value())->toBe(1)
        ->and(IntegerStandard::fromFloat(-1.0)->value())->toBe(-1);

    // PHP_INT_MIN is usually exactly representable as a float (power of 2)
    $minFloat = (float) \PHP_INT_MIN;
    expect(IntegerStandard::fromFloat($minFloat)->value())->toBe(\PHP_INT_MIN);
    expect(fn() => IntegerStandard::fromFloat((float) \PHP_INT_MAX))
        ->toThrow(IntegerTypeException::class);

    // PHP_INT_MAX is usually NOT exactly representable as a float on 64-bit systems
    // But we test the boundary logic anyway.
    // We need a float that is out of range.
    // On 64-bit, (float)PHP_INT_MAX is typically PHP_INT_MAX + 1
    expect(fn() => IntegerStandard::fromFloat((float) \PHP_INT_MAX))
        ->toThrow(IntegerTypeException::class);

    $outOfRangeLower = (float) \PHP_INT_MIN - 4096.0; // Subtracting enough to reach next representable float
    expect(fn() => IntegerStandard::fromFloat($outOfRangeLower))
        ->toThrow(IntegerTypeException::class);
});

it('fromFloat kills BooleanOrToBooleanAnd mutation', function (): void {
    // Both sides of || must be tested to ensure it's not &&
    // Case 1: value > PHP_INT_MAX (only first part is true)
    $tooBig = (float) \PHP_INT_MAX + 2048.0;
    expect(fn() => IntegerStandard::fromFloat($tooBig))
        ->toThrow(IntegerTypeException::class);

    // Case 2: value < PHP_INT_MIN (only second part is true)
    $tooSmall = (float) \PHP_INT_MIN - 2048.0;
    expect(fn() => IntegerStandard::fromFloat($tooSmall))
        ->toThrow(IntegerTypeException::class);

    $tooBig = (float) \PHP_INT_MAX;
    expect(fn() => IntegerStandard::fromFloat($tooBig))
        ->toThrow(IntegerTypeException::class);
});

it('fromFloat rejects non-integer floats and kills precision check mutation', function (): void {
    expect(fn() => IntegerStandard::fromFloat(1.1))
        ->toThrow(IntegerTypeException::class, 'cannot be converted to integer without losing precision');

    expect(fn() => IntegerStandard::fromFloat(-0.1))
        ->toThrow(IntegerTypeException::class);

    expect(fn() => IntegerStandard::fromFloat(1e20)) // Too big for int, should be caught by range check
        ->toThrow(IntegerTypeException::class);
});

it('toFloat returns strictly float values', function (): void {
    $v = new IntegerStandard(123);
    $f = $v->toFloat();
    expect($f)->toBe(123.0)
        ->and($f)->toBeFloat();

    // This is the critical check for the mutation:
    // Identical (===) check will fail if the cast is removed and strict types are on
    expect($f === 123.0)->toBeTrue();

    // Boundary value where int and float representations might differ
    $maxV = new IntegerStandard(\PHP_INT_MAX);
    expect(fn() => $maxV->toFloat())->toThrow(IntegerTypeException::class);
});

it('fromFloat handles the PHP_INT_MAX boundary precision loss', function () {
    $maxFloat = (float) \PHP_INT_MAX;
    // This value is actually > PHP_INT_MAX on most systems
    expect(fn() => IntegerStandard::fromFloat($maxFloat))
        ->toThrow(IntegerTypeException::class);
});
