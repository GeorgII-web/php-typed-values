<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function mb_strlen;

/**
 * MariaDB TEXT string (up to 65,535 characters).
 *
 * Accepts any string including empty, as long as its length measured by
 * mb_strlen() is not greater than 65,535 characters.
 *
 * Example
 *  - $t = StringText::fromString('lorem ipsum');
 *    $t->toString(); // 'lorem ipsum'
 *  - StringText::fromString(str_repeat('x', 65536)); // throws StringTypeException
 *
 * @psalm-immutable
 */
class StringText extends StrType
{
    /**
     * @readonly
     */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if (mb_strlen($value) > 65535) {
            throw new StringTypeException('String is too long, max 65535 chars allowed');
        }

        $this->value = $value;
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @throws StringTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }
}
