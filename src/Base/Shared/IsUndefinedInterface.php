<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

/**
 * Base contract for Undefined type check for any typed value object.
 *
 * @psalm-immutable
 */
interface IsUndefinedInterface
{
    /**
     * Returns if the Object value is an Undefined type class.
     */
    public function isUndefined(): bool;
}
