<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\String\UrlPathStringTypeException;
use PhpTypedValues\String\Specific\StringUrlPath;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(StringUrlPath::class);

describe('StringUrlPath', function () {
    it('accepts valid URL path, preserves value/toString and casts via __toString', function (string $value): void {
        $p = new StringUrlPath($value);

        expect($p->value())
            ->toBe($value)
            ->and($p->toString())
            ->toBe($value)
            ->and((string) $p)
            ->toBe($value);
    })->with([
        ['/'],
        ['/path'],
        ['/path/to/resource'],
        ['/path/to/resource/file.jpg'],
        ['/path/to/resource/file%20name.jpg'],
        ['/path/to/resource/file+name.jpg'],
        ['relative/path'],
        ['/path-with-dashes_and.dots~'],
        ['/path:with:colons@and-at-symbols'],
        ['!$&\'()*+,;='],
    ]);

    it('throws UrlPathStringTypeException on empty or invalid URL paths', function (string $invalidValue, string $message): void {
        expect(fn() => new StringUrlPath($invalidValue))
            ->toThrow(UrlPathStringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty URL path'],
        ['/path with space', 'Expected valid URL path'],
        ['/path?query', 'Expected valid URL path'],
        ['/path#fragment', 'Expected valid URL path'],
        ['/path%GG', 'Expected valid URL path with correct percent-encoding'],
        ["/path\x01", 'Expected valid URL path'],
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = StringUrlPath::tryFromString('/valid/path');
        $bad = StringUrlPath::tryFromString('/invalid path');

        expect($ok)
            ->toBeInstanceOf(StringUrlPath::class)
            ->and($ok->value())
            ->toBe('/valid/path')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringUrlPath::tryFromString('/test')->jsonSerialize())->toBe('/test');
    });

    it('tryFromMixed returns instance for valid URL path strings', function (): void {
        $fromString = StringUrlPath::tryFromMixed('/example');
        $fromStringable = StringUrlPath::tryFromMixed(new class {
            public function __toString(): string
            {
                return '/test/path';
            }
        });

        expect($fromString)
            ->toBeInstanceOf(StringUrlPath::class)
            ->and($fromString->value())
            ->toBe('/example')
            ->and($fromStringable)
            ->toBeInstanceOf(StringUrlPath::class)
            ->and($fromStringable->value())
            ->toBe('/test/path');
    });

    it('tryFromMixed returns Undefined for invalid or non-convertible values', function (): void {
        $fromInvalidPath = StringUrlPath::tryFromMixed('/invalid path');
        $fromArray = StringUrlPath::tryFromMixed([]);
        $fromObject = StringUrlPath::tryFromMixed(new stdClass());
        $fromNull = StringUrlPath::tryFromMixed(null);

        expect($fromInvalidPath)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromArray)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class);
    });

    it('fromString creates instance with correct value', function (): void {
        $path = StringUrlPath::fromString('/path');

        expect($path)
            ->toBeInstanceOf(StringUrlPath::class)
            ->and($path->value())
            ->toBe('/path');
    });

    it('isEmpty is always false for StringUrlPath', function (): void {
        $p = new StringUrlPath('/test');
        expect($p->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringUrlPath', function (): void {
        $p = new StringUrlPath('/test');
        expect($p->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringUrlPath::fromString('/test');
        expect($v->isTypeOf(StringUrlPath::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringUrlPath::fromString('/test');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('covers conversions for StringUrlPath', function (): void {
        expect(StringUrlPath::fromBool(true)->value())->toBe('true')
            ->and(StringUrlPath::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(StringUrlPath::fromInt(123)->value())->toBe('123')
            ->and(StringUrlPath::fromDecimal('1.0')->value())->toBe('1.0');

        $v = StringUrlPath::fromString('/test');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return instances for StringUrlPath', function (): void {
        expect(StringUrlPath::tryFromBool(true))->toBeInstanceOf(StringUrlPath::class)
            ->and(StringUrlPath::tryFromFloat(1.2))->toBeInstanceOf(StringUrlPath::class)
            ->and(StringUrlPath::tryFromInt(123))->toBeInstanceOf(StringUrlPath::class)
            ->and(StringUrlPath::tryFromDecimal('1.0'))->toBeInstanceOf(StringUrlPath::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringUrlPathTest extends StringUrlPath
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
    it('StringUrlPath::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringUrlPathTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringUrlPathTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringUrlPathTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringUrlPathTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringUrlPathTest::tryFromMixed('/test'))->toBeInstanceOf(Undefined::class)
            ->and(StringUrlPathTest::tryFromString('/test'))->toBeInstanceOf(Undefined::class);
    });
});
