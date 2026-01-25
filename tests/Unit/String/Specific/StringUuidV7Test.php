<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\UuidStringTypeException;
use PhpTypedValues\String\Specific\StringUuidV7;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringUuidV7', function () {
    describe('Core behavior', function () {
        it('accepts a valid lowercase UUID v7 and preserves value', function (): void {
            $uuid = '01890f2a-5bcd-7def-8abc-1234567890ab';
            $s = new StringUuidV7($uuid);

            expect($s->value())->toBe($uuid)
                ->and($s->toString())->toBe($uuid);
        });

        it('normalizes uppercase input to lowercase while preserving the UUID semantics', function (): void {
            $upper = '01890F2A-5BCD-7DEF-9ABC-1234567890AB';
            $s = StringUuidV7::fromString($upper);

            expect($s->value())->toBe('01890f2a-5bcd-7def-9abc-1234567890ab')
                ->and($s->toString())->toBe('01890f2a-5bcd-7def-9abc-1234567890ab');
        });

        it('throws on empty string', function (): void {
            expect(fn() => new StringUuidV7(''))
                ->toThrow(UuidStringTypeException::class, 'Expected non-empty UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got ""');
        });

        it('throws when UUID version is not 7 (e.g., version 4)', function (): void {
            $v4 = '550e8400-e29b-41d4-a716-446655440000';
            expect(fn() => StringUuidV7::fromString($v4))
                ->toThrow(UuidStringTypeException::class, 'Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $v4 . '"');
        });

        it('throws when UUID variant nibble is invalid (must be 8,9,a,b)', function (): void {
            $badVariant = '550e8400-e29b-7d14-7716-446655440000';
            expect(fn() => new StringUuidV7($badVariant))
                ->toThrow(UuidStringTypeException::class, 'Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badVariant . '"');
        });

        it('throws on invalid characters or format (non-hex character)', function (): void {
            $badChar = '01890f2a-5bcd-7def-8abc-1234567890ag';
            expect(fn() => StringUuidV7::fromString($badChar))
                ->toThrow(UuidStringTypeException::class, 'Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badChar . '"');
        });

        it('StringUuidV7::tryFromString returns instance for valid UUID v7 (case-insensitive) and normalizes to lowercase', function (): void {
            $input = '01890F2A-5BCD-7DEF-9ABC-1234567890AB';
            $v = StringUuidV7::tryFromString($input);

            expect($v)
                ->toBeInstanceOf(StringUuidV7::class)
                ->and($v->value())
                ->toBe(strtolower($input));
        });

        it('StringUuidV7::tryFromString returns Undefined for non-v7 or invalid UUID', function (): void {
            $u1 = StringUuidV7::tryFromString('550e8400-e29b-41d4-a716-446655440000'); // v4
            $u2 = StringUuidV7::tryFromString('not-a-uuid');

            expect($u1)->toBeInstanceOf(Undefined::class)
                ->and($u2)->toBeInstanceOf(Undefined::class);
        });

        it('jsonSerialize returns string', function (): void {
            $t = new StringUuidV7('01890f2a-5bcd-7def-8abc-1234567890ab');
            expect($t->jsonSerialize())->toBeString();
        });

        it('__toString returns normalized lowercase UUID v7', function (): void {
            $input = '01890F2A-5BCD-7DEF-9ABC-1234567890AB';
            $u = StringUuidV7::fromString($input);

            expect((string) $u)->toBe(strtolower($input))
                ->and($u->__toString())->toBe(strtolower($input));
        });

        it('tryFromMixed returns instance for valid UUID v7 strings', function (): void {
            $fromString = StringUuidV7::tryFromMixed('01890f2a-5bcd-7def-8abc-1234567890ab');
            $fromStringable = StringUuidV7::tryFromMixed(new class {
                public function __toString(): string
                {
                    return '01890F2A-5BCD-7DEF-9ABC-1234567890AB';
                }
            });

            expect($fromString)
                ->toBeInstanceOf(StringUuidV7::class)
                ->and($fromString->value())
                ->toBe('01890f2a-5bcd-7def-8abc-1234567890ab')
                ->and($fromStringable)
                ->toBeInstanceOf(StringUuidV7::class)
                ->and($fromStringable->value())
                ->toBe('01890f2a-5bcd-7def-9abc-1234567890ab');
        });

        it('tryFromMixed returns Undefined for invalid or non-convertible values', function (): void {
            $fromInvalidUuid = StringUuidV7::tryFromMixed('not-a-uuid');
            $fromArray = StringUuidV7::tryFromMixed([]);
            $fromObject = StringUuidV7::tryFromMixed(new stdClass());
            $fromInt = StringUuidV7::tryFromMixed(123);
            $fromNull = StringUuidV7::tryFromMixed(null);

            expect($fromInvalidUuid)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromArray)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromObject)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromInt)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromNull)
                ->toBeInstanceOf(Undefined::class);
        });

        it('isEmpty is always false for StringUuidV7', function (): void {
            $u = new StringUuidV7('01890f2a-5bcd-7def-8abc-1234567890ab');
            expect($u->isEmpty())->toBeFalse();
        });

        it('isUndefined is always false for StringUuidV7', function (): void {
            $u = new StringUuidV7('01890f2a-5bcd-7def-8abc-1234567890ab');
            expect($u->isUndefined())->toBeFalse();
        });

        it('isTypeOf returns true when class matches', function (): void {
            $v = StringUuidV7::fromString('01890f2a-5bcd-7def-8abc-1234567890ab');
            expect($v->isTypeOf(StringUuidV7::class))->toBeTrue();
        });

        it('isTypeOf returns false when class does not match', function (): void {
            $v = StringUuidV7::fromString('01890f2a-5bcd-7def-8abc-1234567890ab');
            expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('covers conversions for StringUuidV7', function (): void {
            // Usually throw because 'true', '1.2', '123' are not valid UUIDs
            expect(fn() => StringUuidV7::fromBool(true))->toThrow(UuidStringTypeException::class)
                ->and(fn() => StringUuidV7::fromFloat(1.2))->toThrow(UuidStringTypeException::class)
                ->and(fn() => StringUuidV7::fromInt(123))->toThrow(UuidStringTypeException::class);

            $v = StringUuidV7::fromString('01890f2a-5bcd-7def-8abc-1234567890ab');
            expect(fn() => $v->toBool())->toThrow(PhpTypedValues\Exception\Integer\IntegerTypeException::class)
                ->and(fn() => $v->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
                ->and(fn() => $v->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class);
        });

        it('tryFromBool, tryFromFloat, tryFromInt return Undefined for StringUuidV7', function (): void {
            expect(StringUuidV7::tryFromBool(true))->toBeInstanceOf(Undefined::class)
                ->and(StringUuidV7::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
                ->and(StringUuidV7::tryFromInt(123))->toBeInstanceOf(Undefined::class);
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
readonly class StringUuidV7Test extends StringUuidV7
{
    public function __construct(string $value)
    {
        throw new Exception('test');
    }
}

describe('StringUuidV7Test (Throwing static)', function () {
    it('StringUuidV7::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringUuidV7Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringUuidV7Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringUuidV7Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringUuidV7Test::tryFromMixed('01890f2a-5bcd-7def-8abc-1234567890ab'))->toBeInstanceOf(Undefined::class)
            ->and(StringUuidV7Test::tryFromString('01890f2a-5bcd-7def-8abc-1234567890ab'))->toBeInstanceOf(Undefined::class);
    });
});
