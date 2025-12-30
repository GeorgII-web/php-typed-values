<?php

declare(strict_types=1);

use PhpTypedValues\String\StringStandard;

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
        ->toBe('45.67')
        ->and($fromNull)
        ->toBeInstanceOf(StringStandard::class)
        ->and($fromNull->value())
        ->toBe('')
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
        ->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class)
        ->and($fromObject)
        ->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class)
        ->and($fromError)
        ->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class);
});

it('tryFromMixed handles non-stringable objects and non-scalar types explicitly', function (): void {
    // stdClass is not Stringable and not scalar
    expect(StringStandard::tryFromMixed(new stdClass()))->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class)
        ->and(StringStandard::tryFromMixed([]))->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class)
        ->and(StringStandard::tryFromMixed(\STDOUT))->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class);
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
    /** @psalm-suppress LessSpecificReturnType */
    public static function fromString(string $value): static
    {
        throw new Exception('Simulated error');
    }
}

it('StringStandard::tryFromString returns Undefined when fromString throws', function (): void {
    $result = StringStandardTest::tryFromString('fail');
    expect($result)->toBeInstanceOf(PhpTypedValues\Undefined\Alias\Undefined::class);
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
