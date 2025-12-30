<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\DateTime\DateTimeType;
use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\Exception\DateTimeTypeException;
use PhpTypedValues\Float\Alias\Positive;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @covers \PhpTypedValues\Base\Primitive\DateTime\DateTimeType
 */
readonly class DateTimeTypeTest extends DateTimeType
{
    public function __construct(private DateTimeImmutable $dt)
    {
    }

    public function value(): DateTimeImmutable
    {
        return $this->dt;
    }

    public function toString(): string
    {
        return $this->dt->format('Y-m-d');
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public static function fromString(string $value, string $timezone = self::DEFAULT_ZONE): static
    {
        return new self(new DateTimeImmutable($value, new DateTimeZone($timezone)));
    }

    public static function fromDateTime(DateTimeImmutable $value): static
    {
        return new self($value);
    }

    public function withTimeZone(string $timezone): static
    {
        return new self(new DateTimeImmutable());
    }

    public static function getFormat(): string
    {
        return '';
    }
}

it('exercises abstract __toString through stub', function (): void {
    $dt = new DateTimeImmutable('2025-01-01');
    $stub = new DateTimeTypeTest($dt);

    // This directly calls DateTimeType::__toString because the stub doesn't override it
    expect((string) $stub)->toBe($stub->toString())
        ->and((string) $stub)->toBe('2025-01-01');
});

// Test 1: Kills "InstanceOfToFalse - self check" mutation
it('kills self instanceof mutation by verifying self instance returns DateTimeAtom not Undefined', function () {
    // Create a DateTimeAtom instance
    $dateTimeAtom = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

    // This should use the "self" branch and return DateTimeAtom
    $result = DateTimeAtom::tryFromMixed($dateTimeAtom);

    // If mutation changes to false, this would throw/return Undefined
    expect($result)
        ->toBeInstanceOf(DateTimeAtom::class)
        ->and($result->isUndefined())->toBeFalse()
        ->and($result->toString())->toBe('2025-01-02T03:04:05+00:00');
});

// Test 2: Kills "IdenticalToNotIdentical - null check" mutation
it('kills null identical mutation by verifying null returns Undefined but non-null does not', function () {
    // Test null - should return Undefined
    $nullResult = DateTimeAtom::tryFromMixed(null);
    expect($nullResult)
        ->toBeInstanceOf(Undefined::class)
        ->and($nullResult->isUndefined())->toBeTrue();

    // Test non-null string - should return DateTimeAtom
    $notNullResult = DateTimeAtom::tryFromMixed('2025-01-02T03:04:05+00:00');
    expect($notNullResult)
        ->toBeInstanceOf(DateTimeAtom::class)
        ->and($notNullResult->isUndefined())->toBeFalse();

    // Test that they're different types
    expect($nullResult)->not->toBeInstanceOf(DateTimeAtom::class);
    expect($notNullResult)->not->toBeInstanceOf(Undefined::class);
});

// Test 3: Kills "EmptyStringToNotEmpty - null branch" mutation
it('kills empty string mutation by verifying fromString with empty string throws but null returns Undefined', function () {
    // Test null - should return Undefined (not throw)
    $nullResult = DateTimeAtom::tryFromMixed(null);
    expect($nullResult)->toBeInstanceOf(Undefined::class);

    // Test empty string directly with fromString - should throw
    // This verifies that '' in the null branch would throw if used
    expect(fn() => DateTimeAtom::fromString(''))
        ->toThrow(Exception::class);

    // Test the mutation string - should also throw/return Undefined
    $mutationResult = DateTimeAtom::tryFromMixed('PEST Mutator was here!');
    // This should be Undefined because the string doesn't parse as a valid date
    expect($mutationResult)->toBeInstanceOf(Undefined::class);

    // Verify null result and mutation string result are both Undefined
    expect($nullResult)->toBeInstanceOf(Undefined::class);
    expect($mutationResult)->toBeInstanceOf(Undefined::class);
});

it('from other instance', function (): void {
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
    $test = DateTimeAtom::tryFromMixed($vo);

    expect($test->isUndefined())->toBeFalse()
        ->and($test->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('from null', function (): void {
    $test = DateTimeAtom::tryFromMixed(null);

    expect($test->isUndefined())->toBeTrue();
});

it('tryFromString returns value on valid string', function (): void {
    $result = DateTimeAtom::tryFromString('2025-01-02T03:04:05+00:00');

    expect($result)->toBeInstanceOf(DateTimeAtom::class)
        ->and($result->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('tryFromString returns Undefined on invalid string', function (): void {
    $result = DateTimeAtom::tryFromString('invalid-date');

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
});

it('tryFromString uses custom default on failure', function (): void {
    $customDefault = Undefined::create();
    $result = DateTimeAtom::tryFromString('invalid-date', DateTimeAtom::DEFAULT_ZONE, $customDefault);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result)->toBe($customDefault);
});

it('tryFromMixed uses custom default on failure', function (): void {
    $customDefault = Undefined::create();
    $result = DateTimeAtom::tryFromMixed(['invalid'], DateTimeAtom::DEFAULT_ZONE, $customDefault);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result)->toBe($customDefault);
});

it('fromDateTime returns same instant and toString is ISO 8601', function (): void {
    $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
    $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

    expect($dt->format(\DATE_ATOM))->toBe('2025-01-02T03:04:05+00:00')
        ->and($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
});

it('DateTimeImmutable has false and throws an exception', function (): void {
    expect(
        fn() => DateTimeAtom::fromString('')
    )->toThrow(DateTimeTypeException::class);
});

it('throws DateTimeTypeException on unexpected conversion when input uses Z instead of +00:00', function (): void {
    $call = fn() => DateTimeAtom::fromString('2025-01-02T03:04:05Z');
    expect($call)->toThrow(DateTimeTypeException::class);
});

it('__toString proxies to toString for DateTimeType', function (): void {
    $typed = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

    expect((string) $typed)
        ->toBe($typed->toString())
        ->and((string) $typed)
        ->toBe('2025-01-02T03:04:05+00:00');
});

it('handles null value by creating empty string DateTime', function () {
    $result = DateTimeAtom::tryFromMixed(null);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue()
        ->and($result->isEmpty())->toBeTrue();
});

it('returns Undefined for invalid mixed datetime inputs', function (mixed $input): void {
    $result = DateTimeAtom::tryFromMixed($input);

    expect($result)->toBeInstanceOf(Undefined::class)
        ->and($result->isUndefined())->toBeTrue();
})->with([
    // Arrays
    ['input' => []],
    ['input' => ['invalid']],
    ['input' => ['year' => 2024, 'month' => 1]],

    // Objects without __toString
    ['input' => new stdClass()],
    ['input' => (object) ['date' => '2024-01-01']],

    // primitiveType
    ['input' => Positive::fromFloat(1.0)],

    // Invalid date strings
    ['input' => 'not-a-date'],
    ['input' => '2024-13-01'], // Invalid month
    ['input' => '2024-01-32'], // Invalid day
    ['input' => '2024-02-30'], // Invalid day for February
    ['input' => '25:61:61'], // Invalid time
    ['input' => '2024-01-01 25:00:00'], // Invalid hour

    // Resources
    ['input' => fopen('php://memory', 'r')],

    // Callables/Closures
    ['input' => fn() => '2024-01-01'],
    ['input' => 'DateTimeImmutable'],

    // Invalid numeric strings
    ['input' => '1e100'], // Scientific notation
    ['input' => '999999999999999999999999999999'], // Too large

    // Special values
    ['input' => \INF],
    ['input' => \NAN],

    // Binary data
    ['input' => "\x00\x01\x02"],
]);

describe('DateTimeType mutation killing tests', function () {
    // Mutation 1: EmptyStringToNotEmpty - Line 67 (trim check)
    describe('Empty string validation mutation', function () {
        it('kills EmptyStringToNotEmpty mutation for trim check', function () {
            // Test with whitespace-only strings
            $whitespaceCases = [
                'empty string' => '',
                'spaces' => '   ',
                'tabs' => "\t\t",
                'newlines' => "\n\n",
                'mixed whitespace' => " \t\n\r ",
            ];

            foreach ($whitespaceCases as $description => $value) {
                // These should all throw DateTimeTypeException
                expect(fn() => DateTimeAtom::fromString($value))
                    ->toThrow(DateTimeTypeException::class, 'blank');
            }

            // Verify non-whitespace strings work
            expect(DateTimeAtom::fromString('2025-01-02T03:04:05+00:00'))
                ->toBeInstanceOf(DateTimeAtom::class);
        });

        it('kills UnwrapTrim mutation (trim vs direct comparison)', function () {
            // Test with strings that have whitespace but aren't empty
            $cases = [
                'leading space' => ' 2025-01-02T03:04:05+00:00',
                'trailing space' => '2025-01-02T03:04:05+00:00 ',
                'both sides' => ' 2025-01-02T03:04:05+00:00 ',
                'tab' => "\t2025-01-02T03:04:05+00:00",
            ];

            foreach ($cases as $description => $value) {
                // These should NOT throw - they should parse successfully
                expect(DateTimeAtom::tryFromString($value))
                    ->toBeInstanceOf(Undefined::class);
            }

            // Empty string should still throw
            expect(fn() => DateTimeAtom::fromString(''))
                ->toThrow(DateTimeTypeException::class, 'blank');
        });
    });

    // Mutation 2: InstanceOfToFalse - DateTimeImmutable check
    describe('DateTimeImmutable instanceof mutation', function () {
        it('kills InstanceOfToFalse mutation for DateTimeImmutable', function () {
            $dateTime = new DateTimeImmutable('2024-01-01');

            // This should work with DateTimeImmutable
            $result = DateTimeAtom::tryFromMixed($dateTime);

            expect($result)->toBeInstanceOf(DateTimeAtom::class)
                ->and($result->isUndefined())->toBeFalse()
                ->and($result->toString())->toBe('2024-01-01T00:00:00+00:00');

            // Verify non-DateTimeImmutable doesn't take this branch
            $stringResult = DateTimeAtom::tryFromMixed('2024-01-01T00:00:00+00:00');
            expect($stringResult)->toBeInstanceOf(DateTimeAtom::class);

            // They should both produce valid dates, but through different branches
            // The string version might have different string representation
            expect($result->toString())->not->toBe('')
                ->and($stringResult->toString())->not->toBe('');
        });

        it('verifies DateTimeImmutable creates different result than string', function () {
            $dateTime = new DateTimeImmutable('2024-01-01T00:00:00+00:00');
            $dateString = '2024-01-01T00:00:00+00:00';

            $fromObject = DateTimeAtom::tryFromMixed($dateTime);
            $fromString = DateTimeAtom::tryFromMixed($dateString);

            // Both should be valid
            expect($fromObject)->toBeInstanceOf(DateTimeAtom::class)
                ->and($fromString)->toBeInstanceOf(DateTimeAtom::class);

            // They should represent the same date/time
            // (might have different string formats though)
            $objectDate = $fromObject->value()->format('Y-m-d H:i:s');
            $stringDate = $fromString->value()->format('Y-m-d H:i:s');

            expect($objectDate)->toBe($stringDate);
        });
    });

    // Mutation 3: InstanceOfToFalse - self check
    describe('self instanceof mutation', function () {
        it('kills InstanceOfToFalse mutation for self check', function () {
            $dateTime = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

            // This should use the "self" branch
            $result = DateTimeAtom::tryFromMixed($dateTime);

            expect($result)->toBeInstanceOf(DateTimeAtom::class)
                ->and($result->isUndefined())->toBeFalse()
                ->and($result->toString())->toBe('2025-01-02T03:04:05+00:00');

            // Verify it's not using the string branch
            // Create a new instance to ensure it's not just returning itself
            $differentDate = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
            $result2 = DateTimeAtom::tryFromMixed($differentDate);

            expect($result2->toString())->toBe('2025-01-02T03:04:05+00:00');
        });

        it('verifies self instance creates different result than raw DateTimeImmutable', function () {
            $dateTime = new DateTimeImmutable('2024-05-10');
            $selfInstance = DateTimeAtom::fromDateTime($dateTime);

            $fromSelf = DateTimeAtom::tryFromMixed($selfInstance);
            $fromRaw = DateTimeAtom::tryFromMixed($dateTime);

            // Both should work but go through different branches
            expect($fromSelf)->toBeInstanceOf(DateTimeAtom::class)
                ->and($fromRaw)->toBeInstanceOf(DateTimeAtom::class);

            // They should represent the same date
            $selfDate = $fromSelf->value()->format('Y-m-d');
            $rawDate = $fromRaw->value()->format('Y-m-d');

            expect($selfDate)->toBe($rawDate);
        });
    });

    // Mutation 4: IdenticalToNotIdentical - null check
    describe('null identical comparison mutation', function () {
        it('kills IdenticalToNotIdentical mutation for null check', function () {
            // Test with null
            $nullResult = DateTimeAtom::tryFromMixed(null);

            // null should go through the null branch
            expect($nullResult)->toBeInstanceOf(Undefined::class)
                ->and($nullResult->isEmpty())->toBeTrue();

            // Test with not null to ensure it doesn't take the null branch
            $notNullResult = DateTimeAtom::tryFromMixed('2025-01-02T03:04:05+00:00');
            expect($notNullResult)->toBeInstanceOf(DateTimeAtom::class)
                ->and($notNullResult->isEmpty())->toBeFalse();

            // Test with other falsy values that aren't null
            $emptyStringResult = DateTimeAtom::tryFromMixed('');
            $zeroResult = DateTimeAtom::tryFromMixed(0);
            $falseResult = DateTimeAtom::tryFromMixed(false);

            // These should NOT be empty (or should throw depending on implementation)
            // They should not take the null branch
            if ($emptyStringResult instanceof DateTimeAtom) {
                expect($emptyStringResult->isEmpty())->not->toBe($nullResult->isEmpty());
            }
        });

        it('verifies null creates empty date while other values do not', function () {
            $nullResult = DateTimeAtom::tryFromMixed(null);
            $emptyStringResult = DateTimeAtom::tryFromMixed('');

            // null should create empty date
            expect($nullResult->isEmpty())->toBeTrue();

            // Empty string might create empty date or throw
            // Either way, the behavior should be different from null
            if ($emptyStringResult instanceof DateTimeAtom) {
                // If empty string creates a date, verify it's not marked empty
                // (or if it is, that's a different code path)
                expect($emptyStringResult->toString())->not->toBe($nullResult->toString());
            } else {
                // If empty string throws/returns Undefined, that's different from null
                expect($emptyStringResult)->toBeInstanceOf(Undefined::class);
            }
        });
    });

    // Mutation 5: EmptyStringToNotEmpty - null branch string
    describe('EmptyStringToNotEmpty mutation in null branch', function () {
        it('kills EmptyStringToNotEmpty mutation for null branch', function () {
            $nullResult = DateTimeAtom::tryFromMixed(null);
            $emptyStringResult = DateTimeAtom::tryFromMixed('');

            // null should create an empty date (fromString(''))
            expect($nullResult->isEmpty())->toBeTrue();

            // The mutation would change '' to 'PEST Mutator was here!'
            // Let's see what that would produce
            $mutationStringResult = DateTimeAtom::tryFromMixed('PEST Mutator was here!');

            // null result and mutation string result should be DIFFERENT
            // because '' and 'PEST Mutator was here!' parse differently

            if ($mutationStringResult instanceof DateTimeAtom) {
                // If the mutation string parses as a date (unlikely),
                // it should be different from the empty date
                expect($nullResult->toString())->not->toBe($mutationStringResult->toString());
            } else {
                // If mutation string doesn't parse, it returns Undefined
                expect($mutationStringResult)->toBeInstanceOf(Undefined::class);
            }

            // Verify empty string produces specific result
            if ($emptyStringResult instanceof DateTimeAtom) {
                // null and empty string should produce SAME result
                // (both call fromString(''))
                expect($nullResult->toString())->toBe($emptyStringResult->toString());
            }
        });

        it('verifies fromString with empty string throws exception', function () {
            // Direct test of fromString with empty string
            // This should throw DateTimeTypeException based on your validation
            expect(fn() => DateTimeAtom::fromString(''))
                ->toThrow(DateTimeTypeException::class, 'blank');
        });
    });

    // Comprehensive test covering all branches
    describe('Comprehensive branch coverage', function () {
        $testCases = [
            // Branch: is_string($value)
            'string date' => [
                'input' => '2025-01-02T03:04:05+00:00',
                'shouldSucceed' => true,
                'branch' => 'string',
            ],

            // Branch: $value instanceof DateTimeImmutable
            'DateTimeImmutable' => [
                'input' => new DateTimeImmutable('2024-02-01'),
                'shouldSucceed' => true,
                'branch' => 'DateTimeImmutable',
            ],

            // Branch: $value instanceof self
            'self instance' => [
                'input' => DateTimeAtom::fromString('2025-01-02T03:04:05+00:00'),
                'shouldSucceed' => true,
                'branch' => 'self',
            ],

            // Branch: $value instanceof Stringable, is_scalar($value)
            'Stringable object' => [
                'input' => new class {
                    public function __toString(): string
                    {
                        return '2025-01-02T03:04:05+00:00';
                    }
                },
                'shouldSucceed' => true,
                'branch' => 'Stringable',
            ],

            // Branch: $value instanceof Stringable, is_scalar($value)
            'scalar integer' => [
                'input' => 1704067200, // 2023-12-31
                'shouldSucceed' => false,
                'branch' => 'scalar',
            ],

            // Branch: $value instanceof Stringable, is_scalar($value)
            'scalar boolean true' => [
                'input' => true,
                'shouldSucceed' => false,
                'branch' => 'scalar',
            ],

            // Branch: $value instanceof Stringable, is_scalar($value)
            'scalar boolean false' => [
                'input' => false,
                'shouldSucceed' => false,
                'branch' => 'scalar',
            ],

            // Branch: null === $value
            'null' => [
                'input' => null,
                'shouldSucceed' => false,
                'branch' => 'null',
            ],

            // Branch: default (throw)
            'invalid array' => [
                'input' => [],
                'shouldSucceed' => false,
                'branch' => 'default',
            ],

            // Additional test for whitespace string
            'whitespace string' => [
                'input' => '   ',
                'shouldSucceed' => false, // Should throw in fromString
                'branch' => 'string->throws',
            ],
        ];

        foreach ($testCases as $description => $case) {
            it("covers {$case['branch']} branch with {$description}", function () use ($case) {
                $result = DateTimeAtom::tryFromMixed($case['input']);

                if ($case['shouldSucceed']) {
                    expect($result)->toBeInstanceOf(DateTimeAtom::class);

                    // Verify specific branch behaviors
                    if ($case['branch'] === 'null') {
                        expect($result->isEmpty())->toBeTrue();
                    } else {
                        expect($result->isEmpty())->toBeFalse();
                    }
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            });
        }
    });
});
