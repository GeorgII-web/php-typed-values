<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\PhoneE164StringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringPhoneE164;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(StringPhoneE164::class);

describe('StringPhoneE164', function () {
    $validPhone = '+14155552671';

    it('accepts valid E.164 phone number, preserves value/toString and casts via __toString', function () use ($validPhone): void {
        $s = new StringPhoneE164($validPhone);

        expect($s->value())
            ->toBe($validPhone)
            ->and($s->toString())
            ->toBe($validPhone)
            ->and((string) $s)
            ->toBe($validPhone);
    });

    it('throws PhoneE164StringTypeException on empty or invalid phone numbers', function (string $value, string $message): void {
        expect(fn() => new StringPhoneE164($value))
            ->toThrow(PhoneE164StringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty phone number'],
        ['14155552671', 'Expected valid E.164 phone number, got "14155552671"'],
        ['+04155552671', 'Expected valid E.164 phone number, got "+04155552671"'],
        ['+1', 'Expected valid E.164 phone number, got "+1"'],
        ['+1234567890123456', 'Expected valid E.164 phone number, got "+1234567890123456"'],
        ['+1415555ABCD', 'Expected valid E.164 phone number, got "+1415555ABCD"'],
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function () use ($validPhone): void {
        $ok = StringPhoneE164::tryFromString($validPhone);
        $bad = StringPhoneE164::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringPhoneE164::class)
            ->and($ok->value())
            ->toBe($validPhone)
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function () use ($validPhone): void {
        expect(StringPhoneE164::fromString($validPhone)->jsonSerialize())->toBe($validPhone);
    });

    it('tryFromMixed returns instance for valid phone numbers and Undefined for invalid or non-convertible', function () use ($validPhone): void {
        $fromString = StringPhoneE164::tryFromMixed($validPhone);
        $fromStringable = StringPhoneE164::tryFromMixed(new class($validPhone) {
            public function __construct(private string $val)
            {
            }

            public function __toString(): string
            {
                return $this->val;
            }
        });
        $fromInvalidType = StringPhoneE164::tryFromMixed([]);
        $fromInvalidValue = StringPhoneE164::tryFromMixed('invalid');
        $fromNull = StringPhoneE164::tryFromMixed(null);
        $fromObject = StringPhoneE164::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringPhoneE164::class)
            ->and($fromString->value())
            ->toBe($validPhone)
            ->and($fromStringable)
            ->toBeInstanceOf(StringPhoneE164::class)
            ->and($fromStringable->value())
            ->toBe($validPhone)
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringPhoneE164', function () use ($validPhone): void {
        $s = new StringPhoneE164($validPhone);
        expect($s->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringPhoneE164', function () use ($validPhone): void {
        $s = new StringPhoneE164($validPhone);
        expect($s->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function () use ($validPhone): void {
        $v = StringPhoneE164::fromString($validPhone);
        expect($v->isTypeOf(StringPhoneE164::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () use ($validPhone): void {
        $v = StringPhoneE164::fromString($validPhone);
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () use ($validPhone): void {
        $v = StringPhoneE164::fromString($validPhone);
        expect($v->isTypeOf('NonExistentClass', StringPhoneE164::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringPhoneE164', function () use ($validPhone): void {
        expect(fn() => StringPhoneE164::fromInt(123))->toThrow(PhoneE164StringTypeException::class);

        $v = StringPhoneE164::fromString($validPhone);
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for invalid inputs', function (): void {
        expect(StringPhoneE164::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringPhoneE164Test extends StringPhoneE164
{
    public static function fromBool(bool $value): static
    {
        throw new PhoneE164StringTypeException('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new PhoneE164StringTypeException('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new PhoneE164StringTypeException('test');
    }

    public static function fromInt(int $value): static
    {
        throw new PhoneE164StringTypeException('test');
    }

    public static function fromString(string $value): static
    {
        throw new PhoneE164StringTypeException('test');
    }
}

describe('Throwing static StringPhoneE164', function () {
    $validPhone = '+14155552671';

    it('StringPhoneE164::tryFrom* returns Undefined when exception occurs (coverage)', function () use ($validPhone): void {
        expect(StringPhoneE164Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164Test::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164Test::tryFromMixed($validPhone))->toBeInstanceOf(Undefined::class)
            ->and(StringPhoneE164Test::tryFromString($validPhone))->toBeInstanceOf(Undefined::class);
    });
});

describe('Null checks', function () {
    it('throws exception on fromNull', function () {
        expect(fn() => StringPhoneE164::fromNull(null))
            ->toThrow(PhoneE164StringTypeException::class);
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringPhoneE164::toNull())
            ->toThrow(PhoneE164StringTypeException::class);
    });
});
