<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\MariaDb;

use const DEBUG_BACKTRACE_IGNORE_ARGS;

use Exception;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\MariaDb\StringLongText;
use PhpTypedValues\Undefined\Alias\Undefined;
use ReflectionMethod;

covers(StringLongText::class);

describe('StringLongText', function () {
    it('accepts empty string and preserves value/toString', function (): void {
        $s = new StringLongText('');
        expect($s->value())->toBe('')
            ->and($s->toString())->toBe('')
            ->and((string) $s)->toBe('');
    });

    it('accepts characters and preserves value', function (): void {
        $str = 'lorem ipsum';
        $s = StringLongText::fromString($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('accepts multibyte characters (emoji) counted by mb_strlen', function (): void {
        $str = '🙂';
        $s = new StringLongText($str);
        expect($s->value())->toBe($str)
            ->and($s->toString())->toBe($str);
    });

    it('StringLongText::tryFromString returns value for valid string', function (): void {
        $ok = StringLongText::tryFromString('valid');

        expect($ok)
            ->toBeInstanceOf(StringLongText::class)
            ->and($ok->value())
            ->toBe('valid');
    });

    it('Alias LongText behaves the same as StringLongText', function (): void {
        $alias = StringLongText::fromString('alias');
        expect($alias->value())->toBe('alias');
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringLongText::tryFromString('hello')->jsonSerialize())->toBeString();
    });

    it('tryFromMixed handles valid strings, stringable, and invalid mixed inputs', function (): void {
        // valid
        $ok = StringLongText::tryFromMixed('test');

        // stringable object
        $stringable = new class('stringable') {
            public function __construct(private string $v)
            {
            }

            public function __toString(): string
            {
                return $this->v;
            }
        };
        $fromStringable = StringLongText::tryFromMixed($stringable);

        // invalid mixed types
        $fromArray = StringLongText::tryFromMixed(['x']);
        $fromNull = StringLongText::tryFromMixed(null);

        expect($ok)->toBeInstanceOf(StringLongText::class)
            ->and($ok->value())->toBe('test')
            ->and($fromStringable)->toBeInstanceOf(StringLongText::class)
            ->and($fromStringable->value())->toBe('stringable')
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(StringLongText::class)
            ->and($fromNull->value())->toBe('');
    });

    it('isEmpty is true for empty and false for non-empty StringLongText', function (): void {
        $empty = new StringLongText('');
        $nonEmpty = StringLongText::fromString('x');

        expect($empty->isEmpty())->toBeTrue()
            ->and($nonEmpty->isEmpty())->toBeFalse();
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instances
        $v1 = new StringLongText('');
        $v2 = StringLongText::fromString('test');

        // Invalid via tryFrom* (non-stringable object)
        $u = StringLongText::tryFromMixed(new class {});

        expect($v1->isUndefined())->toBeFalse()
            ->and($v2->isUndefined())->toBeFalse()
            ->and($u->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringLongText::fromString('test');
        expect($v->isTypeOf(StringLongText::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringLongText::fromString('test');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringLongText::fromString('test');
        expect($v->isTypeOf('NonExistentClass', StringLongText::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringLongText', function (): void {
        expect(StringLongText::fromBool(true)->value())->toBe('true')
            ->and(StringLongText::fromBool(false)->value())->toBe('false')
            ->and(StringLongText::fromInt(123)->value())->toBe('123')
            ->and(StringLongText::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(StringLongText::fromDecimal('1.23')->value())->toBe('1.23');

        $vTrue = StringLongText::fromString('true');
        expect($vTrue->toBool())->toBeTrue();

        $vInt = StringLongText::fromString('123');
        expect($vInt->toInt())->toBe(123);

        $vFloat = StringLongText::fromString('1.0');
        expect($vFloat->toFloat())->toBe(1.0)
            ->and($vFloat->toDecimal())->toBe('1.0');
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return StringLongText for valid inputs', function (): void {
        expect(StringLongText::tryFromBool(true))->toBeInstanceOf(StringLongText::class)
            ->and(StringLongText::tryFromFloat(1.19999999999999996))->toBeInstanceOf(StringLongText::class)
            ->and(StringLongText::tryFromInt(123))->toBeInstanceOf(StringLongText::class)
            ->and(StringLongText::tryFromDecimal('1.23'))->toBeInstanceOf(StringLongText::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringLongTextTest extends StringLongText
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('Trigger fromBool');
    }

    public static function fromDecimal(string $value): static
    {
        throw new Exception('Trigger fromDecimal');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('Trigger fromFloat');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('Trigger fromInt');
    }

    public static function fromString(string $value): static
    {
        if ($value === '') {
            return new self('marker-empty');
        }

        if ($value === 'trigger-exception') {
            throw new Exception('Trigger fromString');
        }

        return new self('generic');
    }

    protected static function maxLength(): int
    {
        // Special case for testing line 46 in StringLongText.php
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            $function = $trace['function'] ?? '';
            if (
                str_contains($function, 'it_throws_when_string_exceeds_max_length')
                || str_contains($function, 'it_accepts_string_of_exact_max_length')
            ) {
                return 5;
            }
        }

        return 4294967295;
    }
}

describe('Coverage for mutants', function () {
    it('tryFromMixed specifically triggers fromString(\"\") for null value', function (): void {
        $result = StringLongTextTest::tryFromMixed(null);
        expect($result)->toBeInstanceOf(StringLongText::class)
            ->and($result->value())->toBe('marker-empty');
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        $result = StringLongTextTest::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });

    it('tryFrom* methods return default on exception', function (): void {
        $default = new Undefined();

        expect(StringLongTextTest::tryFromBool(true, $default))->toBe($default)
            ->and(StringLongTextTest::tryFromDecimal('1.23', $default))->toBe($default)
            ->and(StringLongTextTest::tryFromFloat(1.23, $default))->toBe($default)
            ->and(StringLongTextTest::tryFromInt(123, $default))->toBe($default)
            ->and(StringLongTextTest::tryFromString('trigger-exception', $default))->toBe($default);
    });

    it('it throws when string exceeds max length', function (): void {
        expect(fn() => new StringLongTextTest('too-long'))
            ->toThrow(StringTypeException::class, 'String is too long, max 5 chars allowed');
    });

    it('it accepts string of exact max length', function (): void {
        expect(new StringLongTextTest('abcde'))->toBeInstanceOf(StringLongText::class)
            ->and(new StringLongTextTest('abcde')->value())->toBe('abcde');
    });

    it('maxLength() specifically returns 4294967295', function (): void {
        $reflection = new ReflectionMethod(StringLongText::class, 'maxLength');
        $reflection->setAccessible(true);

        expect($reflection->invoke(null))->toBe(4294967295);
    });
});

describe('Null checks', function () {
    it('throws exception on fromNull', function () {
        expect(fn() => StringLongText::fromNull(null))
            ->toThrow(StringTypeException::class);
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringLongText::toNull())
            ->toThrow(StringTypeException::class);
    });
});
