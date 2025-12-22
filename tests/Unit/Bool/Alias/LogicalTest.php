<?php

declare(strict_types=1);

use PhpTypedValues\Bool\Alias\Logical;

it('is an instance of Logical and behaves like BoolStandard', function (): void {
    $logical = Logical::fromBool(true);

    expect($logical)->toBeInstanceOf(Logical::class)
        ->and($logical->value())->toBeTrue()
        ->and($logical->toString())->toBe('true');
});
