<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Base contract for Undefined type check for any typed value object.
 *
 * @psalm-immutable
 */
interface IsUndefinedInterface
{
    /**
     * Returns if the Object value is an Undefined type class.
     *
     * @psalm-assert-if-true Undefined $this
     *
     * @psalm-assert-if-false !Undefined $this
     */
    public function isUndefined(): bool;
}
