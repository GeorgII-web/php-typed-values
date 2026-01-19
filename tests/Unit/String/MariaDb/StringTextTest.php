<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Alias\MariaDb\Text;
use PhpTypedValues\String\MariaDb\StringText;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts empty string and preserves value/toString', function (): void {
    $s = new StringText('');
    expect($s->value())->toBe('')
        ->and($s->toString())->toBe('')
        ->and((string) $s)->toBe('');
});

it('accepts 65535 ASCII characters (boundary) and preserves value', function (): void {
    $str = str_repeat('x', 65535);
    $s = StringText::fromString($str);
    expect($s->value())->toBe($str)
        ->and($s->toString())->toBe($str);
});

it('throws on 65536 ASCII characters (above boundary)', function (): void {
    $str = str_repeat('y', 65536);
    expect(fn() => new StringText($str))
        ->toThrow(StringTypeException::class, 'String is too long, max 65535 chars allowed');
});

it('accepts 65535 multibyte characters (emoji) counted by mb_strlen', function (): void {
    $str = str_repeat('🙂', 65535);
    $s = new StringText($str);
    expect($s->value())->toBe($str)
        ->and($s->toString())->toBe($str);
});

it('throws on 65536 multibyte characters (emoji)', function (): void {
    $str = str_repeat('🙂', 65536);
    expect(fn() => StringText::fromString($str))
        ->toThrow(StringTypeException::class, 'String is too long, max 65535 chars allowed');
});

it('StringText::tryFromString returns value when length <= 65535 and Undefined when > 65535', function (): void {
    $ok = StringText::tryFromString(str_repeat('a', 10));
    $tooLong = StringText::tryFromString(str_repeat('b', 65536));

    expect($ok)
        ->toBeInstanceOf(StringText::class)
        ->and($ok->value())
        ->toBe(str_repeat('a', 10))
        ->and($tooLong)
        ->toBeInstanceOf(Undefined::class);
});

it('Alias Text behaves the same as StringText', function (): void {
    $alias = Text::fromString('alias');
    expect($alias->value())->toBe('alias');
});

it('jsonSerialize returns string', function (): void {
    expect(Text::tryFromString('hello')->jsonSerialize())->toBeString();
});

it('tryFromMixed handles valid/too-long strings, stringable, and invalid mixed inputs', function (): void {
    // valid within limit
    $ok = StringText::tryFromMixed(str_repeat('a', 10));

    // stringable object within limit
    $str = str_repeat('b', 20);
    $stringable = new class($str) {
        public function __construct(private string $v)
        {
        }

        public function __toString(): string
        {
            return $this->v;
        }
    };
    $fromStringable = StringText::tryFromMixed($stringable);

    // too long (over 65535)
    $tooLong = StringText::tryFromMixed(str_repeat('x', 65536));

    // invalid mixed types
    $fromArray = StringText::tryFromMixed(['x']);
    $fromNull = StringText::tryFromMixed(null);

    expect($ok)->toBeInstanceOf(StringText::class)
        ->and($ok->value())->toBe(str_repeat('a', 10))
        ->and($fromStringable)->toBeInstanceOf(StringText::class)
        ->and($fromStringable->value())->toBe($str)
        ->and($tooLong)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        // null is converted to empty string by convertMixedToString, which is valid for StringText
        ->and($fromNull)->toBeInstanceOf(StringText::class)
        ->and($fromNull->value())->toBe('');
});

it('isEmpty is true for empty and false for non-empty StringText', function (): void {
    $empty = new StringText('');
    $nonEmpty = StringText::fromString('x');

    expect($empty->isEmpty())->toBeTrue()
        ->and($nonEmpty->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Valid instances
    $v1 = new StringText('');
    $v2 = StringText::fromString('short');

    // Invalid via tryFrom*: too long and non-stringable object
    $u1 = StringText::tryFromString(str_repeat('x', 65536));
    $u2 = StringText::tryFromMixed(new stdClass());

    expect($v1->isUndefined())->toBeFalse()
        ->and($v2->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringText::fromString('test');
    expect($v->isTypeOf(StringText::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringText::fromString('test');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringText::fromString('test');
    expect($v->isTypeOf('NonExistentClass', StringText::class, 'AnotherClass'))->toBeTrue();
});

it('covers conversions for StringText', function (): void {
    expect(StringText::fromBool(true)->value())->toBe('true')
        ->and(StringText::fromBool(false)->value())->toBe('false')
        ->and(StringText::fromInt(123)->value())->toBe('123')
        ->and(StringText::fromFloat(1.2)->value())->toBe('1.19999999999999996');

    $vTrue = StringText::fromString('true');
    expect($vTrue->toBool())->toBeTrue();

    $vInt = StringText::fromString('123');
    expect($vInt->toInt())->toBe(123);

    $vFloat = StringText::fromString('1.0');
    expect($vFloat->toFloat())->toBe(1.0);
});

it('tryFromBool, tryFromFloat, tryFromInt return StringText for valid inputs', function (): void {
    expect(StringText::tryFromBool(true))->toBeInstanceOf(StringText::class)
        ->and(StringText::tryFromFloat(1.19999999999999996))->toBeInstanceOf(StringText::class)
        ->and(StringText::tryFromInt(123))->toBeInstanceOf(StringText::class);
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringTextTest extends StringText
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

    public static function fromString(string $value): static
    {
        throw new Exception('test');
    }
}

it('StringText::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
    expect(StringTextTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
        ->and(StringTextTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
        ->and(StringTextTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
        ->and(StringTextTest::tryFromMixed('test'))->toBeInstanceOf(Undefined::class)
        ->and(StringTextTest::tryFromString('test'))->toBeInstanceOf(Undefined::class);
});
