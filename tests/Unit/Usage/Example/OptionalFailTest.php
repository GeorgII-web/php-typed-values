<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Example of optional/late‑fail semantics.
 *
 * - `id` must be valid at construction time (early fail).
 * - `firstName` uses `tryFromMixed` and may be `Undefined` (late fail on access).
 * - `height` fails early only when provided; `null` becomes `Undefined` (late fail).
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class OptionalFailTest implements JsonSerializable
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {
    }

    /**
     * Factory that supports optional and late‑fail inputs.
     *
     * @param int                   $id        positive integer identifier (validated immediately)
     * @param string|null           $firstName non-empty string or empty/invalid treated as `Undefined`
     * @param string|float|int|null $height    positive numeric value; `null` produces `Undefined`
     *
     * @throws IntegerTypeException
     * @throws FloatTypeException
     */
    public static function fromScalars(
        int $id,
        ?string $firstName,
        string|float|int|null $height = null,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            $height !== null
                ? FloatPositive::tryFromMixed($height) // Early fail for not NULL
                : Undefined::create(), // Late fail for NULL
        );
    }

    /**
     * Returns first name or `Undefined` when the input was empty/invalid.
     */
    public function getFirstName(): StringNonEmpty|Undefined
    {
        return $this->firstName;
    }

    /**
     * Returns height, which may be `Undefined` when it was omitted.
     */
    public function getHeight(): FloatPositive|Undefined
    {
        return $this->height;
    }

    /**
     * Returns validated identifier.
     */
    public function getId(): IntegerPositive
    {
        return $this->id;
    }

    /**
     * Serializes to an associative array of strings.
     *
     * @throws UndefinedTypeException
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'firstName' => $this->firstName->toString(),
            'height' => $this->height->toString(),
        ];
    }
}

it('constructs OptionalFailTest from scalars and exposes typed values', function (): void {
    $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.0);

    expect($vo->getId()->toString())->toBe('1');
    expect($vo->getFirstName()->toString())->toBe('Foobar');
    expect($vo->getHeight()->toString())->toBe('170.0');
});

it('checks string cast', function (): void {
    $voInt = OptionalFailTest::fromScalars(id: 1, firstName: 'Test', height: 99.0);
    expect($voInt->getHeight()->value())->toBe(99.0);
});

it('treats empty firstName as Undefined (late-fail semantics)', function (): void {
    $vo = OptionalFailTest::fromScalars(id: 1, firstName: '', height: 10.0);
    expect($vo->getFirstName())->toBeInstanceOf(Undefined::class);
});

it('fails early when height is negative', function (): void {
    expect(fn() => OptionalFailTest::fromScalars(id: 1, firstName: 'Foobar', height: -10.0)->getHeight()->value())
        ->toThrow(UndefinedTypeException::class);
});

it('accepts int/float/numeric-string heights and preserves string formatting via fromString casting', function (): void {
    $asInt = OptionalFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.0);
    $asFloat = OptionalFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    $asString = OptionalFailTest::fromScalars(id: 1, firstName: 'Foobar', height: '42.25');

    expect($asInt->getHeight())->toBeInstanceOf(FloatPositive::class)
        ->and($asInt->getHeight()->toString())->toBe('170.0')
        ->and($asFloat->getHeight())->toBeInstanceOf(FloatPositive::class)
        ->and($asFloat->getHeight()->toString())->toBe('170.5')
        ->and($asString->getHeight())->toBeInstanceOf(FloatPositive::class)
        ->and($asString->getHeight()->toString())->toBe('42.25');
});

it('treats null height as Undefined (late-fail semantics)', function (): void {
    $obj = OptionalFailTest::fromScalars(id: 1, firstName: 'Foobar', height: null);
    expect($obj->getHeight())->toBeInstanceOf(Undefined::class);
});

it('null firstName produces Undefined via tryFromMixed while height succeeds', function (): void {
    $obj = OptionalFailTest::fromScalars(id: 1, firstName: null, height: 180.0);
    expect($obj->getFirstName())->toBeInstanceOf(Undefined::class)
        ->and($obj->getHeight())->toBeInstanceOf(FloatPositive::class);
});

it('invalid id throws IntegerTypeException with exact message', function (): void {
    expect(fn() => OptionalFailTest::fromScalars(id: 0, firstName: 'Name', height: 100.0))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('non-numeric height string throws FloatTypeException from assertFloatString', function (): void {
    expect(fn() => OptionalFailTest::fromScalars(id: 1, firstName: 'Name', height: 'abc')->getHeight()->value())
        ->toThrow(UndefinedTypeException::class);
});

it('jsonSerialize returns associative array of strings when all values are present', function (): void {
    $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Foo', height: 170.0);
    expect($vo->jsonSerialize())
        ->toBe([
            'id' => '1',
            'firstName' => 'Foo',
            'height' => '170.0',
        ]);
});

it('jsonSerialize fails when firstName is Undefined (late fail)', function (): void {
    $vo = OptionalFailTest::fromScalars(id: 1, firstName: '', height: 10.0);
    expect(fn() => $vo->jsonSerialize())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('jsonSerialize fails when height is Undefined (late fail)', function (): void {
    $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Name', height: null);
    expect(fn() => $vo->jsonSerialize())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});
