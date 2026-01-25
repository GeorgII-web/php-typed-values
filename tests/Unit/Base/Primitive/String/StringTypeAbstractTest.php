<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Bool\BoolTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StringTypeAbstract::class);

/**
 * @internal
 *
 * @coversNothing
 */
readonly class StringTypeAbstractTest extends StringTypeAbstract
{
    public function __construct(private string $val)
    {
    }

    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
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
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Throwable) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Throwable) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Throwable) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                \is_string($value) => static::fromString($value),
                \is_float($value) => static::fromFloat($value),
                \is_int($value) => static::fromInt($value),
                ($value instanceof self) => static::fromString($value->value()),
                \is_bool($value) => static::fromBool($value),
                $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Throwable) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Throwable) {
            /** @var T */
            return $default;
        }
    }

    public function value(): string
    {
        return $this->val;
    }
}

describe('StringTypeAbstract', function () {
    describe('Creation via Mock', function () {
        it('creates instance from string', function () {
            $mock = StringTypeAbstractTest::fromString('test');
            expect($mock)->toBeInstanceOf(StringTypeAbstractTest::class)
                ->and($mock->value())->toBe('test');
        });

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, string $expected) {
            $result = StringTypeAbstractTest::tryFromMixed($input);
            expect($result)->toBeInstanceOf(StringTypeAbstractTest::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'string' => ['hello', 'hello'],
            'float' => [3.14, '3.14000000000000012'],
            'int' => [42, '42'],
            'bool true' => [true, 'true'],
            'bool false' => [false, 'false'],
            'StringTypeAbstractTest instance' => [new StringTypeAbstractTest('instance'), 'instance'],
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
            expect(StringTypeAbstractTest::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'array' => [[]],
            'object' => [new stdClass()],
            'null' => [null],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $isSuccess) {
            $result = StringTypeAbstractTest::tryFromString($input);
            if ($isSuccess) {
                expect($result)->toBeInstanceOf(StringTypeAbstractTest::class)
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
            $mock = new StringTypeAbstractTest('test');
            expect($mock->value())->toBe('test')
                ->and($mock->toString())->toBe('test')
                ->and((string) $mock)->toBe('test')
                ->and($mock->jsonSerialize())->toBe('test')
                ->and($mock->isUndefined())->toBeFalse();
        });

        it('isEmpty returns correct boolean', function (string $input, bool $expected) {
            expect((new StringTypeAbstractTest($input))->isEmpty())->toBe($expected);
        })->with([
            'empty' => ['', true],
            'not empty' => ['not-empty', false],
        ]);
    });

    describe('isTypeOf', function () {
        it('returns true when class matches', function () {
            $mock = new StringTypeAbstractTest('test');
            expect($mock->isTypeOf(StringTypeAbstractTest::class))->toBeTrue();
        });

        it('returns false when class does not match', function () {
            $mock = new StringTypeAbstractTest('test');
            expect($mock->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('returns true for multiple classNames when one matches', function () {
            $mock = new StringTypeAbstractTest('test');
            expect($mock->isTypeOf('NonExistentClass', StringTypeAbstractTest::class, 'AnotherClass'))->toBeTrue();
        });

        it('returns false for multiple classNames when none match (kills FalseToTrue)', function () {
            $mock = new StringTypeAbstractTest('test');
            expect($mock->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
        });

        it('returns false for empty classNames (kills ForeachEmptyIterable)', function () {
            $mock = new StringTypeAbstractTest('test');
            expect($mock->isTypeOf())->toBeFalse();
        });

        it('returns false if IfNegated mutant triggers', function () {
            $mock = new StringTypeAbstractTest('test');
            // If mutated to "if (!$this instanceof $className)" it would return true for non-matching class
            expect($mock->isTypeOf('stdClass'))->toBeFalse();
        });
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
