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

describe('OptionalFailTest', function () {
    describe('Creation', function () {
        it('constructs from scalars and exposes typed values', function (int $id, ?string $firstName, mixed $height, string $expectedId, string $expectedFirstName, string $expectedHeight) {
            $vo = OptionalFailTest::fromScalars(id: $id, firstName: $firstName, height: $height);

            expect($vo->getId()->toString())->toBe($expectedId)
                ->and($vo->getFirstName()->toString())->toBe($expectedFirstName)
                ->and($vo->getHeight()->toString())->toBe($expectedHeight);
        })->with([
            'standard values' => [1, 'Alice', 170.0, '1', 'Alice', '170.0'],
            'numeric strings' => [2, 'Bob', '42.25', '2', 'Bob', '42.25'],
            'floats with precision' => [3, 'Charlie', 170.5, '3', 'Charlie', '170.5'],
        ]);

        it('checks value() for float heights', function () {
            $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Test', height: 99.0);
            expect($vo->getHeight()->value())->toBe(99.0);
        });

        it('handles optional and invalid inputs as Undefined (late-fail)', function (string|null $firstName, mixed $height, string $getterName) {
            $vo = OptionalFailTest::fromScalars(id: 1, firstName: $firstName, height: $height);
            expect($vo->$getterName())->toBeInstanceOf(Undefined::class);
        })->with([
            'empty firstName' => ['', 10.0, 'getFirstName'],
            'null firstName' => [null, 10.0, 'getFirstName'],
            'null height' => ['Alice', null, 'getHeight'],
            'invalid height string' => ['Alice', 'abc', 'getHeight'],
            'negative height' => ['Alice', -10.0, 'getHeight'],
        ]);

        it('fails early when id is invalid', function () {
            expect(fn() => OptionalFailTest::fromScalars(id: 0, firstName: 'Name', height: 100.0))
                ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
        });

        it('fails on access when height is invalid', function (mixed $height) {
            $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Alice', height: $height);
            expect(fn() => $vo->getHeight()->value())->toThrow(UndefinedTypeException::class);
        })->with([
            'negative' => [-10.0],
            'non-numeric' => ['abc'],
        ]);

        it('mixes valid height and null firstName', function () {
            $obj = OptionalFailTest::fromScalars(id: 1, firstName: null, height: 180.0);
            expect($obj->getFirstName())->toBeInstanceOf(Undefined::class)
                ->and($obj->getHeight())->toBeInstanceOf(FloatPositive::class);
        });
    });

    describe('Serialization', function () {
        it('jsonSerialize returns associative array of strings when all values are present', function () {
            $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Foo', height: 170.0);
            expect($vo->jsonSerialize())
                ->toBe([
                    'id' => '1',
                    'firstName' => 'Foo',
                    'height' => '170.0',
                ]);
        });

        it('jsonSerialize fails when firstName is Undefined (late fail)', function () {
            $vo = OptionalFailTest::fromScalars(id: 1, firstName: '', height: 10.0);
            expect(fn() => $vo->jsonSerialize())
                ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
        });

        it('jsonSerialize fails when height is Undefined (late fail)', function () {
            $vo = OptionalFailTest::fromScalars(id: 1, firstName: 'Name', height: null);
            expect(fn() => $vo->jsonSerialize())
                ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
        });
    });
});
