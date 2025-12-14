<?php

declare(strict_types=1);

namespace Abstract\Primitive;

use PhpTypedValues\Abstract\Primitive\PrimitiveType;
use PhpTypedValues\Exception\TypeException;
use stdClass;
use Stringable;

use function is_resource;

abstract readonly class PrimitiveTypeTest extends PrimitiveType
{
    public static function convert(mixed $value): string
    {
        return self::convertMixedToString($value);
    }
}

it('convertMixedToString casts scalars and null to string', function (): void {
    expect(PrimitiveTypeTest::convert(123))->toBe('123')
        ->and(PrimitiveTypeTest::convert(1.5))->toBe('1.5')
        ->and(PrimitiveTypeTest::convert('foo'))->toBe('foo')
        ->and(PrimitiveTypeTest::convert(true))->toBe('1')
        ->and(PrimitiveTypeTest::convert(false))->toBe('')
        ->and(PrimitiveTypeTest::convert(null))->toBe('');
});

it('convertMixedToString accepts Stringable objects', function (): void {
    $obj = new class implements Stringable {
        public function __toString(): string
        {
            return '3.5';
        }
    };

    expect(PrimitiveTypeTest::convert($obj))->toBe('3.5');
});

it('convertMixedToString throws TypeException for non-stringable objects, arrays and resources', function (): void {
    // array
    expect(fn() => PrimitiveTypeTest::convert(['x']))
        ->toThrow(TypeException::class, 'Value cannot be cast to string');

    // stdClass (non-stringable)
    expect(fn() => PrimitiveTypeTest::convert(new stdClass()))
        ->toThrow(TypeException::class, 'Value cannot be cast to string');

    // resource
    $res = fopen('php://memory', 'r');
    try {
        expect(fn() => PrimitiveTypeTest::convert($res))
            ->toThrow(TypeException::class, 'Value cannot be cast to string');
    } finally {
        is_resource($res) && fclose($res);
    }
});
