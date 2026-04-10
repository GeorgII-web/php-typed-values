<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\MimeTypeStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringMimeType;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StringMimeType::class);

describe('StringMimeType', function () {
    it('accepts valid MIME types, preserves value/toString and casts via __toString', function (string $value): void {
        $m = new StringMimeType($value);

        expect($m->value())
            ->toBe($value)
            ->and($m->toString())
            ->toBe($value)
            ->and((string) $m)
            ->toBe($value);
    })->with([
        'application/json',
        'image/png',
        'text/plain',
        'application/vnd.api+json',
        'audio/mpeg',
    ]);

    it('throws MimeTypeStringTypeException on empty or invalid MIME types', function (string $value, string $message): void {
        expect(fn() => new StringMimeType($value))
            ->toThrow(MimeTypeStringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty MIME type'],
        ['application', 'Expected valid MIME type, got "application"'],
        ['/json', 'Expected valid MIME type, got "/json"'],
        ['application/', 'Expected valid MIME type, got "application/"'],
        ['app/json/extra', 'Expected valid MIME type, got "app/json/extra"'],
    ]);

    it('correctly extracts type and subtype', function (): void {
        $m = new StringMimeType('application/json');

        expect($m->getType())->toBe('application')
            ->and($m->getSubtype())->toBe('json');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = StringMimeType::tryFromString('application/json');
        $bad = StringMimeType::tryFromString('invalid-mime');

        expect($ok)
            ->toBeInstanceOf(StringMimeType::class)
            ->and($ok->value())
            ->toBe('application/json')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringMimeType::fromString('application/json')->jsonSerialize())->toBe('application/json');
    });

    it('tryFromMixed returns instance for valid MIME types and Undefined for invalid or non-convertible', function (): void {
        $fromString = StringMimeType::tryFromMixed('image/png');
        $fromStringable = StringMimeType::tryFromMixed(new class {
            public function __toString(): string
            {
                return 'text/html';
            }
        });
        $fromInvalidType = StringMimeType::tryFromMixed([]);
        $fromInvalidValue = StringMimeType::tryFromMixed('invalid-mime');
        $fromNull = StringMimeType::tryFromMixed(null);

        expect($fromString)
            ->toBeInstanceOf(StringMimeType::class)
            ->and($fromString->value())
            ->toBe('image/png')
            ->and($fromStringable)
            ->toBeInstanceOf(StringMimeType::class)
            ->and($fromStringable->value())
            ->toBe('text/html')
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringMimeType', function (): void {
        $m = new StringMimeType('application/json');
        expect($m->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringMimeType', function (): void {
        $m = new StringMimeType('application/json');
        expect($m->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringMimeType::fromString('application/json');
        expect($v->isTypeOf(StringMimeType::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringMimeType::fromString('application/json');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('covers conversions for StringMimeType', function (): void {
        // These will likely fail validation in constructor if from* methods use default boolToString etc.
        // But for coverage we check them.
        expect(fn() => StringMimeType::fromInt(123))->toThrow(MimeTypeStringTypeException::class);

        $v = StringMimeType::fromString('application/json');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for invalid inputs', function (): void {
        expect(StringMimeType::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeType::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeType::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeType::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
    });

    it('kills InstanceOfToTrue mutation in tryFromMixed', function (): void {
        $v = StringMimeTypeTest::tryFromMixed(null);

        expect($v)->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringMimeTypeTest extends StringMimeType
{
    public function __construct(string $value = 'text/plain')
    {
        parent::__construct($value);
    }

    public static function fromString(string $value): static
    {
        return new static('text/plain');
    }
}

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringMimeTypeStandardTest extends StringMimeType
{
    public static function fromBool(bool $value): static
    {
        throw new MimeTypeStringTypeException('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new MimeTypeStringTypeException('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new MimeTypeStringTypeException('test');
    }

    public static function fromInt(int $value): static
    {
        throw new MimeTypeStringTypeException('test');
    }

    public static function fromString(string $value): static
    {
        throw new MimeTypeStringTypeException('test');
    }
}

describe('Throwing static StringMimeTypeStandard', function () {
    it('StringMimeType::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringMimeTypeStandardTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeTypeStandardTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeTypeStandardTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeTypeStandardTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeTypeStandardTest::tryFromMixed('application/json'))->toBeInstanceOf(Undefined::class)
            ->and(StringMimeTypeStandardTest::tryFromString('application/json'))->toBeInstanceOf(Undefined::class);
    });
});

describe('Null checks', function () {
    it('throws exception on fromNull', function () {
        expect(fn() => StringMimeType::fromNull(null))
            ->toThrow(MimeTypeStringTypeException::class);
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringMimeType::toNull())
            ->toThrow(MimeTypeStringTypeException::class);
    });
});
