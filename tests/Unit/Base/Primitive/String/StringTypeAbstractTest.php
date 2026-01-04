<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StringTypeAbstract::class);

/**
 * @internal
 *
 * @covers \PhpTypedValues\Base\Primitive\String\StringTypeAbstract
 */
readonly class StringTypeAbstractTest extends StringTypeAbstract
{
    public function __construct(private string $val)
    {
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function isTypeOf(string ...$classNames): bool
    {
        return true;
    }

    public function value(): string
    {
        return $this->val;
    }

    public function toString(): string
    {
        return $this->val;
    }

    public function jsonSerialize(): string
    {
        return $this->val;
    }

    public function isEmpty(): bool
    {
        return $this->val === '';
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        if ($value === null) {
            return new static('');
        }

        if (\is_string($value) || $value instanceof Stringable || \is_scalar($value)) {
            return new static((string) $value);
        }

        return $default;
    }

    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        return new static($value);
    }
}

it('exercises StrType through a concrete stub', function (): void {
    $strType = new StringTypeAbstractTest('test');

    expect($strType)->toBeInstanceOf(StringTypeAbstract::class)
        ->and($strType->value())->toBe('test')
        ->and($strType->toString())->toBe('test')
        ->and((string) $strType)->toBe('test')
        ->and($strType->jsonSerialize())->toBe('test')
        ->and($strType->isEmpty())->toBeFalse()
        ->and($strType->isUndefined())->toBeFalse();

    $emptyStrType = new StringTypeAbstractTest('');
    expect($emptyStrType->isEmpty())->toBeTrue();
});

it('exercises abstract static methods via stub', function (): void {
    expect(StringTypeAbstractTest::tryFromMixed('hello'))->toBeInstanceOf(StringTypeAbstractTest::class)
        ->and(StringTypeAbstractTest::tryFromMixed('hello')->value())->toBe('hello')
        ->and(StringTypeAbstractTest::tryFromMixed(['invalid']))->toBeInstanceOf(Undefined::class)
        ->and(StringTypeAbstractTest::tryFromMixed(['invalid'], Undefined::create()))->toBeInstanceOf(Undefined::class)
        ->and(StringTypeAbstractTest::tryFromString('world'))->toBeInstanceOf(StringTypeAbstractTest::class)
        ->and(StringTypeAbstractTest::tryFromString('world')->value())->toBe('world')
        ->and(StringTypeAbstractTest::fromString('hello'))->toBeInstanceOf(StringTypeAbstractTest::class);
});

it('__toString proxies to toString for StrType', function (): void {
    $v = new StringStandard('abc');

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('abc');
});

it('fromString returns exact value and toString matches', function (): void {
    $s1 = StringStandard::fromString('hello');
    expect($s1->value())->toBe('hello')
        ->and($s1->toString())->toBe('hello');

    $s2 = StringStandard::fromString('');
    expect($s2->value())->toBe('')
        ->and($s2->toString())->toBe('');
});

it('handles unicode and whitespace transparently', function (): void {
    $unicode = StringStandard::fromString('hi 🌟');
    expect($unicode->value())->toBe('hi 🌟')
        ->and($unicode->toString())->toBe('hi 🌟');

    $ws = StringStandard::fromString('  spaced  ');
    expect($ws->value())->toBe('  spaced  ')
        ->and($ws->toString())->toBe('  spaced  ');
});
