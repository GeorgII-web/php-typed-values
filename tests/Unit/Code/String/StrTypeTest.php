<?php

declare(strict_types=1);

use PhpTypedValues\String\StringBasic;

it('fromString returns exact value and toString matches', function (): void {
    $s1 = StringBasic::fromString('hello');
    expect($s1->value())->toBe('hello')
        ->and($s1->toString())->toBe('hello');

    $s2 = StringBasic::fromString('');
    expect($s2->value())->toBe('')
        ->and($s2->toString())->toBe('');
});

it('handles unicode and whitespace transparently', function (): void {
    $unicode = StringBasic::fromString('Привет 🌟');
    expect($unicode->value())->toBe('Привет 🌟')
        ->and($unicode->toString())->toBe('Привет 🌟');

    $ws = StringBasic::fromString('  spaced  ');
    expect($ws->value())->toBe('  spaced  ')
        ->and($ws->toString())->toBe('  spaced  ');
});
