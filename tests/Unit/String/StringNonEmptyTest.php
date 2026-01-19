<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

it('StringNonEmpty::tryFromString returns value for non-empty string', function (): void {
    $v = StringNonEmpty::tryFromString('abc');

    expect($v)
        ->toBeInstanceOf(StringNonEmpty::class)
        ->and($v->value())
        ->toBe('abc');
});

it('StringNonEmpty::tryFromString returns Undefined for empty string', function (): void {
    $u = StringNonEmpty::tryFromString('');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('constructs and preserves non-empty string', function (): void {
    $s = new StringNonEmpty('hello');
    expect($s->value())->toBe('hello')
        ->and($s->toString())->toBe('hello');
});

it('allows whitespace and unicode as non-empty', function (): void {
    $w = new StringNonEmpty(' ');
    $u = StringNonEmpty::fromString('🙂');
    expect($w->value())->toBe(' ')
        ->and($u->toString())->toBe('🙂');
});

it('throws on empty string via constructor', function (): void {
    expect(fn() => new StringNonEmpty(''))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('throws on empty string via fromString', function (): void {
    expect(fn() => StringNonEmpty::fromString(''))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('jsonSerialize returns string', function (): void {
    expect(StringNonEmpty::tryFromString('hello')->jsonSerialize())->toBeString();
});

it('__toString returns the original non-empty string', function (): void {
    $s = new StringNonEmpty('world');
    expect((string) $s)->toBe('world')
        ->and($s->__toString())->toBe('world');
});

it('isEmpty is always false for StringNonEmpty', function (): void {
    $s = new StringNonEmpty('x');
    expect($s->isEmpty())->toBeFalse();
});

it('isUndefined is always false for StringNonEmpty', function (): void {
    $s = new StringNonEmpty('x');
    expect($s->isUndefined())->toBeFalse();
});

it('tryFromMixed handles various inputs for StringNonEmpty', function (): void {
    $fromString = StringNonEmpty::tryFromMixed('hello');
    $fromInt = StringNonEmpty::tryFromMixed(123);
    $fromNull = StringNonEmpty::tryFromMixed(null);
    $fromArray = StringNonEmpty::tryFromMixed(['x']);
    $fromObject = StringNonEmpty::tryFromMixed(new stdClass());

    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'stringable-content';
        }
    };
    $fromStringable = StringNonEmpty::tryFromMixed($stringable);

    expect($fromString)->toBeInstanceOf(StringNonEmpty::class)
        ->and($fromString->value())->toBe('hello')
        ->and($fromInt)->toBeInstanceOf(StringNonEmpty::class)
        ->and($fromInt->value())->toBe('123')
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromObject)->toBeInstanceOf(Undefined::class)
        ->and($fromStringable)->toBeInstanceOf(StringNonEmpty::class)
        ->and($fromStringable->value())->toBe('stringable-content');
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringNonEmpty::fromString('test');
    expect($v->isTypeOf(StringNonEmpty::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringNonEmpty::fromString('test');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringNonEmpty::fromString('test');
    expect($v->isTypeOf('NonExistentClass', StringNonEmpty::class, 'AnotherClass'))->toBeTrue();
});

it('covers conversions for StringNonEmpty', function (): void {
    expect(StringNonEmpty::fromBool(true)->value())->toBe('true')
        ->and(StringNonEmpty::fromBool(false)->value())->toBe('false')
        ->and(StringNonEmpty::fromInt(123)->value())->toBe('123')
        ->and(StringNonEmpty::fromFloat(1.2)->value())->toBe('1.19999999999999996');

    $vTrue = StringNonEmpty::fromString('true');
    expect($vTrue->toBool())->toBeTrue();

    $vInt = StringNonEmpty::fromString('123');
    expect($vInt->toInt())->toBe(123);

    $vFloat = StringNonEmpty::fromString('1.19999999999999996');
    expect($vFloat->toFloat())->toBe(1.2);
});

it('tryFromBool, tryFromFloat, tryFromInt return StringNonEmpty for valid inputs', function (): void {
    expect(StringNonEmpty::tryFromBool(true))->toBeInstanceOf(StringNonEmpty::class)
        ->and(StringNonEmpty::tryFromFloat(1.19999999999999996))->toBeInstanceOf(StringNonEmpty::class)
        ->and(StringNonEmpty::tryFromInt(123))->toBeInstanceOf(StringNonEmpty::class);
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringNonEmptyTest extends StringNonEmpty
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('test');
    }
}

it('StringNonEmpty::tryFromBool returns Undefined when fromBool throws (coverage)', function (): void {
    expect(StringNonEmptyTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
});

it('StringNonEmpty::tryFromFloat returns Undefined when fromFloat throws (coverage)', function (): void {
    expect(StringNonEmptyTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class);
});

it('StringNonEmpty::tryFromInt returns Undefined when fromInt throws (coverage)', function (): void {
    expect(StringNonEmptyTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
});
