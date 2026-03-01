<?php

declare(strict_types=1);

namespace PhpTypedValues\Exception\String;

/**
 * Thrown when a currency code string violates ISO 4217 constraints.
 *
 * @psalm-immutable
 */
class CurrencyCodeStringTypeException extends StringTypeException
{
}
