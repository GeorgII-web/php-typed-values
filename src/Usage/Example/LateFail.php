<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @psalm-internal PhpTypedValues
 * @psalm-immutable
 */
final readonly class LateFail
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {
    }

    /**
     * @throws IntegerTypeException
     */
    public static function fromScalars(
        int $id,
        mixed $firstName,
        string|float|int|null $height,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            FloatPositive::tryFromMixed($height), // Late fail
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
