<?php

declare(strict_types=1);

namespace PhpTypedValues\Exception\String;

/**
 * Thrown when a string violates IBAN (International Bank Account Number) format constraints.
 *
 * @psalm-immutable
 */
class IbanStringTypeException extends StringTypeException
{
}
