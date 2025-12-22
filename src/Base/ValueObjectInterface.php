<?php

declare(strict_types=1);

namespace PhpTypedValues\Base;

use JsonSerializable;
use PhpTypedValues\Base\Shared\FromArray;
use PhpTypedValues\Base\Shared\IsEmptyInterface;
use PhpTypedValues\Base\Shared\IsUndefinedInterface;
use PhpTypedValues\Base\Shared\ToArray;

/**
 * Base contract for a composite Value object
 * which will be build using this typed values object.
 *
 * @psalm-immutable
 */
interface ValueObjectInterface extends JsonSerializable, IsUndefinedInterface, IsEmptyInterface, ToArray, FromArray
{
}
