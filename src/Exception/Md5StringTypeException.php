<?php

declare(strict_types=1);

namespace PhpTypedValues\Exception;

/**
 * Thrown when a string violates MD5 hash format constraints.
 *
 * @psalm-immutable
 */
class Md5StringTypeException extends StringTypeException
{
}
