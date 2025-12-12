<?php

declare(strict_types=1);

use PhpTypedValues\Abstract\AbstractType;
use PhpTypedValues\Exception\TypeException;

abstract readonly class AbstractTypeTest extends AbstractType
{
    public static function convert(mixed $value): string
    {
        return self::convertMixedToString($value);
    }
}

it('convertMixedToString casts scalars and null to string', function (): void {
    expect(AbstractTypeTest::convert(123))->toBe('123')
        ->and(AbstractTypeTest::convert(1.5))->toBe('1.5')
        ->and(AbstractTypeTest::convert('foo'))->toBe('foo')
        ->and(AbstractTypeTest::convert(true))->toBe('1')
        ->and(AbstractTypeTest::convert(false))->toBe('')
        ->and(AbstractTypeTest::convert(null))->toBe('');
});

it('convertMixedToString accepts Stringable objects', function (): void {
    $obj = new class implements Stringable {
        public function __toString(): string
        {
            return '3.5';
        }
    };

    expect(AbstractTypeTest::convert($obj))->toBe('3.5');
});

it('convertMixedToString throws TypeException for non-stringable objects, arrays and resources', function (): void {
    // array
    expect(fn() => AbstractTypeTest::convert(['x']))
        ->toThrow(TypeException::class, 'Value cannot be cast to string');

    // stdClass (non-stringable)
    expect(fn() => AbstractTypeTest::convert(new stdClass()))
        ->toThrow(TypeException::class, 'Value cannot be cast to string');

    // resource
    $res = fopen('php://memory', 'r');
    try {
        expect(fn() => AbstractTypeTest::convert($res))
            ->toThrow(TypeException::class, 'Value cannot be cast to string');
    } finally {
        \is_resource($res) && fclose($res);
    }
});
