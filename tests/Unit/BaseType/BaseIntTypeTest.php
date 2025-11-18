<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Type\Integer\Integer;

it('fromInt returns exact value and toString matches', function (): void {
    $i1 = Integer::fromInt(-10);
    expect($i1->value())->toBe(-10)
        ->and($i1->toString())->toBe('-10');

    $i2 = Integer::fromInt(0);
    expect($i2->value())->toBe(0)
        ->and($i2->toString())->toBe('0');
});

it('fromString parses valid integer strings including negatives and leading zeros', function (): void {
    expect(Integer::fromString('-15')->value())->toBe(-15)
        ->and(Integer::fromString('0007')->value())->toBe(7)
        ->and(Integer::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-integer strings', function (): void {
    $invalid = ['+5', ' 5', '5 ', '5a', '', 'abc', '--5', '3.14'];
    foreach ($invalid as $str) {
        expect(fn() => Integer::fromString($str))->toThrow(IntegerTypeException::class);
    }
});

it('assertGreaterThan works for exclusive and inclusive bounds', function (): void {
    $i = new Integer(0);

    // exclusive (default): value must be strictly greater than limit
    $i->assertGreaterThan(6, 5); // pass
    $i->assertGreaterThan(5, 4); // pass

    // failing exclusive cases
    expect(fn() => $i->assertGreaterThan(5, 5))->toThrow(IntegerTypeException::class); // equal should fail
    expect(fn() => $i->assertGreaterThan(4, 5))->toThrow(IntegerTypeException::class); // less should fail

    // inclusive: value can be equal to limit
    $i->assertGreaterThan(5, 5, true); // pass

    // failing inclusive case: value less than limit
    expect(fn() => $i->assertGreaterThan(4, 5, true))->toThrow(IntegerTypeException::class);
});

it('assertLessThan works for exclusive and inclusive bounds', function (): void {
    $i = new Integer(0);

    // exclusive (default): value must be strictly less than limit
    $i->assertLessThan(4, 5); // pass

    // failing exclusive cases
    expect(fn() => $i->assertLessThan(5, 5))->toThrow(IntegerTypeException::class); // equal should fail
    expect(fn() => $i->assertLessThan(6, 5))->toThrow(IntegerTypeException::class); // greater should fail

    // inclusive: value can be equal to limit
    $i->assertLessThan(5, 5, true); // pass

    // failing inclusive case: value greater than limit
    expect(fn() => $i->assertLessThan(6, 5, true))->toThrow(IntegerTypeException::class);
});
