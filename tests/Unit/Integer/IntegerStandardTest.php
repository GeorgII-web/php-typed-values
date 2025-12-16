<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
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
        ->and($n->toString())->toBe('-10')
        ->and((string) $n)->toBe('-10')
        ->and($p->value())->toBe(42)
        ->and($p->toString())->toBe('42');
});

it('fromString uses strict integer parsing', function (): void {
    expect(IntegerStandard::fromString('-5')->value())->toBe(-5)
        ->and(IntegerStandard::fromString('0')->value())->toBe(0)
        ->and(IntegerStandard::fromString('17')->toString())->toBe('17');

    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerStandard::fromString($bad))
            ->toThrow(IntegerTypeException::class, \sprintf('String "%s" has no valid strict integer value', $bad));
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
