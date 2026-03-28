<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\MariaDb;

use Exception;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Alias\MariaDb\MediumText;
use PhpTypedValues\String\MariaDb\StringMediumText;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringMediumText', function () {
    it('accepts empty string and preserves value/toString', function (): void {
        $s = new StringMediumText('');
        expect($s->value())->toBe('')
            ->and($s->toString())->toBe('')
            ->and((string) $s)->toBe('');
    });

    it('accepts 16777215 ASCII characters (boundary) and preserves value', function (): void {
        $str = str_repeat('x', 16777215);
        $s = StringMediumText::fromString($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('throws on 16777216 ASCII characters (above boundary)', function (): void {
        $str = str_repeat('y', 16777216);
        expect(fn() => new StringMediumText($str))
            ->toThrow(StringTypeException::class, 'String is too long, max 16777215 chars allowed');
    });

    it('accepts 16777215 multibyte characters (emoji) counted by mb_strlen', function (): void {
        // Warning: this might be very slow or memory intensive in tests, but let's follow the pattern.
        // Actually, 16M emojis is ~64MB in UTF-8.
        $str = str_repeat('🙂', 16777215);
        $s = new StringMediumText($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('throws on 16777216 multibyte characters (emoji)', function (): void {
        $str = str_repeat('🙂', 16777216);
        expect(fn() => StringMediumText::fromString($str))
            ->toThrow(StringTypeException::class, 'String is too long, max 16777215 chars allowed');
    });

    it('StringMediumText::tryFromString returns value when length <= 16777215 and Undefined when > 16777215', function (): void {
        $ok = StringMediumText::tryFromString(str_repeat('a', 10));
        $tooLong = StringMediumText::tryFromString(str_repeat('b', 16777216));

        expect($ok)
            ->toBeInstanceOf(StringMediumText::class)
            ->and($ok->value())
            ->toBe(str_repeat('a', 10))
            ->and($tooLong)
            ->toBeInstanceOf(Undefined::class);
    });

    it('Alias MediumText behaves the same as StringMediumText', function (): void {
        $alias = MediumText::fromString('alias');
        expect($alias->value())->toBe('alias');
    });

    it('jsonSerialize returns string', function (): void {
        expect(MediumText::tryFromString('hello')->jsonSerialize())->toBeString();
    });

    it('tryFromMixed handles valid/too-long strings, stringable, and invalid mixed inputs', function (): void {
        // valid within limit
        $ok = StringMediumText::tryFromMixed(str_repeat('a', 10));

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
        $fromStringable = StringMediumText::tryFromMixed($stringable);

        // too long (over 16777215)
        $tooLong = StringMediumText::tryFromMixed(str_repeat('x', 16777216));

        // invalid mixed types
        $fromArray = StringMediumText::tryFromMixed(['x']);
        $fromNull = StringMediumText::tryFromMixed(null);

        expect($ok)->toBeInstanceOf(StringMediumText::class)
            ->and($ok->value())->toBe(str_repeat('a', 10))
            ->and($fromStringable)->toBeInstanceOf(StringMediumText::class)
            ->and($fromStringable->value())->toBe($str)
            ->and($tooLong)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            // null is converted to empty string by convertMixedToString, which is valid for StringMediumText
            ->and($fromNull)->toBeInstanceOf(StringMediumText::class)
            ->and($fromNull->value())->toBe('');
    });

    it('isEmpty is true for empty and false for non-empty StringMediumText', function (): void {
        $empty = new StringMediumText('');
        $nonEmpty = StringMediumText::fromString('x');

        expect($empty->isEmpty())->toBeTrue()
            ->and($nonEmpty->isEmpty())->toBeFalse();
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instances
        $v1 = new StringMediumText('');
        $v2 = StringMediumText::fromString('short');

        // Invalid via tryFrom*: too long and non-stringable object
        $u1 = StringMediumText::tryFromString(str_repeat('x', 16777216));
        $u2 = StringMediumText::tryFromMixed(new stdClass());

        expect($v1->isUndefined())->toBeFalse()
            ->and($v2->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringMediumText::fromString('test');
        expect($v->isTypeOf(StringMediumText::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringMediumText::fromString('test');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringMediumText::fromString('test');
        expect($v->isTypeOf('NonExistentClass', StringMediumText::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringMediumText', function (): void {
        expect(StringMediumText::fromBool(true)->value())->toBe('true')
            ->and(StringMediumText::fromBool(false)->value())->toBe('false')
            ->and(StringMediumText::fromInt(123)->value())->toBe('123')
            ->and(StringMediumText::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(StringMediumText::fromDecimal('1.23')->value())->toBe('1.23');

        $vTrue = StringMediumText::fromString('true');
        expect($vTrue->toBool())->toBeTrue();

        $vInt = StringMediumText::fromString('123');
        expect($vInt->toInt())->toBe(123);

        $vFloat = StringMediumText::fromString('1.0');
        expect($vFloat->toFloat())->toBe(1.0)
            ->and($vFloat->toDecimal())->toBe('1.0');
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return StringMediumText for valid inputs', function (): void {
        expect(StringMediumText::tryFromBool(true))->toBeInstanceOf(StringMediumText::class)
            ->and(StringMediumText::tryFromFloat(1.19999999999999996))->toBeInstanceOf(StringMediumText::class)
            ->and(StringMediumText::tryFromInt(123))->toBeInstanceOf(StringMediumText::class)
            ->and(StringMediumText::tryFromDecimal('1.23'))->toBeInstanceOf(StringMediumText::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringMediumTextTest extends StringMediumText
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromDecimal(string $value): static
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

describe('Throwing static', function () {
    it('StringMediumText::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringMediumTextTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringMediumTextTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringMediumTextTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringMediumTextTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringMediumTextTest::tryFromMixed('test'))->toBeInstanceOf(Undefined::class)
            ->and(StringMediumTextTest::tryFromString('test'))->toBeInstanceOf(Undefined::class);
    });
});
