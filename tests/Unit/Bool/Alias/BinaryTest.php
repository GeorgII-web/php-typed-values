<?php

declare(strict_types=1);

use PhpTypedValues\Bool\Alias\Binary;

describe('Binary', function () {
    it('is an instance of Binary and behaves like BoolStandard', function (): void {
        $binary = Binary::fromBool(true);

        expect($binary)->toBeInstanceOf(Binary::class)
            ->and($binary->value())->toBeTrue()
            ->and($binary->toString())->toBe('true');
    });
});
