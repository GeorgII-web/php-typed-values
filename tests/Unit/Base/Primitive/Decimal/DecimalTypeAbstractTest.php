<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Base\Primitive\Decimal;

use PhpTypedValues\Base\Primitive\Decimal\DecimalTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;
use ReflectionClass;
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

    public function callCompareDecimalWithInt(string $sign, string $whole, string $fraction, int $bound): int
    {
        $reflection = new ReflectionClass(DecimalTypeAbstract::class);
        $method = $reflection->getMethod('compareDecimalWithInt');

        return $method->invoke($this, $sign, $whole, $fraction, $bound);
    }

    public function callComparePositiveIntStrings(string $a, string $b): int
    {
        $reflection = new ReflectionClass(DecimalTypeAbstract::class);
        $method = $reflection->getMethod('comparePositiveIntStrings');

        return $method->invoke($this, $a, $b);
    }

    public function callParseDecimalString(string $value): array
    {
        $reflection = new ReflectionClass(DecimalTypeAbstract::class);
        $method = $reflection->getMethod('parseDecimalString');

        return $method->invoke($this, $value);
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

    public static function fromNull(null $value): never
    {
        throw new Exception('Value cannot be null');
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

    public function toNull(): never
    {
        throw new Exception('Value cannot be null');
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
            'positive, zero bound' => ['0.0', 0, 0, true],
            'negative, zero bound' => ['-0.0', 0, 0, false],
            'positive, zero bound, fraction' => ['0.1', 0, 0, false],
            'negative, zero bound, fraction' => ['-0.1', 0, 0, false],
            'positive, zero bound, mutation kill' => ['1.0', 0, 0, false],
            'positive > negative bound' => ['1.0', -10, -5, false],
            'negative whole equal bound' => ['-10.0', -10, -5, true],
            'negative whole equal bound, small fraction' => ['-10.1', -10, -5, false],
            'negative whole equal bound, zero fraction' => ['-10.0', -10, -5, true],
            'negative whole larger absolute, within range' => ['-15', -20, -10, true],
            'negative whole smaller absolute, within range' => ['-5', -10, 0, true],
            'negative whole equal bound, zero fraction kills negative-zero' => ['-5.0', -5, -1, true],
            'positive whole equal bound, zero fraction kills positive-zero' => ['5.0', 1, 5, true],
            'positive opposite sign' => ['10', -20, -10, false],
            'negative opposite sign' => ['-10', 5, 15, false],
            'positive > negative bound' => ['1.0', -10, -5, false],
            'negative < positive bound' => ['-1.0', 5, 10, false],
            'negative both match whole, decimal larger' => ['-10.5', -11, -10, true],
            'negative both match whole, decimal smaller' => ['-10.5', -10, -5, false],
            'positive both match whole, decimal larger' => ['10.5', 10, 11, true],
            'positive both match whole, decimal smaller' => ['10.5', 5, 10, false],
            'positive match upper bound exactly' => ['10.0', 5, 10, true],
            'positive slightly over upper bound' => ['10.1', 5, 10, false],
            'negative match lower bound exactly' => ['-10.0', -10, -5, true],
            'negative slightly under lower bound' => ['-10.1', -10, -5, false],
            'positive below range sign' => ['1.0', -1, 0, false],
            'negative < positive bound kills 860a127270d429bc' => ['-1.0', 0, 10, false],
            'positive > negative bound kills f5b3ca4d94518b12' => ['1.0', -10, -5, false],
            'negative > positive bound opposite sign 1' => ['-1.0', 1, 10, false],
            'positive < negative bound opposite sign 2' => ['1.0', -10, -5, false],
            'negative same sign larger abs' => ['-15.0', -10, -5, false],
            'negative same sign smaller abs' => ['-3.0', -10, -5, false],
            'negative same sign equal whole fraction zero' => ['-10.0', -10, -5, true],
            'negative same sign equal whole fraction non-zero' => ['-10.1', -10, -5, false],
            'positive same sign larger' => ['15.0', 5, 10, false],
            'positive same sign smaller' => ['3.0', 5, 10, false],
            'positive same sign equal whole fraction zero' => ['10.0', 5, 10, true],
            'positive same sign equal whole fraction non-zero' => ['10.1', 5, 10, false],
        ]);

        it('zero equals both bounds is valid', function () {
            $mock = new DecimalTypeAbstractTest('0.0');
            expect($mock->isValidRange('0.0', 0, 0))->toBeTrue();
        });

        it('negative fraction between -1 and 0 is within [-1,0]', function () {
            $mock = new DecimalTypeAbstractTest('-0.1');
            expect($mock->isValidRange('-0.1', -1, 0))->toBeTrue();
        });

        it('kills comparePositiveIntStrings mutants', function (string $val, int $from, int $to, bool $expected) {
            $mock = new DecimalTypeAbstractTest($val);
            expect($mock->isValidRange($val, $from, $to))->toBe($expected);
        })->with([
            'leading zeros match' => ['0010', 5, 15, true],
            'leading zeros mismatch' => ['0010', 10, 20, true],
            'different lengths' => ['100', 50, 150, true],
            'shorter value' => ['5', 10, 20, false],
            'longer value' => ['1000', 10, 100, false],
            'leading zeros on bound' => ['10', 0, 10, true],
            'leading zeros on both' => ['010', 5, 15, true],
            'different lengths 1' => ['9', 10, 20, false],
            'different lengths 2' => ['21', 10, 20, false],
            'same length mismatch' => ['15', 10, 20, true],
            'different lengths: val longer, should return true' => ['100', 50, 150, true],
            'different lengths: val shorter, should return true' => ['10', 5, 15, true],
            'different lengths: val longer than to, should return false' => ['151', 50, 150, false],
            'different lengths: val shorter than from, should return false' => ['49', 50, 150, false],
            'string vs length comparison 1' => ['2', 10, 20, false],
            'string vs length comparison 2' => ['20', 5, 15, false],
            'string vs length comparison 3' => ['100', 20, 30, false],
            'string vs length comparison 4' => ['20', 100, 200, false],
            'leading zeros on bound to kill UnwrapLtrim' => ['10', 0, 10, true],
            'leading zeros on bound to kill UnwrapLtrim 2' => ['10', 10, 20, true],
            'leading zeros on bound b to kill b0ec2b05726371d6' => ['10', 0, 10, true],
            'leading zeros on value to kill ltrim a' => ['0010', 10, 20, true],
            'different lengths to kill a306f2944cd209bc' => ['100', 10, 20, false],
            'different lengths return 1' => ['100', 10, 200, true],
            'different lengths return -1' => ['10', 100, 200, false],
            'length vs string order a>b' => ['9', 10, 20, false],
            'length vs string order b>a' => ['20', 1, 9, false],
            'kill early return: 9 in [5,10] true' => ['9', 5, 10, true],
            'kill early return: 11 in [5,9] false' => ['11', 5, 9, false],
        ]);

        it('kills parseDecimalString mutants', function (string $input, bool $shouldFail = false, string $expectedMessage = '') {
            $mock = new DecimalTypeAbstractTest('dummy');
            if ($shouldFail) {
                try {
                    $mock->isValidRange($input, 0, 10);
                    $this->fail('Exception not thrown for input: ' . $input);
                } catch (DecimalTypeException $e) {
                    if ($expectedMessage !== '') {
                        expect($e->getMessage())->toContain($expectedMessage);
                    }
                }
            } else {
                expect($mock->isValidRange($input, -100, 100))->toBeTrue();
            }
        })->with([
            'trimmed' => ['  1.2  ', true, 'String "  1.2  " has no valid decimal value'],
            'empty' => ['', true, 'String "" has no valid decimal value'],
            'only dot' => ['.', true, 'String "." has no valid decimal value'],
            'only sign' => ['-', true, 'String "-" has no valid decimal value'],
            'dot followed by nothing' => ['1.', false],
            'only fraction' => ['.5', false],
            'plus sign' => ['+1.5', false],
            'invalid characters' => ['1.2a', true, 'String "1.2a" has no valid decimal value'],
            'multiple dots' => ['1.2.3', true, 'String "1.2.3" has no valid decimal value'],
            'no digits' => ['+', true, 'String "+" has no valid decimal value'],
            'plus dot' => ['+.', true, 'String "+." has no valid decimal value'],
            'minus dot' => ['-.', true, 'String "-." has no valid decimal value'],
            'whole zero fraction empty' => ['0.0', false],
            'whole empty fraction zero' => ['0.', false],
            'whole non-empty fraction empty' => ['12.3', false],
            'whole non-empty fraction empty matches2' => ['12.30', false],
            'empty whole to kill 6d4d69531cbe38fb' => ['.5', false],
            'empty whole to kill b833a0c8f4060068' => ['.0', false],
            'empty fraction to kill 44dff3d9dbd6958b' => ['10', false],
        ]);
    });

    describe('Internal Methods (Killing Mutants via Reflection)', function () {
        it('kills compareDecimalWithInt return value mutants', function (string $sign, string $whole, string $fraction, int $bound, int $expected) {
            $mock = new DecimalTypeAbstractTest('dummy');
            expect($mock->callCompareDecimalWithInt($sign, $whole, $fraction, $bound))->toBe($expected);
        })->with([
            'negative < positive return -1' => ['-', '1', '0', 10, -1],
            'positive > negative return 1' => ['', '1', '0', -10, 1],
            'both negative, decimal smaller (more negative) return -1' => ['-', '15', '0', -10, -1],
            'both negative, decimal larger (less negative) return 1' => ['-', '5', '0', -10, 1],
            'both negative, whole equal, non-zero fraction smaller return -1' => ['-', '10', '1', -10, -1],
            'both positive, decimal larger return 1' => ['', '15', '0', 10, 1],
            'both positive, decimal smaller return -1' => ['', '5', '0', 10, -1],
            'both positive, whole equal, non-zero fraction larger return 1' => ['', '10', '1', 10, 1],
            'positive zero sign mutant' => ['', '10', '0', 0, 1],
            'positive value zero sign kill' => ['', '0', '0', 0, 0],
            'positive value zero sign kill 2' => ['', '1', '0', 0, 1],
            'positive value zero sign kill 3' => ['', '0', '1', 0, 1],
            'positive value positive sign kill' => ['', '0', '0', 1, -1],
            'positive value positive sign kill 2' => ['', '5', '0', 10, -1],
            'positive value positive sign kill 3' => ['', '10', '0', 5, 1],
            'positive value positive sign kill 4' => ['', '10', '0', 10, 0],
            'positive zero sign mutant exact' => ['', '0', '0', 0, 0],
            'negative zero sign mutant' => ['-', '10', '0', 0, -1],
            'negative value zero sign kill' => ['-', '0', '1', 0, -1],
            'positive value positive bound sign kill' => ['', '10', '0', 5, 1],
            'positive value zero bound sign kill' => ['', '10', '0', 0, 1],
            'both negative, whole equal, fraction zero return 0' => ['-', '10', '0', -10, 0],
            'both negative, whole larger return -1' => ['-', '20', '0', -10, -1],
            'both negative, whole larger return -1 by 1' => ['-', '11', '0', -10, -1],
            'both negative, whole smaller return 1' => ['-', '5', '0', -10, 1],
            'both negative, whole smaller return 1 by 1' => ['-', '9', '0', -10, 1],
            'positive bound zero' => ['', '10', '0', 0, 1],
            'positive bound positive' => ['', '10', '0', 10, 0],
        ]);

        it('kills comparePositiveIntStrings return value mutants', function (string $a, string $b, int $expected) {
            $mock = new DecimalTypeAbstractTest('dummy');
            expect($mock->callComparePositiveIntStrings($a, $b))->toBe($expected);
        })->with([
            'different lengths 1' => ['100', '10', 1],
            'different lengths 2' => ['10', '100', -1],
            'different lengths alpha 1' => ['2', '10', -1],
            'different lengths alpha 2' => ['10', '2', 1],
            'different lengths alpha 3' => ['10', '0', 1],
            'different lengths alpha 4' => ['0', '10', -1],
            'different lengths alpha 5' => ['9', '10', -1],
            'different lengths alpha 6' => ['10', '9', 1],
            'same length mismatch 1' => ['15', '10', 1],
            'same length mismatch 2' => ['10', '15', -1],
            'same value' => ['10', '10', 0],
            'leading zeros a' => ['0010', '10', 0],
            'leading zeros b' => ['10', '0010', 0],
            'diff len string order differs 9 vs 10' => ['9', '10', -1],
            'diff len string order differs 10 vs 9' => ['10', '9', 1],
            'diff len string order differs 99 vs 100' => ['99', '100', -1],
            'diff len string order differs 100 vs 99' => ['100', '99', 1],
            'kill mutant 1' => ['2', '10', -1],
            'kill mutant 2' => ['10', '2', 1],
        ]);

        it('kills parseDecimalString return value mutants', function (string $input, array $expected) {
            $mock = new DecimalTypeAbstractTest('dummy');
            expect($mock->callParseDecimalString($input))->toBe($expected);
        })->with([
            'empty whole' => ['.5', ['sign' => '', 'whole' => '0', 'fraction' => '5']],
            'empty whole zero' => ['.0', ['sign' => '', 'whole' => '0', 'fraction' => '0']],
            'empty fraction' => ['10', ['sign' => '', 'whole' => '10', 'fraction' => '0']],
            'empty fraction dot' => ['10.', ['sign' => '', 'whole' => '10', 'fraction' => '0']],
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
