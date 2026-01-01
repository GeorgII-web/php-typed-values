<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\ReasonableRangeIntegerTypeException;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerStandard::tryFromString returns value on valid integer string', function (): void {
    $v = IntegerStandard::tryFromString('123');

    expect($v)
        ->toBeInstanceOf(IntegerStandard::class)
        ->and($v->value())
        ->toBe(123);
});

it('IntegerStandard::tryFromString returns Undefined on invalid integer string', function (): void {
    $v = IntegerStandard::tryFromString('5.0');

    expect($v)->toBeInstanceOf(Undefined::class);
});

it('IntegerStandard::tryFromInt always returns value for any int', function (): void {
    $v = IntegerStandard::tryFromInt(-999);

    expect($v)
        ->toBeInstanceOf(IntegerStandard::class)
        ->and($v->value())
        ->toBe(-999);
});

it('IntegerStandard::fromInt returns instance and preserves value', function (): void {
    $v = IntegerStandard::fromInt(42);

    expect($v)
        ->toBeInstanceOf(IntegerStandard::class)
        ->and($v->value())->toBe(42)
        ->and($v->toString())->toBe('42');
});

it('IntegerStandard::fromString throws on non-integer strings (strict check)', function (): void {
    expect(fn() => IntegerStandard::fromString('12.3'))
        ->toThrow(IntegerTypeException::class, 'String "12.3" has no valid strict integer value');
});

it('creates Integer from int', function (): void {
    expect(IntegerStandard::fromInt(5)->value())->toBe(5);
});

it('creates Integer from string', function (): void {
    expect(IntegerStandard::fromString('5')->value())->toBe(5);
});

it('fails on "integer-ish" float string', function (): void {
    expect(fn() => IntegerStandard::fromString('5.'))->toThrow(IntegerTypeException::class);
});

it('fails on float string', function (): void {
    expect(fn() => IntegerStandard::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('fails on type mismatch', function (): void {
    // Instead of passing wrong-typed value to fromInt (violates Psalm),
    // verify mixed conversion path rejects non-integer-like input.
    $u = IntegerStandard::tryFromMixed('12.3');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns integer', function (): void {
    expect(IntegerStandard::tryFromString('1')->jsonSerialize())->toBeInt();
});

it('wraps any PHP int and preserves value/toString', function (): void {
    $n = new IntegerStandard(-10);
    $p = IntegerStandard::fromInt(42);

    expect($n->value())->toBe(-10)
        ->and($n->toInt())->toBe(-10)
        ->and($n->toString())->toBe('-10')
        ->and((string) $n)->toBe('-10')
        ->and($p->value())->toBe(42)
        ->and($p->toInt())->toBe(42)
        ->and($p->toString())->toBe('42');
});

it('fromString uses strict integer parsing', function (): void {
    expect(IntegerStandard::fromString('-5')->value())->toBe(-5)
        ->and(IntegerStandard::fromString('0')->value())->toBe(0)
        ->and(IntegerStandard::fromString('17')->toString())->toBe('17');

    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerStandard::fromString($bad))
            ->toThrow(IntegerTypeException::class);
    }
});

it('tryFromInt always returns instance; tryFromString returns Undefined on invalid', function (): void {
    $okI1 = IntegerStandard::tryFromInt(\PHP_INT_MIN + 1);
    $okI2 = IntegerStandard::tryFromInt(\PHP_INT_MAX - 1);
    $okS = IntegerStandard::tryFromString('123');
    $badS = IntegerStandard::tryFromString('01');

    expect($okI1)->toBeInstanceOf(IntegerStandard::class)
        ->and($okI2)->toBeInstanceOf(IntegerStandard::class)
        ->and($okS)->toBeInstanceOf(IntegerStandard::class)
        ->and($badS)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns native int', function (): void {
    expect(IntegerStandard::fromInt(-3)->jsonSerialize())->toBe(-3);
});

it('tryFromMixed returns instance for integer-like inputs and Undefined otherwise', function (): void {
    $okInt = IntegerStandard::tryFromMixed(15);
    $okStr = IntegerStandard::tryFromMixed('20');
    $badF = IntegerStandard::tryFromMixed('1.0');
    $badX = IntegerStandard::tryFromMixed(['x']);

    expect($okInt)->toBeInstanceOf(IntegerStandard::class)
        ->and($okInt->value())->toBe(15)
        ->and($okStr)->toBeInstanceOf(IntegerStandard::class)
        ->and($okStr->toString())->toBe('20')
        ->and($badF)->toBeInstanceOf(Undefined::class)
        ->and($badX)->toBeInstanceOf(Undefined::class);
});

it('isEmpty returns false for IntegerStandard', function (): void {
    $a = new IntegerStandard(-1);
    $b = IntegerStandard::fromInt(0);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined is always false', function (): void {
    expect(IntegerStandard::fromInt(0)->isUndefined())->toBeFalse()
        ->and(IntegerStandard::fromInt(1)->isUndefined())->toBeFalse();
});

it('converts mixed values to correct integer state', function (mixed $input, int $expected): void {
    $result = IntegerStandard::tryFromMixed($input);

    expect($result)->toBeInstanceOf(IntegerStandard::class)
        ->and($result->value())->toBe($expected);
})->with([
    // Integers
    ['input' => 1, 'expected' => 1],
    ['input' => 0, 'expected' => 0],
    ['input' => -42, 'expected' => -42],
    ['input' => \PHP_INT_MAX, 'expected' => \PHP_INT_MAX],
    ['input' => \PHP_INT_MIN, 'expected' => \PHP_INT_MIN],
    // Type class
    [
        'input' => IntegerStandard::fromInt(123),
        'expected' => 123,
    ],
    // Float
    ['input' => 55.0, 'expected' => 55],
    // Booleans
    ['input' => true, 'expected' => 1],
    ['input' => false, 'expected' => 0],
    // Strings
    ['input' => '15', 'expected' => 15],
    ['input' => '0', 'expected' => 0],
    ['input' => '-10', 'expected' => -10],
    // Stringable Object
    ['input' => new class {
        public function __toString(): string
        {
            return '25';
        }
    }, 'expected' => 25],
]);

it('returns Undefined for invalid mixed integer inputs', function (mixed $input): void {
    $result = IntegerStandard::tryFromMixed($input);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
})->with([
    ['input' => null],
    ['input' => []],
    ['input' => 1.5],                  // Float
    ['input' => new stdClass()],
    ['input' => 'not-an-int'],
    ['input' => '1.0'],                // Float string
    ['input' => '01'],                 // Invalid format (leading zero)
    ['input' => fn() => 1],            // Closure
    ['input' => fopen('php://memory', 'r')], // Resource
    ['input' => \INF],                 // Infinite
    ['input' => \NAN],                 // NaN
]);

it('IntegerStandard::fromString validates various edge cases', function (
    string $input,
    int|array $expected,
): void {
    if (\is_int($expected)) {
        expect(IntegerStandard::fromString($input)->value())->toBe($expected);
    } else {
        [$exceptionClass, $message] = $expected;
        expect(fn() => IntegerStandard::fromString($input))
            ->toThrow($exceptionClass, $message);
    }
})->with([
    // --- 1. Valid Decimals (Standard) ---
    'valid_int' => ['42', 42],
    'valid_negative' => ['-42', -42],
    'int_max' => [(string) \PHP_INT_MAX, \PHP_INT_MAX],
    'int_min' => [(string) \PHP_INT_MIN, \PHP_INT_MIN],

    // --- 2. Range & Precision Issues ---
    'str_overflow' => ['9223372036854775808', [ReasonableRangeIntegerTypeException::class, 'no reasonable range integer value']],
    'str_too_long' => ['123456789012345678901234567890', [ReasonableRangeIntegerTypeException::class, 'no reasonable range integer value']],
    'str_huge' => ['123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890', [ReasonableRangeIntegerTypeException::class, 'no reasonable range integer value']],

    // --- 3. Notation & Formatting ---
    'str_plus_sign' => ['+42', [IntegerTypeException::class, 'is not in canonical form ("42")']],
    'str_leading_zero' => ['012', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_scientific' => ['1e2', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_comma' => ['1,000', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_decimal_dot' => ['42.0', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_dot_start' => ['.42', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_dot_end' => ['42.', [IntegerTypeException::class, 'has no valid strict integer value']],

    // --- 4. Alternative Bases ---
    'str_hex' => ['0x1A', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_octal_pref' => ['0o10', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_bin_pref' => ['0b1101', [IntegerTypeException::class, 'has no valid strict integer value']],

    // --- 5. Whitespace & Control Characters ---
    'str_space_lead' => [' 42', [IntegerTypeException::class, 'is not in canonical form ("42")']],
    'str_newline_end' => ["42\n", [IntegerTypeException::class, 'is not in canonical form ("42")']],
    'str_whitespace' => [" \t\n\r ", [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_null_byte' => ["42\0junk", [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_invisible' => ["42\u{200B}", [IntegerTypeException::class, 'has no valid strict integer value']],

    // --- 6. Non-Numeric Types & Garbage ---
    'str_empty' => ['', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_text_suffix' => ['42 units', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_bool_true' => ['true', [IntegerTypeException::class, 'has no valid strict integer value']],
    'str_neg_inf' => ['-INF', [IntegerTypeException::class, 'has no valid strict integer value']],
]);
