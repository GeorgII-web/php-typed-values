<?php

declare(strict_types=1);

namespace PhpTypedValues\Exception\String;

/**
 * Thrown when a language code string violates ISO 639-1 constraints.
 *
 * @psalm-immutable
 */
class LanguageCodeStringTypeException extends StringTypeException
{
}
