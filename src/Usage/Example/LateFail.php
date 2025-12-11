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
 *
 * @psalm-immutable
 */
final class LateFail
{
    /**
     * @readonly
     */
    private IntegerPositive $id;
    /**
     * @readonly
     * @var \PhpTypedValues\String\StringNonEmpty|\PhpTypedValues\Undefined\Alias\Undefined
     */
    private $firstName;
    /**
     * @readonly
     * @var \PhpTypedValues\Float\FloatPositive|\PhpTypedValues\Undefined\Alias\Undefined
     */
    private $height;
    /**
     * @param \PhpTypedValues\String\StringNonEmpty|\PhpTypedValues\Undefined\Alias\Undefined $firstName
     * @param \PhpTypedValues\Float\FloatPositive|\PhpTypedValues\Undefined\Alias\Undefined $height
     */
    public function __construct(IntegerPositive $id, $firstName, $height)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->height = $height;
    }

    /**
     * @throws IntegerTypeException
     * @param string|float|int|null $height
     * @param mixed $firstName
     */
    public static function fromScalars(
        int $id,
        $firstName,
        $height
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            FloatPositive::tryFromMixed($height), // Late fail
        );
    }

    /**
     * @return \PhpTypedValues\Float\FloatPositive|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public function getHeight()
    {
        return $this->height;
    }

    public function getId(): IntegerPositive
    {
        return $this->id;
    }

    /**
     * @return \PhpTypedValues\String\StringNonEmpty|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
}
