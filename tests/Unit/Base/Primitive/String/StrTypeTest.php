<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StrType::class);

/**
 * @internal
 *
 * @covers \PhpTypedValues\Base\Primitive\String\StrType
 */
readonly class StrTypeTest extends StrType
{
    public function __construct(private string $val)
    {
    }

    public static function tryFromMixed(mixed $value, mixed $default = new Undefined()): mixed
    {
        return $default;
    }

    public static function tryFromString(string $value, mixed $default = new Undefined()): mixed
    {
        return $default;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->val;
    }

    public function toString(): string
    {
        return $this->val;
    }

    public function __toString(): string
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
}

it('exercises StrType through a concrete stub', function (): void {
    $strType = new StrTypeTest('test');

    expect($strType)->toBeInstanceOf(StrType::class);
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
    $unicode = StringStandard::fromString('Привет 🌟');
    expect($unicode->value())->toBe('Привет 🌟')
        ->and($unicode->toString())->toBe('Привет 🌟');

    $ws = StringStandard::fromString('  spaced  ');
    expect($ws->value())->toBe('  spaced  ')
        ->and($ws->toString())->toBe('  spaced  ');
});
