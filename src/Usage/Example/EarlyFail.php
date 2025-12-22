<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use PhpTypedValues\Base\ValueObjectInterface;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;

/**
 * Example of strict early‑fail semantics for constructing a composite value.
 *
 * All fields must be valid on creation time. Any invalid input immediately
 * raises a domain exception from the underlying typed values.
 *
 * Example
 *  - $p = EarlyFail::fromScalars(id: 1, firstName: 'Alice', height: 170.5);
 *    $p->getFirstName()->toString(); // 'Alice'
 *  - EarlyFail::fromScalars(id: 0, firstName: 'Alice', height: 170); // throws IntegerTypeException
 *  - EarlyFail::fromScalars(id: 1, firstName: '', height: 170); // throws StringTypeException
 *  - EarlyFail::fromScalars(id: 1, firstName: 'Alice', height: -1); // throws FloatTypeException
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class EarlyFail implements ValueObjectInterface
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty $firstName,
        private FloatPositive $height,
    ) {
    }

    /**
     * Factory that validates all inputs and fails immediately on invalid data.
     *
     * @param int    $id        positive integer identifier
     * @param string $firstName non-empty person name
     * @param float  $height    positive height value
     *
     * @throws IntegerTypeException
     * @throws FloatTypeException
     * @throws StringTypeException
     */
    public static function fromScalars(
        int $id,
        string $firstName,
        float $height,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::fromString($firstName), // Early fail
            FloatPositive::fromFloat($height), // Early fail
        );
    }

    /**
     * Returns validated height value.
     */
    public function getHeight(): FloatPositive
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
     * Returns validated first name.
     */
    public function getFirstName(): StringNonEmpty
    {
        return $this->firstName;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'firstName' => $this->firstName->value(),
            'height' => $this->height->value(),
        ];
    }

    public static function fromArray(array $value): self
    {
        return new self(
            IntegerPositive::fromInt($value['id']),
            StringNonEmpty::fromString($value['firstName']),
            FloatPositive::fromFloat($value['height']),
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
