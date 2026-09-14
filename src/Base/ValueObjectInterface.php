<?php

declare(strict_types=1);

namespace PhpTypedValues\Base;

use JsonSerializable;

/**
 * Base contract for a composite Value object
 * which will be build using this typed values object.
 *
 * @psalm-immutable
 */
interface ValueObjectInterface extends JsonSerializable
{
    /**
     * @return static
     */
    public static function fromArray(array $value);

    /**
     * Returns true if the Object value is empty.
     */
    public function isEmpty(): bool;

    /**
     * Returns if the Object value is an Undefined type class.
     */
    public function isUndefined(): bool;

    public function toArray(): array;
}
