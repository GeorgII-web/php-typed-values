<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Base\Primitive\Decimal;

use PhpTypedValues\Base\Primitive\Decimal\DecimalTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;
use Throwable;

use function is_bool;
use function is_float;
use function is_int;
use function is_string;

covers(DecimalTypeAbstract::class);

/**
 * @internal
 *
 * @coversNothing
 */
readonly class DecimalTypeAbstractTest extends DecimalTypeAbstract
{
    public function __construct(private string $val)
    {
    }

    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    public static function fromDecimal(string $value): static
    {
        return new static($value);
    }

    /**
     * @throws FloatTypeException
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws FloatTypeException
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function isEmpty(): bool
    {
        return $this->val === '';
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws BoolTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    public function toDecimal(): string
    {
        return $this->value();
    }

    /**
     * @throws FloatTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws IntegerTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    public function toString(): string
    {
        return $this->value();
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Throwable) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Throwable) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Throwable) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Throwable) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                is_float($value) => static::fromFloat($value),
                is_int($value) => static::fromInt($value),
                ($value instanceof self) => static::fromString($value->value()),
                is_bool($value) => static::fromBool($value),
                $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Throwable) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): PrimitiveTypeAbstract|static {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Throwable) {
            /** @var PrimitiveTypeAbstract */
            return $default;
        }
    }

    public function value(): string
    {
        return $this->val;
    }
}

describe('DecimalTypeAbstract', function () {
    describe('Creation via Mock', function () {
        it('creates instance from string', function () {
            $mock = DecimalTypeAbstractTest::fromString('test');
            expect($mock)->toBeInstanceOf(DecimalTypeAbstractTest::class)
                ->and($mock->value())->toBe('test');
        });

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, string $expected) {
            $result = DecimalTypeAbstractTest::tryFromMixed($input);
            expect($result)->toBeInstanceOf(DecimalTypeAbstractTest::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'string' => ['hello', 'hello'],
            'float' => [3.14, '3.14000000000000012'],
            'int' => [42, '42'],
            'bool true' => [true, 'true'],
            'bool false' => [false, 'false'],
            'DecimalTypeAbstractTest instance' => [new DecimalTypeAbstractTest('instance'), 'instance'],
            'Stringable' => [
                new class implements Stringable {
                    public function __toString(): string
                    {
                        return 'stringable';
                    }
                },
                'stringable',
            ],
        ]);

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(DecimalTypeAbstractTest::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'array' => [[]],
            'object' => [new stdClass()],
            'null' => [null],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $isSuccess) {
            $result = DecimalTypeAbstractTest::tryFromString($input);
            if ($isSuccess) {
                expect($result)->toBeInstanceOf(DecimalTypeAbstractTest::class)
                    ->and($result->value())->toBe($input);
            } else {
                expect($result)->toBeInstanceOf(Undefined::class);
            }
        })->with([
            'valid' => ['world', true],
        ]);
    });

    describe('Instance Methods', function () {
        it('exposes internal value and formats', function () {
            $mock = new DecimalTypeAbstractTest('test');
            expect($mock->value())->toBe('test')
                ->and($mock->toString())->toBe('test')
                ->and((string) $mock)->toBe('test')
                ->and($mock->jsonSerialize())->toBe('test')
                ->and($mock->isUndefined())->toBeFalse();
        });

        it('isEmpty returns correct boolean', function (string $input, bool $expected) {
            expect((new DecimalTypeAbstractTest($input))->isEmpty())->toBe($expected);
        })->with([
            'empty' => ['', true],
            'not empty' => ['not-empty', false],
        ]);
    });

    describe('isTypeOf', function () {
        it('returns true when class matches', function () {
            $mock = new DecimalTypeAbstractTest('test');
            expect($mock->isTypeOf(DecimalTypeAbstractTest::class))->toBeTrue();
        });

        it('returns false when class does not match', function () {
            $mock = new DecimalTypeAbstractTest('test');
            expect($mock->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('returns true for multiple classNames when one matches', function () {
            $mock = new DecimalTypeAbstractTest('test');
            expect($mock->isTypeOf('NonExistentClass', DecimalTypeAbstractTest::class, 'AnotherClass'))->toBeTrue();
        });

        it('returns false for multiple classNames when none match (kills FalseToTrue)', function () {
            $mock = new DecimalTypeAbstractTest('test');
            expect($mock->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
        });

        it('returns false for empty classNames (kills ForeachEmptyIterable)', function () {
            $mock = new DecimalTypeAbstractTest('test');
            expect($mock->isTypeOf())->toBeFalse();
        });

        it('returns false if IfNegated mutant triggers', function () {
            $mock = new DecimalTypeAbstractTest('test');
            // If mutated to "if (!$this instanceof $className)" it would return true for non-matching class
            expect($mock->isTypeOf('stdClass'))->toBeFalse();
        });
    });

    describe('isValidRange', function () {
        it('validates ranges correctly', function (string $value, int $from, int $to, bool $expected) {
            $mock = new DecimalTypeAbstractTest($value);
            expect($mock->isValidRange($value, $from, $to))->toBe($expected);
        })->with([
            'positive within range' => ['50.0', 0, 100, true],
            'positive below range' => ['-1.0', 0, 100, false],
            'positive above range' => ['100.1', 0, 100, false],
            'zero' => ['0.0', 0, 100, true],

            'negative within range (same sign)' => ['-50.5', -100, 0, true],
            'negative upper boundary' => ['-0.000', -100, 0, true],
            'negative exceeded lower limit' => ['-100.1', -100, 0, false],
            'negative exceeded upper limit' => ['1.0', -100, 0, false],

            'positive vs negative limit' => ['5', -10, -1, false],
            'negative vs positive limit' => ['-5', 1, 10, false],

            'both negative, value < bound' => ['-15', -10, 0, false],
            'both negative, value > bound' => ['-5', -10, -1, true],
            'both negative, equal whole, zero fraction' => ['-10.0', -10, -5, true],
            'both negative, equal whole, non-zero fraction' => ['-10.5', -10, -5, false],

            'both positive, equal whole, non-zero fraction checks upper' => ['10.5', 5, 10, false],
            'both positive, equal whole, zero fraction checks upper' => ['10.0', 5, 10, true],
        ]);
    });

    describe('Concrete implementation check (StringStandard)', function () {
        it('__toString proxies to toString', function () {
            $v = new StringStandard('abc');
            expect((string) $v)->toBe($v->toString())
                ->and((string) $v)->toBe('abc');
        });

        it('fromString handles various inputs', function (string $input) {
            $s = StringStandard::fromString($input);
            expect($s->value())->toBe($input)
                ->and($s->toString())->toBe($input);
        })->with([
            'standard' => ['hello'],
            'empty' => [''],
            'unicode' => ['hi 🌟'],
            'whitespace' => ['  spaced  '],
        ]);
    });
});
