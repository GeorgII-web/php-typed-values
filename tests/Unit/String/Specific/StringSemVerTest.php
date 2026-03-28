<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\SemVerStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringSemVer;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringSemVer', function () {
    it('accepts valid SemVer strings and preserves value', function (): void {
        $simple = new StringSemVer('1.2.3');
        $withPre = new StringSemVer('1.0.0-alpha');
        $withBuild = new StringSemVer('1.0.0+build.123');
        $withPreAndBuild = new StringSemVer('1.0.0-beta.1+build.456');

        expect($simple->value())
            ->toBe('1.2.3')
            ->and($withPre->value())
            ->toBe('1.0.0-alpha')
            ->and($withBuild->value())
            ->toBe('1.0.0+build.123')
            ->and($withPreAndBuild->value())
            ->toBe('1.0.0-beta.1+build.456');
    });

    it('accepts edge-case valid SemVer strings', function (string $valid): void {
        $v = StringSemVer::fromString($valid);
        expect($v->value())->toBe($valid);
    })->with([
        '0.0.0',
        '0.0.1',
        '10.20.30',
        '1.0.0-alpha.1',
        '1.0.0-0.3.7',
        '1.0.0-x.7.z.92',
        '1.0.0+20130313144700',
        '1.0.0-beta+exp.sha.5114f85',
        '1.1.2-prerelease+meta',
        '99999999999.99999999999.99999999999',
    ]);

    it('throws on invalid SemVer format', function (): void {
        expect(fn() => new StringSemVer('not valid!'))
            ->toThrow(SemVerStringTypeException::class, 'Expected SemVer string, got "not valid!"');

        expect(fn() => StringSemVer::fromString(''))
            ->toThrow(SemVerStringTypeException::class);

        expect(fn() => StringSemVer::fromString('1.2'))
            ->toThrow(SemVerStringTypeException::class);

        expect(fn() => StringSemVer::fromString('v1.2.3'))
            ->toThrow(SemVerStringTypeException::class);
    });

    it('rejects strings with invalid SemVer formats', function (string $invalid): void {
        expect(fn() => StringSemVer::fromString($invalid))
            ->toThrow(SemVerStringTypeException::class);
    })->with([
        '1',
        '1.2',
        '1.2.3.4',
        'v1.2.3',
        '01.2.3',
        '1.02.3',
        '1.2.03',
        '1.2.3-',
        '1.2.3+',
        '1.2.3-01',
        '.1.2.3',
        '1.2.3 ',
        ' 1.2.3',
    ]);

    it('tryFromString returns instance for valid SemVer and Undefined for invalid', function (): void {
        $ok = StringSemVer::tryFromString('1.2.3');
        $bad1 = StringSemVer::tryFromString('not valid!');
        $bad2 = StringSemVer::tryFromString('');

        expect($ok)
            ->toBeInstanceOf(StringSemVer::class)
            ->and($ok->value())
            ->toBe('1.2.3')
            ->and($bad1)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad2)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid SemVer strings and invalid mixed inputs', function (): void {
        $ok = StringSemVer::tryFromMixed('1.2.3');

        $stringable = new class {
            public function __toString(): string
            {
                return '2.0.0';
            }
        };
        $fromStringable = StringSemVer::tryFromMixed($stringable);

        $badFormat = StringSemVer::tryFromMixed('not valid!');
        $fromArray = StringSemVer::tryFromMixed(['1.2.3']);
        $fromNull = StringSemVer::tryFromMixed(null);
        $fromScalar = StringSemVer::tryFromMixed(123);
        $fromObject = StringSemVer::tryFromMixed(new stdClass());
        $fromBool = StringSemVer::tryFromMixed(true);
        $fromFloat = StringSemVer::tryFromMixed(1.5);

        expect($ok)->toBeInstanceOf(StringSemVer::class)
            ->and($ok->value())->toBe('1.2.3')
            ->and($fromStringable)->toBeInstanceOf(StringSemVer::class)
            ->and($fromStringable->value())->toBe('2.0.0')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromScalar)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class)
            ->and($fromBool)->toBeInstanceOf(Undefined::class)
            ->and($fromFloat)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringSemVer::fromString('1.2.3');
        $u1 = StringSemVer::tryFromString('not valid!');
        $u2 = StringSemVer::tryFromMixed(['1.2.3']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringSemVer::fromString('1.2.3');
        expect($v->isTypeOf(StringSemVer::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringSemVer::fromString('1.2.3');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for StringSemVer', function (): void {
        $v = StringSemVer::fromString('1.2.3');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringSemVer::fromString('1.2.3');
        expect($v->jsonSerialize())->toBe('1.2.3');
    });

    it('toString returns the SemVer string', function (): void {
        $v = StringSemVer::fromString('1.2.3');
        expect($v->toString())->toBe('1.2.3');
    });

    it('__toString returns the value', function (): void {
        $v = StringSemVer::fromString('1.2.3');
        expect((string) $v)->toBe('1.2.3');
    });

    it('covers conversions for StringSemVer', function (): void {
        expect(fn() => StringSemVer::fromBool(true))->toThrow(SemVerStringTypeException::class)
            ->and(fn() => StringSemVer::fromBool(false))->toThrow(SemVerStringTypeException::class)
            ->and(fn() => StringSemVer::fromFloat(1.2))->toThrow(SemVerStringTypeException::class)
            ->and(fn() => StringSemVer::fromInt(123))->toThrow(SemVerStringTypeException::class)
            ->and(fn() => StringSemVer::fromDecimal('1.0'))->toThrow(SemVerStringTypeException::class);

        $v = StringSemVer::fromString('1.2.3');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal for StringSemVer', function (): void {
        expect(StringSemVer::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVer::tryFromBool(false))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVer::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVer::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVer::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringSemVerTest extends StringSemVer
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
    it('StringSemVer::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringSemVerTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVerTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVerTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVerTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVerTest::tryFromMixed('1.2.3'))->toBeInstanceOf(Undefined::class)
            ->and(StringSemVerTest::tryFromString('1.2.3'))->toBeInstanceOf(Undefined::class);
    });
});
