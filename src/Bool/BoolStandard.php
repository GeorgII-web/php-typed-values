<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Abstract\Bool\BoolType;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\UndefinedStandard;

use function sprintf;

/**
 * Boolean value.
 *
 * Example "true"
 *
 * @psalm-immutable
 */
readonly class BoolStandard extends BoolType
{
    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public static function tryFromString(string $value): self|UndefinedStandard
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return UndefinedStandard::create();
        }
    }

    public static function tryFromInt(int $value): self|UndefinedStandard
    {
        try {
            return static::fromInt($value);
        } catch (TypeException) {
            return UndefinedStandard::create();
        }
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromString(string $value): static
    {
        $lowerCaseValue = strtolower(trim($value));

        if ($lowerCaseValue === 'true' || $lowerCaseValue === '1' || $lowerCaseValue === 'yes' || $lowerCaseValue === 'on' || $lowerCaseValue === 'y') {
            $boolValue = true;
        } elseif ($lowerCaseValue === 'false' || $lowerCaseValue === '0' || $lowerCaseValue === 'no' || $lowerCaseValue === 'off' || $lowerCaseValue === 'n') {
            $boolValue = false;
        } else {
            throw new BoolTypeException(sprintf('Expected string "true" or "false", got "%s"', $value));
        }

        return new static($boolValue);
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static
    {
        if ($value === 1) {
            $boolValue = true;
        } elseif ($value === 0) {
            $boolValue = false;
        } else {
            throw new BoolTypeException(sprintf('Expected int "1" or "0", got "%s"', $value));
        }

        return new static($boolValue);
    }

    public function value(): bool
    {
        return $this->value;
    }
}
