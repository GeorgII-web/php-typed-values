<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias;

use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Alias\StringType;

covers(StringType::class);

describe('StringType', function () {
    it('is alias for StringStandard', function () {
        $s = StringType::fromString('test');
        expect($s->value())->toBe('test');
    });

    it('throws exception on fromNull', function () {
        expect(fn() => StringType::fromNull(null))
            ->toThrow(StringTypeException::class, 'StringType type cannot be created from null');
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringType::toNull())
            ->toThrow(StringTypeException::class, 'StringType type cannot be converted to null');
    });
});
