<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\Bool;

use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Contract for boolean typed values.
 *
 * Describes the API that all bool-backed value objects must implement,
 * including factories, accessors and formatting helpers.
 *
 * Example
 *  - $v = MyBoolean::fromString('true');
 *  - $v->value();      // true
 *  - (string) $v;      // "true"
 *
 * @psalm-immutable
 */
interface BoolTypeInterface
{
    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value);

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     * @param mixed $value
     */
    public static function tryFromMixed($value);

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromInt(int $value);

    public function value(): bool;

    /**
     * @return static
     */
    public static function fromBool(bool $value);
}
