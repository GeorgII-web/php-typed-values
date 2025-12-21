<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

use JsonSerializable;
use PhpTypedValues\ArrayType\ArrayOfObjects;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

require_once 'vendor/autoload.php';

/**
 * Example of optional/late‑fail semantics.
 *
 * - `id` must be valid at construction time (early fail).
 * - `firstName` uses `tryFromMixed` and may be `Undefined` (late fail on access).
 * - `height` fails early only when provided; `null` becomes `Undefined` (late fail).
 *
 * Example
 *  - $p = OptionalFail::fromScalars(id: 1, firstName: 'Alice', height: 170);
 *    $p->jsonSerialize(); // ['id' => '1', 'firstName' => 'Alice', 'height' => '170']
 *  - $p = OptionalFail::fromScalars(id: 1, firstName: '', height: null);
 *    $p->getFirstName()->toString(); // late fail (UndefinedTypeException)
 *    $p->getHeight()->toString(); // late fail (UndefinedTypeException)
 *
 * @template TNickNames of StringNonEmpty
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class WithArrays implements JsonSerializable
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
            $height !== null
                ? FloatPositive::fromString((string) $height) // Early fail for not NULL
                : Undefined::create(), // Late fail for NULL
            ArrayOfObjects::fromArray($nickNamesObjects)
        );
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
     * Returns first name or `Undefined` when the input was empty/invalid.
     */
    public function getFirstName(): StringNonEmpty|Undefined
    {
        return $this->firstName;
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
