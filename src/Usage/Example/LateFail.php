<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Example of late‑fail semantics using `Undefined` for optional/invalid inputs.
 *
 * - `id` must be valid at construction time (early fail).
 * - `firstName` and `height` accept mixed inputs and may become `Undefined`;
 *   accessing their string/primitive values may fail later.
 *
 * Example
 *  - $p = LateFail::fromScalars(id: 1, firstName: 'Bob', height: '170.5');
 *    $p->getHeight()->toString(); // '170.5'
 *  - $p = LateFail::fromScalars(id: 1, firstName: '', height: null);
 *    $p->getFirstName()->toString(); // late fail (UndefinedTypeException)
 *    $p->getHeight()->toString(); // late fail (UndefinedTypeException)
 *
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
     * Factory that validates only the strictly required field (`id`) early,
     * while optional/mixed inputs for `firstName` and `height` may result in
     * `Undefined` values (late‑fail on access).
     *
     * @param int                   $id        positive integer identifier (validated immediately)
     * @param mixed                 $firstName non-empty string or will become `Undefined`
     * @param string|float|int|null $height    positive numeric or `null` to become `Undefined`
     *
     * @throws IntegerTypeException
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
     * Returns first name or `Undefined`.
     * @return \PhpTypedValues\String\StringNonEmpty|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Returns height, which may be `Undefined`.
     * @return \PhpTypedValues\Float\FloatPositive|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public function getHeight()
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
}
