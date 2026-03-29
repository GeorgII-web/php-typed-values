<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\Slug;

covers(Slug::class);

describe('Slug', function () {
    it('Slug::fromString returns Slug instance (late static binding)', function (): void {
        $slug = 'my-awesome-slug';
        $v = Slug::fromString($slug);

        expect($v)->toBeInstanceOf(Slug::class)
            ->and($v::class)->toBe(Slug::class)
            ->and($v->value())->toBe($slug);
    });
});
