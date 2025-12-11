<?php

declare(strict_types=1);

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
final class NullableType
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
     * @throws FloatTypeException
     * @param string|float|int|null $height
     */
    public static function fromScalars(
        int $id,
        ?string $firstName,
        $height
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            $height !== null ? FloatPositive::fromString((string) $height) : Undefined::create(), // Late fail for NULL, Early fail for anything else
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
