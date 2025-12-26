<?php

declare(strict_types=1);

namespace PhpTypedValues\String\MariaDb;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

/**
 * MariaDB VARCHAR(255) string.
 *
 * Accepts any string with length up to 255 characters (mb_strlen based). The
 * original string is preserved on success; longer values are rejected.
 *
 * Example
 *  - $v = StringVarChar255::fromString('Hello world');
 *    $v->toString(); // 'Hello world'
 *  - StringVarChar255::fromString(str_repeat('x', 256)); // throws StringTypeException
 *
 * @method        false        isUndefined()
 * @method        string       value()
 * @method        bool         isEmpty()
 * @method        string       toString()
 * @method static static|mixed tryFromString(string $value, mixed $default = null)
 * @method static static|mixed tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
readonly class StringVarChar255 extends StrType
{
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if (mb_strlen($value) > 255) {
            throw new StringTypeException('String is too long, max 255 chars allowed');
        }

        $this->value = $value;
    }

    /**
     * @throws StringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
