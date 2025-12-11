<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;

/**
 * @internal
 *
 * @psalm-internal PhpTypedValues
 */
final class StrictType
{
    /**
     * @readonly
     */
    private IntegerPositive $id;
    /**
     * @readonly
     */
    private StringNonEmpty $firstName;
    /**
     * @readonly
     */
    private FloatPositive $height;
    public function __construct(IntegerPositive $id, StringNonEmpty $firstName, FloatPositive $height)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->height = $height;
    }

    /**
     * @throws IntegerTypeException
     * @throws FloatTypeException
     * @throws StringTypeException
     */
    public static function fromScalars(
        int $id,
        string $firstName,
        float $height
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::fromString($firstName), // Early fail
            FloatPositive::fromFloat($height), // Early fail
        );
    }

    public function getHeight(): FloatPositive
    {
        return $this->height;
    }

    public function getId(): IntegerPositive
    {
        return $this->id;
    }

    public function getFirstName(): StringNonEmpty
    {
        return $this->firstName;
    }
}
