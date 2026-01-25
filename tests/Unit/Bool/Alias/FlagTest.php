<?php

declare(strict_types=1);

use PhpTypedValues\Bool\Alias\Flag;

describe('Flag', function () {
    it('is an instance of Flag and behaves like BoolStandard', function (): void {
        $flag = Flag::fromBool(true);

        expect($flag)->toBeInstanceOf(Flag::class)
            ->and($flag->value())->toBeTrue()
            ->and($flag->toString())->toBe('true');
    });
});
