<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use const STDOUT;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringSlugException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringSlug;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

describe('StringSlug', function () {
    describe('Construction and Validation', function () {
        it('creates an instance from a valid slug', function (string $slug): void {
            $v = StringSlug::fromString($slug);
            expect($v)->toBeInstanceOf(StringSlug::class)
                ->and($v->toString())->toBe($slug)
                ->and($v->value())->toBe($slug);
        })->with([
            'my-awesome-slug',
            'slug-123',
            '123',
            'a',
            'some-very-long-slug-with-many-hyphens',
        ]);

        it('throws StringSlugException for invalid slug', function (string $invalidSlug): void {
            expect(fn() => StringSlug::fromString($invalidSlug))
                ->toThrow(StringSlugException::class, "Expected valid slug, got \"{$invalidSlug}\"");
        })->with([
            '-starts-with-hyphen',
            'ends-with-hyphen-',
            'Contains-Uppercase',
            'double--hyphen',
            'space in slug',
            'special!char',
            '',
        ]);
    });

    describe('Factory methods', function () {
        it('creates instance from bool', function (): void {
            expect(StringSlug::fromBool(true)->value())->toBe('true')
                ->and(StringSlug::fromBool(false)->value())->toBe('false');
        });

        it('creates instance from valid int', function (): void {
            expect(StringSlug::fromInt(123)->value())->toBe('123');
        });

        it('throws StringSlugException from fromInt with invalid value', function (): void {
            expect(fn() => StringSlug::fromInt(-123))
                ->toThrow(StringSlugException::class);
        });

        it('throws StringSlugException from fromDecimal', function (): void {
            expect(fn() => StringSlug::fromDecimal('1.23'))
                ->toThrow(StringSlugException::class);
        });

        it('throws StringSlugException from fromFloat', function (): void {
            expect(fn() => StringSlug::fromFloat(1.23))
                ->toThrow(StringSlugException::class);
        });
    });

    describe('Conversion methods', function () {
        it('throws StringTypeException when converting non-int-string to int', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-float-string to float', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect(fn() => $v->toFloat())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-bool-string to bool', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect(fn() => $v->toBool())->toThrow(StringTypeException::class);
        });

        it('throws DecimalTypeException when converting non-decimal-string to decimal', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });

        it('converts to other types when string value is compatible', function (): void {
            $v = StringSlug::fromString('123');
            expect($v->toInt())->toBe(123);
        });
    });

    describe('Try methods', function () {
        it('tryFromString returns instance or default', function (): void {
            expect(StringSlug::tryFromString('my-slug'))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromString('Invalid Slug'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromBool returns instance or default', function (): void {
            expect(StringSlug::tryFromBool(true))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromBool(true)->value())->toBe('true');
        });

        it('tryFromInt returns instance or default', function (): void {
            expect(StringSlug::tryFromInt(123))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromInt(-1))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromDecimal returns instance or default', function (): void {
            expect(StringSlug::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (): void {
            expect(StringSlug::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed returns instance or default', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return 'my-slug';
                }
            };

            $nonStringable = new stdClass();

            expect(StringSlug::tryFromMixed('my-slug'))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromMixed(123))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromMixed(-123))->toBeInstanceOf(Undefined::class)
                ->and(StringSlug::tryFromMixed(true))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromMixed('Invalid Slug'))->toBeInstanceOf(Undefined::class)
                ->and(StringSlug::tryFromMixed($stringable))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromMixed(null))->toBeInstanceOf(StringSlug::class)
                ->and(StringSlug::tryFromMixed($nonStringable))->toBeInstanceOf(Undefined::class)
                ->and(StringSlug::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(StringSlug::tryFromMixed(STDOUT))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles non-convertible types correctly with custom default', function (): void {
            $default = StringSlug::fromString('default-slug');

            expect(StringSlug::tryFromMixed([], $default))->toBe($default)
                ->and(StringSlug::tryFromMixed(new stdClass(), $default))->toBe($default)
                ->and(StringSlug::tryFromMixed(STDOUT, $default))->toBe($default);
        });
    });

    describe('Metadata', function () {
        it('is not empty', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect($v->isEmpty())->toBeFalse();
        });

        it('is not undefined', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect($v->isUndefined())->toBeFalse();
        });

        it('identifies its type', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect($v->isTypeOf(StringSlug::class))->toBeTrue()
                ->and($v->isTypeOf('StringSlug'))->toBeFalse()
                ->and($v->isTypeOf(StringSlug::class, 'other'))->toBeTrue();
        });

        it('serializes to JSON', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect(json_encode($v))->toBe('"my-slug"');
        });

        it('converts to string via __toString', function (): void {
            $v = StringSlug::fromString('my-slug');
            expect((string) $v)->toBe('my-slug');
        });
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringSlugTest extends StringSlug
{
    public static function fromString(string $value): static
    {
        if ($value === 'null') {
            throw new Exception('Trigger fromString for null');
        }

        return new self('generic-slug');
    }
}

describe('Coverage for mutants', function () {
    it('tryFromMixed specifically triggers fromString("null") for null value', function (): void {
        $default = StringSlug::fromString('default-slug');
        $result = StringSlugTest::tryFromMixed(null, $default);
        expect($result)->toBe($default);
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        $result = StringSlugTest::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});
