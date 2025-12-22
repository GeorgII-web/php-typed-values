<?php

declare(strict_types=1);

use PhpTypedValues\Bool\Alias\Toggle;

it('is an instance of Toggle and behaves like BoolStandard', function (): void {
    $toggle = Toggle::fromBool(true);

    expect($toggle)->toBeInstanceOf(Toggle::class)
        ->and($toggle->value())->toBeTrue()
        ->and($toggle->toString())->toBe('true');
});
