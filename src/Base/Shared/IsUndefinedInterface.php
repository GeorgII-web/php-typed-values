<?php

declare(strict_types=1);

namespace PhpTypedValues\Base\Shared;

use PhpTypedValues\Undefined\UndefinedStandard;

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
     * @psalm-assert-if-true UndefinedStandard   $this
     *
     * @psalm-assert-if-false !UndefinedStandard $this
     */
    public function isUndefined(): bool;
}
