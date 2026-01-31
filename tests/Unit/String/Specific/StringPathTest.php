<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\PathStringTypeException;
use PhpTypedValues\String\Specific\StringPath;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringPath', function () {
    it('checks valid paths', function (): void {
        expect(StringPath::fromString('/etc/passwd')->value())->toBe('/etc/passwd')
            ->and(StringPath::fromString('etc/passwd')->value())->toBe('etc/passwd')
            ->and(StringPath::fromString('/')->value())->toBe('/')
            ->and(StringPath::fromString('etc')->value())->toBe('etc')
            ->and(StringPath::fromString('etc/')->value())->toBe('etc/')
            ->and(StringPath::fromString('/etc')->value())->toBe('/etc')
            ->and(StringPath::fromString('etc/passwd/')->value())->toBe('etc/passwd/')
            ->and(StringPath::fromString('//etc//passwd//')->value())->toBe('//etc//passwd//')
            ->and(StringPath::fromString('/etc/passwd/')->value())->toBe('/etc/passwd/');
    });

    it('accepts valid path, preserves value/toString and casts via __toString', function (): void {
        $p1 = new StringPath('/src/String');
        $p2 = new StringPath('src\String\\');

        expect($p1->value())
            ->toBe('/src/String')
            ->and($p1->toString())
            ->toBe('/src/String')
            ->and((string) $p1)
            ->toBe('/src/String')
            ->and($p2->value())
            ->toBe('src\String\\')
            ->and($p2->toString())
            ->toBe('src\String\\')
            ->and((string) $p2)
            ->toBe('src\String\\');
    });

    it('throws PathStringTypeException on empty or invalid paths', function (): void {
        expect(fn() => new StringPath(''))
            ->toThrow(PathStringTypeException::class, 'Expected non-empty path')
            ->and(fn() => StringPath::fromString('path/to/file*.txt'))
            ->toThrow(PathStringTypeException::class, 'Expected valid path, got "path/to/file*.txt"')
            ->and(fn() => StringPath::fromString('invalid?path'))
            ->toThrow(PathStringTypeException::class, 'Expected valid path, got "invalid?path"');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = StringPath::tryFromString('/etc/passwd');
        $bad = StringPath::tryFromString('bad?path');

        expect($ok)
            ->toBeInstanceOf(StringPath::class)
            ->and($ok->value())
            ->toBe('/etc/passwd')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringPath::tryFromString('/var/log')->jsonSerialize())->toBeString();
    });

    it('tryFromMixed returns instance for valid paths and Undefined for invalid or non-convertible', function (): void {
        $fromString = StringPath::tryFromMixed('/home/user');
        $fromStringable = StringPath::tryFromMixed(new class implements Stringable {
            public function __toString(): string
            {
                return 'src\String';
            }
        });
        $fromInvalidType = StringPath::tryFromMixed([]);
        $fromInvalidValue = StringPath::tryFromMixed('path*');
        $fromNull = StringPath::tryFromMixed(null);
        $fromObject = StringPath::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringPath::class)
            ->and($fromString->value())
            ->toBe('/home/user')
            ->and($fromStringable)
            ->toBeInstanceOf(StringPath::class)
            ->and($fromStringable->value())
            ->toBe('src\String')
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringPath', function (): void {
        $p = new StringPath('/tmp');
        expect($p->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringPath', function (): void {
        $p = new StringPath('/tmp');
        expect($p->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringPath::fromString('/tmp');
        expect($v->isTypeOf(StringPath::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringPath::fromString('/tmp');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('covers conversions for StringPath', function (): void {
        expect(StringPath::fromBool(true)->value())->toBe('true')
            ->and(StringPath::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(StringPath::fromInt(123)->value())->toBe('123')
            ->and(StringPath::fromDecimal('1.23')->value())->toBe('1.23');

        $v = StringPath::fromString('/home/user');
        expect(fn() => $v->toBool())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and($v->toDecimal())->toBe('/home/user');
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return StringPath for valid inputs', function (): void {
        expect(StringPath::tryFromBool(true))->toBeInstanceOf(StringPath::class)
            ->and(StringPath::tryFromFloat(1.2))->toBeInstanceOf(StringPath::class)
            ->and(StringPath::tryFromInt(123))->toBeInstanceOf(StringPath::class)
            ->and(StringPath::tryFromDecimal('1.23'))->toBeInstanceOf(StringPath::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringPathTest extends StringPath
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
    it('StringPath::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringPathTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringPathTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringPathTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringPathTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringPathTest::tryFromMixed('/home/user'))->toBeInstanceOf(Undefined::class)
            ->and(StringPathTest::tryFromString('/home/user'))->toBeInstanceOf(Undefined::class);
    });
});
