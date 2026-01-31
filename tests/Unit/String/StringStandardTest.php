<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringStandard', function () {
    describe('Core behavior', function () {
        it('StringStandard::tryFromString returns instance for any string', function (): void {
            $v = StringStandard::tryFromString('hello');

            expect($v)
                ->toBeInstanceOf(StringStandard::class)
                ->and($v->value())
                ->toBe('hello')
                ->and($v->toString())
                ->toBe('hello');
        });

        it('jsonSerialize returns string', function (): void {
            expect(StringStandard::tryFromString('hello')->jsonSerialize())->toBeString();
        });

        it('tryFromMixed returns instance for valid string-convertible values', function (): void {
            $fromString = StringStandard::tryFromMixed('world');
            $fromInt = StringStandard::tryFromMixed(123);
            $fromFloat = StringStandard::tryFromMixed(45.67);
            $fromNull = StringStandard::tryFromMixed(null);

            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return 'stringable-content';
                }
            };
            $fromStringable = StringStandard::tryFromMixed($stringable);

            expect($fromString)
                ->toBeInstanceOf(StringStandard::class)
                ->and($fromString->value())
                ->toBe('world')
                ->and($fromInt)
                ->toBeInstanceOf(StringStandard::class)
                ->and($fromInt->value())
                ->toBe('123')
                ->and($fromFloat)
                ->toBeInstanceOf(StringStandard::class)
                ->and($fromFloat->value())
                ->toBe('45.67000000000000171')
                ->and($fromNull)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromStringable)
                ->toBeInstanceOf(StringStandard::class)
                ->and($fromStringable->value())
                ->toBe('stringable-content');
        });

        it('tryFromMixed returns Undefined for non-convertible values', function (): void {
            $fromArray = StringStandard::tryFromMixed([]);
            $fromObject = StringStandard::tryFromMixed(new stdClass());

            $stringableWithError = new class implements Stringable {
                public function __toString(): string
                {
                    throw new Exception('Simulated error during string conversion');
                }
            };
            $fromError = StringStandard::tryFromMixed($stringableWithError);

            expect($fromArray)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromObject)
                ->toBeInstanceOf(Undefined::class)
                ->and($fromError)
                ->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles non-stringable objects and non-scalar types explicitly', function (): void {
            // stdClass is not Stringable and not scalar
            expect(StringStandard::tryFromMixed(new stdClass()))->toBeInstanceOf(Undefined::class)
                ->and(StringStandard::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(StringStandard::tryFromMixed(\STDOUT))->toBeInstanceOf(Undefined::class);
        });

        it('fromString creates instance with correct value', function (): void {
            $s = StringStandard::fromString('test');

            expect($s)
                ->toBeInstanceOf(StringStandard::class)
                ->and($s->value())
                ->toBe('test');
        });

        it('__toString returns the string value', function (): void {
            $s = StringStandard::fromString('cast test');

            expect((string) $s)->toBe('cast test')
                ->and($s->__toString())->toBe('cast test');
        });

        it('isEmpty is false for non-empty StringStandard', function (): void {
            $s = StringStandard::fromString('x');
            expect($s->isEmpty())->toBeFalse();
        });

        it('isEmpty is true for empty StringStandard', function (): void {
            $s = StringStandard::fromString('');
            expect($s->isEmpty())->toBeTrue();
        });

        it('isUndefined is always false for StringStandard', function (): void {
            expect(StringStandard::fromString('x')->isUndefined())->toBeFalse()
                ->and(StringStandard::fromString('')->isUndefined())->toBeFalse();
        });

        it('StringStandard::tryFromBool returns instance from bool', function (): void {
            $fromTrue = StringStandard::tryFromBool(true);
            $fromFalse = StringStandard::tryFromBool(false);

            expect($fromTrue)->toBeInstanceOf(StringStandard::class)
                ->and($fromTrue->value())->toBe('true')
                ->and($fromFalse)->toBeInstanceOf(StringStandard::class)
                ->and($fromFalse->value())->toBe('false');
        });

        it('StringStandard::tryFromFloat returns instance from float', function (): void {
            $v = StringStandard::tryFromFloat(1.23);

            expect($v)->toBeInstanceOf(StringStandard::class)
                ->and($v->value())->toBe('1.22999999999999998');
        });

        it('StringStandard::tryFromInt returns instance from int', function (): void {
            $v = StringStandard::tryFromInt(123);

            expect($v)->toBeInstanceOf(StringStandard::class)
                ->and($v->value())->toBe('123');
        });

        it('StringStandard::tryFromDecimal returns instance from decimal', function (): void {
            $v = StringStandard::tryFromDecimal('1.23');

            expect($v)->toBeInstanceOf(StringStandard::class)
                ->and($v->value())->toBe('1.23');
        });

        it('StringStandard conversions to bool, float, int, decimal', function (): void {
            $vTrue = StringStandard::fromString('true');
            $vFalse = StringStandard::fromString('false');
            $vFloat = StringStandard::fromString('1.5');
            $vInt = StringStandard::fromString('123');
            $vDecimal = StringStandard::fromString('1.23');

            expect($vTrue->toBool())->toBeTrue()
                ->and($vFalse->toBool())->toBeFalse()
                ->and($vFloat->toFloat())->toBe(1.5)
                ->and($vInt->toInt())->toBe(123)
                ->and($vDecimal->toDecimal())->toBe('1.23');
        });

        it('StringStandard::fromBool creates instance', function (): void {
            expect(StringStandard::fromBool(true)->value())->toBe('true');
        });

        it('StringStandard::fromInt creates instance', function (): void {
            expect(StringStandard::fromInt(456)->value())->toBe('456');
        });

        it('StringStandard::fromFloat creates instance', function (): void {
            expect(StringStandard::fromFloat(0.5)->value())->toBe('0.5');
        });

        it('StringStandard::fromDecimal creates instance', function (): void {
            expect(StringStandard::fromDecimal('1.23')->value())->toBe('1.23');
        });

        it('tryFromMixed covers all arms', function (): void {
            $v1 = StringStandard::tryFromMixed('test');
            $v2 = StringStandard::tryFromMixed(1.23);
            $v3 = StringStandard::tryFromMixed(456);
            $v4 = StringStandard::tryFromMixed(StringStandard::fromString('nested'));
            $v5 = StringStandard::tryFromMixed(true);
            $v6 = StringStandard::tryFromMixed(new class implements Stringable {
                public function __toString(): string
                {
                    return 'stringable';
                }
            });

            expect($v1->value())->toBe('test')
                ->and($v2->value())->toBe('1.22999999999999998')
                ->and($v3->value())->toBe('456')
                ->and($v4->value())->toBe('nested')
                ->and($v5->value())->toBe('true')
                ->and($v6->value())->toBe('stringable');
        });

        it('isTypeOf returns true when class matches', function (): void {
            $v = StringStandard::fromString('test');
            expect($v->isTypeOf(StringStandard::class))->toBeTrue();
        });

        it('isTypeOf returns false when class does not match', function (): void {
            $v = StringStandard::fromString('test');
            expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true for multiple classNames when one matches', function (): void {
            $v = StringStandard::fromString('test');
            expect($v->isTypeOf('NonExistentClass', StringStandard::class, 'AnotherClass'))->toBeTrue();
        });

        it('toBool, toFloat, toInt throw for invalid strings in StringStandard', function (): void {
            $v = StringStandard::fromString('not-a-bool');
            expect(fn() => $v->toBool())->toThrow(StringTypeException::class);

            $v2 = StringStandard::fromString('not-a-float');
            expect(fn() => $v2->toFloat())->toThrow(StringTypeException::class);

            $v3 = StringStandard::fromString('not-an-int');
            expect(fn() => $v3->toInt())->toThrow(StringTypeException::class);
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
readonly class StringStandardTest extends StringStandard
{
    public static function fromDecimal(string $value): static
    {
        throw new Exception('Simulated error');
    }

    /** @psalm-suppress LessSpecificReturnType */
    public static function fromString(string $value): static
    {
        throw new Exception('Simulated error');
    }
}

describe('StringStandardTest (Throwing static)', function () {
    it('StringStandard::tryFromString returns Undefined when fromString throws', function (): void {
        $result = StringStandardTest::tryFromString('fail');
        expect($result)->toBeInstanceOf(Undefined::class);
    });

    it('StringStandard::tryFromDecimal returns Undefined when fromDecimal throws (StringStandardTest)', function (): void {
        $result = StringStandardTest::tryFromDecimal('fail');
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});

readonly class ThrowingStringStandard extends StringStandard
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

describe('ThrowingStringStandard', function () {
    it('StringStandard::tryFromBool returns Undefined when fromBool throws', function (): void {
        expect(ThrowingStringStandard::tryFromBool(true))->toBeInstanceOf(Undefined::class);
    });

    it('StringStandard::tryFromFloat returns Undefined when fromFloat throws', function (): void {
        expect(ThrowingStringStandard::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class);
    });

    it('StringStandard::tryFromInt returns Undefined when fromInt throws', function (): void {
        expect(ThrowingStringStandard::tryFromInt(1))->toBeInstanceOf(Undefined::class);
    });

    it('StringStandard::tryFromString returns Undefined when fromString throws (using throwing class)', function (): void {
        expect(ThrowingStringStandard::tryFromString('fail'))->toBeInstanceOf(Undefined::class);
    });

    it('StringStandard::tryFromDecimal returns Undefined when fromDecimal throws', function (): void {
        expect(ThrowingStringStandard::tryFromDecimal('fail'))->toBeInstanceOf(Undefined::class);
    });

    it('StringStandard::tryFromMixed returns Undefined when static method throws', function (): void {
        expect(ThrowingStringStandard::tryFromMixed('any'))->toBeInstanceOf(Undefined::class);
    });
});
