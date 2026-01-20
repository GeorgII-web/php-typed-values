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
 * Example of optional/late‑fail semantics.
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

it('builds from valid scalars and serializes to expected array', function (): void {
    $obj = WithArraysTest::fromScalars(
        id: 1,
        firstName: 'Alice',
        height: 170.0,
        nickNames: ['User1', 'Admin5'],
    );

    expect($obj->getId()->toString())->toBe('1')
        ->and($obj->getFirstName()->toString())->toBe('Alice')
        ->and($obj->getHeight()->toString())->toBe('170.0');

    $nick = $obj->getNickNames();
    expect($nick)->toBeInstanceOf(ArrayOfObjects::class)
        ->and($nick->toArray())->toBe(['User1', 'Admin5']);

    expect($obj->jsonSerialize())->toBe([
        'id' => '1',
        'firstName' => 'Alice',
        'height' => '170.0',
        'nickNames' => ['User1', 'Admin5'],
    ]);
});

it('handles Undefined for empty firstName and null height (late fail on access)', function (): void {
    $obj = WithArraysTest::fromScalars(id: 1, firstName: '', height: null);

    expect(fn() => $obj->getFirstName()->toString())
        ->toThrow(UndefinedTypeException::class);

    expect(fn() => $obj->getHeight()->toString())
        ->toThrow(UndefinedTypeException::class);

    // jsonSerialize also fails due to Undefineds
    expect(fn() => $obj->jsonSerialize())
        ->toThrow(UndefinedTypeException::class);
});

it('throws on invalid id (non-positive)', function (): void {
    expect(fn() => WithArraysTest::fromScalars(id: 0, firstName: 'X', height: 10.0))
        ->toThrow(IntegerTypeException::class);
});

it('throws on invalid height when provided (non-positive)', function (): void {
    expect(fn() => WithArraysTest::fromScalars(id: 1, firstName: 'X', height: -1.0)->getHeight()->value())
        ->toThrow(UndefinedTypeException::class);
});

it('transforms nickNames to ArrayOfObjects of non-empty strings', function (): void {
    $obj = WithArraysTest::fromScalars(id: 1, firstName: 'Bob', height: 10.0, nickNames: ['n1', 'n2']);
    $nn = $obj->getNickNames();

    expect($nn)->toBeInstanceOf(ArrayOfObjects::class)
        ->and($nn->toArray())->toBe(['n1', 'n2'])
        ->and($nn->isEmpty())->toBeFalse()
        ->and($nn->hasUndefined())->toBeFalse()
        ->and($nn->isUndefined())->toBeFalse();
});
