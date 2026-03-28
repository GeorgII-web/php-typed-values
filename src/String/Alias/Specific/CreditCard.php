<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Alias\Specific;

use PhpTypedValues\String\Specific\StringCreditCard;

/**
 * Alias for credit card number string.
 *
 * Provides the same behavior as StringCreditCard while exposing a concise
 * name suitable for APIs that prefer "CreditCard".
 *
 * Example
 *  - $c = CreditCard::fromString('4111111111111111');
 *    $c->toString(); // "4111111111111111"
 *
 * @psalm-immutable
 */
final readonly class CreditCard extends StringCreditCard
{
}
