<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use const STDOUT;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\String\StringUsernameException;
use PhpTypedValues\String\Specific\StringUsername;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(StringUsername::class);

describe('StringUsername', function () {
    describe('Construction and Validation', function () {
        it('creates an instance from a valid username', function (string $username): void {
            $v = StringUsername::fromString($username);
            expect($v)->toBeInstanceOf(StringUsername::class)
                ->and($v->toString())->toBe($username)
                ->and($v->value())->toBe($username);
        })->with([
            'john_doe',
            'jane.doe',
            'user-123',
            'ABC',
            'a.b_c-1',
            'very_long_username_up_to_30_ch',
        ]);

        it('throws StringUsernameException for invalid username', function (string $invalidUsername): void {
            expect(fn() => StringUsername::fromString($invalidUsername))
                ->toThrow(StringUsernameException::class, "Expected valid username, got \"{$invalidUsername}\"");
        })->with([
            'ab', // too short
            'this_is_a_very_long_username_that_exceeds_thirty_characters', // too long
            'user@name', // invalid char
            'user name', // space
            '',
        ]);
    });

    describe('Factory methods', function () {
        it('creates instance from bool', function (): void {
            expect(StringUsername::fromBool(true)->value())->toBe('true')
                ->and(StringUsername::fromBool(false)->value())->toBe('false');
        });

        it('creates instance from valid int', function (): void {
            expect(StringUsername::fromInt(123)->value())->toBe('123');
        });

        it('throws StringUsernameException from fromInt with invalid value', function (): void {
            expect(fn() => StringUsername::fromInt(12)) // too short "12"
                ->toThrow(StringUsernameException::class);
        });

        it('throws StringUsernameException from fromDecimal', function (string $invalid): void {
            expect(fn() => StringUsername::fromDecimal($invalid))
                ->toThrow(StringUsernameException::class);
        })->with([
            '0.123456789012345678901234567890', // 32 chars
        ]);

        it('throws StringUsernameException from fromFloat', function (float $invalid): void {
            expect(fn() => StringUsername::fromFloat($invalid))
                ->toThrow(StringUsernameException::class);
        })->with([
            1.23456789012345678901234567890e30, // very long
        ]);
    });

    describe('Conversion methods', function () {
        it('throws StringTypeException when converting non-int-string to int', function (): void {
            $v = StringUsername::fromString('my_user');
            expect(fn() => $v->toInt())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-float-string to float', function (): void {
            $v = StringUsername::fromString('my_user');
            expect(fn() => $v->toFloat())->toThrow(StringTypeException::class);
        });

        it('throws StringTypeException when converting non-bool-string to bool', function (): void {
            $v = StringUsername::fromString('my_user');
            expect(fn() => $v->toBool())->toThrow(StringTypeException::class);
        });

        it('throws DecimalTypeException when converting non-decimal-string to decimal', function (): void {
            $v = StringUsername::fromString('my_user');
            expect(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
        });

        it('converts to other types when string value is compatible', function (): void {
            $v = StringUsername::fromString('123');
            expect($v->toInt())->toBe(123);
        });
    });

    describe('Try methods', function () {
        it('tryFromString returns instance or default', function (): void {
            expect(StringUsername::tryFromString('my_user'))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromString('ab'))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromBool returns instance or default', function (): void {
            expect(StringUsername::tryFromBool(true))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromBool(true)->value())->toBe('true');
        });

        it('tryFromInt returns instance or default', function (): void {
            expect(StringUsername::tryFromInt(123))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed returns instance or default', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return 'my_user';
                }
            };

            $nonStringable = new stdClass();

            expect(StringUsername::tryFromMixed('my_user'))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromMixed(123))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromMixed(1))->toBeInstanceOf(Undefined::class)
                ->and(StringUsername::tryFromMixed(true))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromMixed('ab'))->toBeInstanceOf(Undefined::class)
                ->and(StringUsername::tryFromMixed($stringable))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromMixed(null))->toBeInstanceOf(StringUsername::class)
                ->and(StringUsername::tryFromMixed($nonStringable))->toBeInstanceOf(Undefined::class)
                ->and(StringUsername::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(StringUsername::tryFromMixed(STDOUT))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles non-convertible types correctly with custom default', function (): void {
            $default = StringUsername::fromString('default_user');

            expect(StringUsername::tryFromMixed([], $default))->toBe($default)
                ->and(StringUsername::tryFromMixed(new stdClass(), $default))->toBe($default)
                ->and(StringUsername::tryFromMixed(STDOUT, $default))->toBe($default);
        });
    });

    describe('Metadata', function () {
        it('is not empty', function (): void {
            $v = StringUsername::fromString('my_user');
            expect($v->isEmpty())->toBeFalse();
        });

        it('is not undefined', function (): void {
            $v = StringUsername::fromString('my_user');
            expect($v->isUndefined())->toBeFalse();
        });

        it('identifies its type', function (): void {
            $v = StringUsername::fromString('my_user');
            expect($v->isTypeOf(StringUsername::class))->toBeTrue()
                ->and($v->isTypeOf('StringUsername'))->toBeFalse()
                ->and($v->isTypeOf(StringUsername::class, 'other'))->toBeTrue();
        });

        it('serializes to JSON', function (): void {
            $v = StringUsername::fromString('my_user');
            expect(json_encode($v))->toBe('"my_user"');
        });

        it('converts to string via __toString', function (): void {
            $v = StringUsername::fromString('my_user');
            expect((string) $v)->toBe('my_user');
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
readonly class StringUsernameTest extends StringUsername
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
        if ($value === 'null') {
            throw new Exception('Trigger fromString for null');
        }

        if ($value === 'trigger-exception') {
            throw new Exception('Trigger fromString');
        }

        return new self('generic_user');
    }
}

    describe('Coverage for mutants', function () {
        it('tryFromMixed returns instance for string value', function (): void {
            expect(StringUsername::tryFromMixed('john_doe', StringUsername::fromString('wrong')))->not->toBe(StringUsername::fromString('wrong'));
        });

        it('tryFromMixed returns instance for int value', function (): void {
            expect(StringUsername::tryFromMixed(123, StringUsername::fromString('wrong')))->not->toBe(StringUsername::fromString('wrong'));
        });

        it('tryFromMixed specifically triggers fromString("null") for null value', function (): void {
        $default = StringUsername::fromString('default_user');
        $result = StringUsernameTest::tryFromMixed(null, $default);
        expect($result)->toBe($default);
    });

    it('tryFromMixed specifically triggers default branch for unknown types like array', function (): void {
        $result = StringUsernameTest::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });

    it('tryFrom* methods return default on exception', function (): void {
        $default = new Undefined();

        expect(StringUsernameTest::tryFromBool(true, $default))->toBe($default)
            ->and(StringUsernameTest::tryFromDecimal('1.23', $default))->toBe($default)
            ->and(StringUsernameTest::tryFromFloat(1.23, $default))->toBe($default)
            ->and(StringUsernameTest::tryFromInt(123, $default))->toBe($default)
            ->and(StringUsernameTest::tryFromString('trigger-exception', $default))->toBe($default);
    });
});
