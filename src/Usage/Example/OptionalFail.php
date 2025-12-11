<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

use JsonSerializable;
use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

require_once 'vendor/autoload.php';

/**
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class OptionalFail implements JsonSerializable
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
        string|float|int|null $height = null,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            $height !== null
                ? FloatPositive::fromString((string) $height) // Early fail for not NULL
                : Undefined::create(), // Late fail for NULL
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

    /**
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
