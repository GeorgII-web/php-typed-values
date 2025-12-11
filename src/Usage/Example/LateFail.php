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
final readonly class LateFail
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {
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
        mixed $firstName,
        string|float|int|null $height,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            FloatPositive::tryFromMixed($height), // Late fail
        );
    }

    /**
     * Returns height, which may be `Undefined`.
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
     * Returns first name or `Undefined`.
     */
    public function getFirstName(): StringNonEmpty|Undefined
    {
        return $this->firstName;
    }
}
