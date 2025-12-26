<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;
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
interface BoolTypeInterface extends PrimitiveTypeInterface
{
    public static function tryFromInt(int $value): static|Undefined;

    /**
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static;

    public function value(): bool;

    public static function fromBool(bool $value): static;
}
