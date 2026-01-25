<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\MariaDb\StringVarChar255;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringVarChar255', function () {
    it('accepts empty string and preserves value', function (): void {
        $s = new StringVarChar255('');
        expect($s->value())->toBe('')
            ->and($s->toString())->toBe('');
    });

    it('accepts 255 ASCII characters (boundary) and preserves value', function (): void {
        $str = str_repeat('a', 255);
        $s = StringVarChar255::fromString($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('throws on 256 ASCII characters (above boundary)', function (): void {
        $str = str_repeat('b', 256);
        expect(fn() => new StringVarChar255($str))
            ->toThrow(StringTypeException::class, 'String is too long, max 255 chars allowed');
    });

    it('accepts 255 multibyte characters (emoji) counted by mb_strlen', function (): void {
        $str = str_repeat('🙂', 255);
        $s = new StringVarChar255($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('throws on 256 multibyte characters (emoji)', function (): void {
        $str = str_repeat('🙂', 256);
        expect(fn() => StringVarChar255::fromString($str))
            ->toThrow(StringTypeException::class, 'String is too long, max 255 chars allowed');
    });

    it('StringVarChar255::tryFromString returns value when length <= 255', function (): void {
        $short = str_repeat('a', 255);
        $v = StringVarChar255::tryFromString($short);

        expect($v)
            ->toBeInstanceOf(StringVarChar255::class)
            ->and($v->value())
            ->toBe($short);
    });

    it('StringVarChar255::tryFromString returns Undefined when length > 255', function (): void {
        $long = str_repeat('b', 256);
        $u = StringVarChar255::tryFromString($long);

        expect($u)->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringVarChar255::tryFromString('hello')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original string value', function (): void {
        $str = str_repeat('C', 10);
        $s = new StringVarChar255($str);

        expect((string) $s)->toBe($str)
            ->and($s->__toString())->toBe($str);
    });

    it('tryFromMixed handles valid/too-long strings, stringable, and invalid mixed inputs', function (): void {
        // valid within limit
        $ok = StringVarChar255::tryFromMixed(str_repeat('a', 255));

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
        $fromStringable = StringVarChar255::tryFromMixed($stringable);

        // too long
        $tooLong = StringVarChar255::tryFromMixed(str_repeat('x', 256));

        // invalid mixed
        $fromArray = StringVarChar255::tryFromMixed(['x']);
        $fromNull = StringVarChar255::tryFromMixed(null);

        expect($ok)->toBeInstanceOf(StringVarChar255::class)
            ->and($ok->value())->toBe(str_repeat('a', 255))
            ->and($fromStringable)->toBeInstanceOf(StringVarChar255::class)
            ->and($fromStringable->value())->toBe($val)
            ->and($tooLong)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            // null converts to empty string which is valid for VarChar(255)
            ->and($fromNull)->toBeInstanceOf(StringVarChar255::class)
            ->and($fromNull->value())->toBe('');
    });

    it('isEmpty is true for empty and false for non-empty StringVarChar255', function (): void {
        $empty = new StringVarChar255('');
        $nonEmpty = StringVarChar255::fromString('x');

        expect($empty->isEmpty())->toBeTrue()
            ->and($nonEmpty->isEmpty())->toBeFalse();
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instances
        $v1 = new StringVarChar255('');
        $v2 = StringVarChar255::fromString('ok');

        // Invalid via tryFrom*: too long and non-string mixed
        $u1 = StringVarChar255::tryFromString(str_repeat('x', 256));
        $u2 = StringVarChar255::tryFromMixed(['x']);

        expect($v1->isUndefined())->toBeFalse()
            ->and($v2->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringVarChar255::fromString('test');
        expect($v->isTypeOf(StringVarChar255::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringVarChar255::fromString('test');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringVarChar255::fromString('test');
        expect($v->isTypeOf('NonExistentClass', StringVarChar255::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringVarChar255', function (): void {
        expect(StringVarChar255::fromBool(true)->value())->toBe('true')
            ->and(StringVarChar255::fromBool(false)->value())->toBe('false')
            ->and(StringVarChar255::fromInt(123)->value())->toBe('123')
            ->and(StringVarChar255::fromFloat(1.2)->value())->toBe('1.19999999999999996');

        $vTrue = StringVarChar255::fromString('true');
        expect($vTrue->toBool())->toBeTrue();

        $vInt = StringVarChar255::fromString('123');
        expect($vInt->toInt())->toBe(123);

        $vFloat = StringVarChar255::fromString('1.19999999999999996');
        expect($vFloat->toFloat())->toBe(1.2);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return StringVarChar255 for valid inputs', function (): void {
        expect(StringVarChar255::tryFromBool(true))->toBeInstanceOf(StringVarChar255::class)
            ->and(StringVarChar255::tryFromFloat(1.19999999999999996))->toBeInstanceOf(StringVarChar255::class)
            ->and(StringVarChar255::tryFromInt(123))->toBeInstanceOf(StringVarChar255::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringVarChar255Test extends StringVarChar255
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

it('StringVarChar255::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
    expect(StringVarChar255Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
        ->and(StringVarChar255Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
        ->and(StringVarChar255Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
        ->and(StringVarChar255Test::tryFromMixed('test'))->toBeInstanceOf(Undefined::class)
        ->and(StringVarChar255Test::tryFromString('test'))->toBeInstanceOf(Undefined::class);
});
