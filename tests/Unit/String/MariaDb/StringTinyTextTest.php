<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\MariaDb;

use Exception;
use PhpTypedValues\Exception\String\TinyTextStringException;
use PhpTypedValues\String\MariaDb\StringTinyText;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StringTinyText::class);

describe('StringTinyText', function () {
    it('accepts empty string and preserves value', function (): void {
        $s = new StringTinyText('');
        expect($s->value())->toBe('')
            ->and($s->toString())->toBe('');
    });

    it('accepts 255 ASCII characters (boundary) and preserves value', function (): void {
        $str = str_repeat('a', 255);
        $s = StringTinyText::fromString($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('throws on 256 ASCII characters (above boundary)', function (): void {
        $str = str_repeat('b', 256);
        expect(fn() => new StringTinyText($str))
            ->toThrow(TinyTextStringException::class, 'String is too long, max 255 chars allowed');
    });

    it('accepts 255 multibyte characters (emoji) counted by mb_strlen', function (): void {
        $str = str_repeat('🙂', 255);
        $s = new StringTinyText($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('throws on 256 multibyte characters (emoji)', function (): void {
        $str = str_repeat('🙂', 256);
        expect(fn() => StringTinyText::fromString($str))
            ->toThrow(TinyTextStringException::class, 'String is too long, max 255 chars allowed');
    });

    it('StringTinyText::tryFromString returns value when length <= 255', function (): void {
        $short = str_repeat('a', 255);
        $v = StringTinyText::tryFromString($short);

        expect($v)
            ->toBeInstanceOf(StringTinyText::class)
            ->and($v->value())
            ->toBe($short);
    });

    it('StringTinyText::tryFromString returns Undefined when length > 255', function (): void {
        $long = str_repeat('b', 256);
        $u = StringTinyText::tryFromString($long);

        expect($u)->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringTinyText::tryFromString('hello')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original string value', function (): void {
        $str = str_repeat('C', 10);
        $s = new StringTinyText($str);

        expect((string) $s)->toBe($str)
            ->and($s->__toString())->toBe($str);
    });

    it('tryFromMixed handles valid/too-long strings, stringable, and invalid mixed inputs', function (): void {
        // valid within limit
        $ok = StringTinyText::tryFromMixed(str_repeat('a', 255));

        // stringable within limit
        $val = str_repeat('b', 5);
        $stringable = new class($val) {
            public function __construct(private string $v)
            {
            }

            public function __toString(): string
            {
                return $this->v;
            }
        };
        $fromStringable = StringTinyText::tryFromMixed($stringable);

        // too long
        $tooLong = StringTinyText::tryFromMixed(str_repeat('x', 256));

        // invalid mixed
        $fromArray = StringTinyText::tryFromMixed(['x']);
        $fromNull = StringTinyText::tryFromMixed(null);

        expect($ok)->toBeInstanceOf(StringTinyText::class)
            ->and($ok->value())->toBe(str_repeat('a', 255))
            ->and($fromStringable)->toBeInstanceOf(StringTinyText::class)
            ->and($fromStringable->value())->toBe($val)
            ->and($tooLong)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            // null converts to empty string which is valid for TinyText
            ->and($fromNull)->toBeInstanceOf(StringTinyText::class)
            ->and($fromNull->value())->toBe('');
    });

    it('isEmpty is true for empty and false for non-empty StringTinyText', function (): void {
        $empty = new StringTinyText('');
        $nonEmpty = StringTinyText::fromString('x');

        expect($empty->isEmpty())->toBeTrue()
            ->and($nonEmpty->isEmpty())->toBeFalse();
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instances
        $v1 = new StringTinyText('');
        $v2 = StringTinyText::fromString('ok');

        // Invalid via tryFrom*: too long and non-string mixed
        $u1 = StringTinyText::tryFromString(str_repeat('x', 256));
        $u2 = StringTinyText::tryFromMixed(['x']);

        expect($v1->isUndefined())->toBeFalse()
            ->and($v2->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringTinyText::fromString('test');
        expect($v->isTypeOf(StringTinyText::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringTinyText::fromString('test');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringTinyText::fromString('test');
        expect($v->isTypeOf('NonExistentClass', StringTinyText::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringTinyText', function (): void {
        expect(StringTinyText::fromBool(true)->value())->toBe('true')
            ->and(StringTinyText::fromBool(false)->value())->toBe('false')
            ->and(StringTinyText::fromInt(123)->value())->toBe('123')
            ->and(StringTinyText::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(StringTinyText::fromDecimal('1.23')->value())->toBe('1.23');

        $vTrue = StringTinyText::fromString('true');
        expect($vTrue->toBool())->toBeTrue();

        $vInt = StringTinyText::fromString('123');
        expect($vInt->toInt())->toBe(123);

        $vFloat = StringTinyText::fromString('1.19999999999999996');
        expect($vFloat->toFloat())->toBe(1.2)
            ->and($vFloat->toDecimal())->toBe('1.19999999999999996');
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return StringTinyText for valid inputs', function (): void {
        expect(StringTinyText::tryFromBool(true))->toBeInstanceOf(StringTinyText::class)
            ->and(StringTinyText::tryFromFloat(1.19999999999999996))->toBeInstanceOf(StringTinyText::class)
            ->and(StringTinyText::tryFromInt(123))->toBeInstanceOf(StringTinyText::class)
            ->and(StringTinyText::tryFromDecimal('1.23'))->toBeInstanceOf(StringTinyText::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringTinyTextTest extends StringTinyText
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
    it('StringTinyText::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringTinyTextTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringTinyTextTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringTinyTextTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringTinyTextTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringTinyTextTest::tryFromMixed('test'))->toBeInstanceOf(Undefined::class)
            ->and(StringTinyTextTest::tryFromString('test'))->toBeInstanceOf(Undefined::class);
    });
});
