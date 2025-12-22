<?php

declare(strict_types=1);

namespace PhpTypedValues\Base;

use JsonSerializable;
use PhpTypedValues\Base\Shared\isEmptyInterface;
use PhpTypedValues\Base\Shared\isUndefinedInterface;
use PhpTypedValues\Base\Shared\ValueObjectArrayInterface;

/**
 * Base contract for a composite Value object
 * which will be build using this typed values object.
 *
 * @psalm-immutable
 */
interface ValueObjectInterface extends JsonSerializable, ValueObjectArrayInterface, isUndefinedInterface, isEmptyInterface
{
}
