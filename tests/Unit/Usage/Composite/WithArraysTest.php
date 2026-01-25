<?php

declare(strict_types=1);

use PhpTypedValues\ArrayType\ArrayOfObjects;
use PhpTypedValues\Exception\ArrayType\ArrayTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Composite of optional/late‑fail semantics.
 *
 * - `id` must be valid at construction time (early fail).
 * - `firstName` uses `tryFromMixed` and may be `Undefined` (late fail on access).
 * - `height` fails early only when provided; `null` becomes `Undefined` (late fail).
 *
 * @template TNickNames of StringNonEmpty
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class WithArraysTest implements JsonSerializable
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
        private ArrayOfObjects $nickNames,
    ) {
    }

    /**
     * Factory that supports optional and late‑fail inputs.
     *
     * @param int                   $id        positive integer identifier (validated immediately)
     * @param string|null           $firstName non-empty string or empty/invalid treated as `Undefined`
     * @param string|float|int|null $height    positive numeric value; `null` produces `Undefined`
     * @param array                 $nickNames list of non-empty strings
     *
     * @throws ArrayTypeException
     * @throws FloatTypeException
     * @throws IntegerTypeException
     * @throws StringTypeException
     */
    public static function fromScalars(
        int $id,
        ?string $firstName,
        string|float|int|null $height = null,
        array $nickNames = [],
    ): self {
        // Make the array of Primitives
        $nickNamesObjects = [];
        foreach ($nickNames as $nickName) {
            $nickNamesObjects[] = StringNonEmpty::fromString($nickName);
        }

        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            FloatPositive::tryFromMixed($height), // Late fail
            ArrayOfObjects::fromItems(...$nickNamesObjects)
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

    public function getNickNames(): ArrayOfObjects
    {
        return $this->nickNames;
    }

    /**
     * Serializes to an associative array of strings.
     *
     * @throws UndefinedTypeException
     * @throws ArrayTypeException
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'firstName' => $this->firstName->toString(),
            'height' => $this->height->toString(),
            'nickNames' => $this->nickNames->toArray(),
        ];
    }
}

describe('WithArraysTest', function () {
    describe('Creation', function () {
        it('builds from valid scalars', function (int $id, ?string $firstName, float $height, array $nickNames, array $expectedNickNames) {
            $obj = WithArraysTest::fromScalars(
                id: $id,
                firstName: $firstName,
                height: $height,
                nickNames: $nickNames,
            );

            expect($obj->getId()->value())->toBe($id)
                ->and($obj->getFirstName()->value())->toBe($firstName)
                ->and($obj->getHeight()->value())->toBe($height)
                ->and($obj->getNickNames()->toArray())->toBe($expectedNickNames);
        })->with([
            'standard values' => [1, 'Alice', 170.0, ['User1', 'Admin5'], ['User1', 'Admin5']],
            'another values' => [2, 'Bob', 10.0, ['n1', 'n2'], ['n1', 'n2']],
        ]);

        it('handles Undefined for empty firstName and null height (late fail on access)', function () {
            $obj = WithArraysTest::fromScalars(id: 1, firstName: '', height: null);

            expect($obj->getFirstName())->toBeInstanceOf(Undefined::class)
                ->and($obj->getHeight())->toBeInstanceOf(Undefined::class);

            expect(fn() => $obj->getFirstName()->toString())->toThrow(UndefinedTypeException::class)
                ->and(fn() => $obj->getHeight()->toString())->toThrow(UndefinedTypeException::class);
        });

        it('throws on invalid id (non-positive) early', function () {
            expect(fn() => WithArraysTest::fromScalars(id: 0, firstName: 'X', height: 10.0))
                ->toThrow(IntegerTypeException::class);
        });

        it('handles invalid height when provided (becomes Undefined, late fail)', function () {
            $obj = WithArraysTest::fromScalars(id: 1, firstName: 'X', height: -1.0);
            expect($obj->getHeight())->toBeInstanceOf(Undefined::class);
            expect(fn() => $obj->getHeight()->value())->toThrow(UndefinedTypeException::class);
        });

        it('throws on invalid nickName', function () {
            expect(fn() => WithArraysTest::fromScalars(id: 1, firstName: 'Bob', height: 10.0, nickNames: ['']))
                ->toThrow(StringTypeException::class);
        });
    });

    describe('Getters and State', function () {
        it('exposes typed values through getters', function () {
            $obj = WithArraysTest::fromScalars(id: 1, firstName: 'Alice', height: 170.5, nickNames: ['n1']);

            expect($obj->getId())->toBeInstanceOf(IntegerPositive::class)
                ->and($obj->getFirstName())->toBeInstanceOf(StringNonEmpty::class)
                ->and($obj->getHeight())->toBeInstanceOf(FloatPositive::class)
                ->and($obj->getNickNames())->toBeInstanceOf(ArrayOfObjects::class);
        });

        it('nickNames collection state', function () {
            $obj = WithArraysTest::fromScalars(id: 1, firstName: 'Bob', height: 10.0, nickNames: ['n1', 'n2']);
            $nn = $obj->getNickNames();

            expect($nn->isEmpty())->toBeFalse()
                ->and($nn->hasUndefined())->toBeFalse()
                ->and($nn->isUndefined())->toBeFalse();
        });
    });

    describe('Serialization', function () {
        it('serializes to expected array when all fields are valid', function () {
            $obj = WithArraysTest::fromScalars(
                id: 1,
                firstName: 'Alice',
                height: 170.0,
                nickNames: ['User1', 'Admin5'],
            );

            $expected = [
                'id' => '1',
                'firstName' => 'Alice',
                'height' => '170.0',
                'nickNames' => ['User1', 'Admin5'],
            ];

            expect($obj->jsonSerialize())->toBe($expected);
        });

        it('jsonSerialize fails if any field is Undefined', function () {
            $obj = WithArraysTest::fromScalars(id: 1, firstName: '', height: null);

            expect(fn() => $obj->jsonSerialize())
                ->toThrow(UndefinedTypeException::class);
        });
    });
});
