<?php

namespace PhpTypedValues\Usage\Example;

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

require_once 'vendor/autoload.php';

/**
 * @internal
 *
 * @psalm-internal PhpTypedValues
 */
final readonly class NullableType
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {
    }

    /**
     * @throws IntegerTypeException
     * @throws FloatTypeException
     */
    public static function fromScalars(
        int $id,
        ?string $firstName,
        string|float|int|null $height,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            $height !== null ? FloatPositive::fromString((string) $height) : Undefined::create(), // Late fail for NULL, Early fail for anything else
        );
    }

    public function getHeight(): FloatPositive|Undefined
    {
        return $this->height;
    }

    public function getId(): IntegerPositive
    {
        return $this->id;
    }

    public function getFirstName(): StringNonEmpty|Undefined
    {
        return $this->firstName;
    }
}
