<?php

declare(strict_types=1);

use PhpTypedValues\Abstract\AbstractType;
use PhpTypedValues\Exception\TypeException;

it('convertMixedToString casts scalars and null to string', function (): void {
    expect(AbstractType::convertMixedToString(123))->toBe('123')
        ->and(AbstractType::convertMixedToString(1.5))->toBe('1.5')
        ->and(AbstractType::convertMixedToString('foo'))->toBe('foo')
        ->and(AbstractType::convertMixedToString(true))->toBe('1')
        ->and(AbstractType::convertMixedToString(false))->toBe('')
        ->and(AbstractType::convertMixedToString(null))->toBe('');
});

it('convertMixedToString accepts Stringable objects', function (): void {
    $obj = new class implements Stringable {
        public function __toString(): string
        {
            return '3.5';
        }
    };

    expect(AbstractType::convertMixedToString($obj))->toBe('3.5');
});

it('convertMixedToString throws TypeException for non-stringable objects, arrays and resources', function (): void {
    // array
    expect(fn() => AbstractType::convertMixedToString(['x']))
        ->toThrow(TypeException::class, 'Value cannot be cast to string');

    // stdClass (non-stringable)
    expect(fn() => AbstractType::convertMixedToString(new stdClass()))
        ->toThrow(TypeException::class, 'Value cannot be cast to string');

    // resource
    $res = fopen('php://memory', 'r');
    try {
        expect(fn() => AbstractType::convertMixedToString($res))
            ->toThrow(TypeException::class, 'Value cannot be cast to string');
    } finally {
        \is_resource($res) && fclose($res);
    }
});
