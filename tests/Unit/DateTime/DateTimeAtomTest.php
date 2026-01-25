<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('DateTimeAtom', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('returns same instant and toString is ATOM', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = DateTimeAtom::fromDateTime($dt);

                expect($vo->value()->format(\DATE_ATOM))->toBe('2025-01-02T03:04:05+00:00')
                    ->and($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
            });
        });

        describe('fromString', function () {
            it('parses valid ATOM and preserves timezone offset (normalized to UTC)', function () {
                $vo = DateTimeAtom::fromString('2030-12-31T23:59:59+03:00');

                expect($vo->toString())->toBe('2030-12-31T20:59:59+00:00')
                    ->and($vo->value()->format(\DATE_ATOM))->toBe('2030-12-31T20:59:59+00:00');
            });

            it('accepts custom timezone', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T04:04:05+01:00', 'Europe/Berlin');
                expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00')
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('throws exception on invalid input', function (string $input, string $containedMessage) {
                expect(fn() => DateTimeAtom::fromString($input))
                    ->toThrow(DateTimeTypeException::class, $containedMessage);
            })->with([
                'invalid month' => ['2025-13-02T03:04:05+00:00', 'Warning at 25: The parsed date was invalid'],
                'trailing space' => ['2025-01-02T03:04:05+00:00 ', 'Error at 25: Trailing data'],
                'multiple errors/warnings' => [
                    '2025-13-02T03:04:05+00:00 ',
                    "Invalid date time value \"2025-13-02T03:04:05+00:00 \", use format \"Y-m-d\\TH:i:sP\"\nError at 25: Trailing data\nWarning at 25: The parsed date was invalid",
                ],
                'double error' => [
                    '2025-12-02T03:04:05+ 00:00',
                    "Error at 19: The timezone could not be found in the database\nError at 20: Trailing data",
                ],
            ]);

            it('aggregates multiple parse warnings with proper concatenation', function () {
                $input = '2025-13-40T25:61:61+00:00';
                try {
                    DateTimeAtom::fromString($input);
                    expect()->fail('Exception was not thrown');
                } catch (DateTimeTypeException $e) {
                    $msg = $e->getMessage();
                    expect($msg)->toContain('Invalid date time value "2025-13-40T25:61:61+00:00", use format "Y-m-d\TH:i:sP"')
                        ->and($msg)->toContain('Warning at 25: The parsed date was invalid')
                        ->and($msg)->not->toContain('PEST Mutator was here!');
                }
            });

            it('errors-only path keeps newline after header and after error line', function () {
                $input = '2025-01-02T03:04:05+00:00 ';
                try {
                    DateTimeAtom::fromString($input);
                    expect()->fail('Exception was not thrown');
                } catch (DateTimeTypeException $e) {
                    $msg = $e->getMessage();
                    expect($msg)->toContain("Invalid date time value \"2025-01-02T03:04:05+00:00 \", use format \"Y-m-d\\TH:i:sP\"\nError at 25: Trailing data\n");
                    expect(substr_count($msg, \PHP_EOL))->toBeGreaterThanOrEqual(2);
                }
            });
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, string $tz, bool $isSuccess) {
                $result = DateTimeAtom::tryFromString($input, $tz);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(DateTimeAtom::class)
                        ->and($result->toString())->toBe('2025-01-02T03:04:05+00:00');
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid UTC' => ['2025-01-02T03:04:05+00:00', 'UTC', true],
                'valid with TZ' => ['2025-01-02T04:04:05+01:00', 'Europe/Berlin', true],
                'invalid format' => ['2025-01-02 03:04:05Z', 'UTC', false],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $tz, string $expected) {
                $result = DateTimeAtom::tryFromMixed($input, $tz);
                expect($result)->toBeInstanceOf(DateTimeAtom::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'valid string' => ['2025-01-02T03:04:05+00:00', 'UTC', '2025-01-02T03:04:05+00:00'],
                'valid string with TZ' => ['2025-01-02T04:04:05+01:00', 'Europe/Berlin', '2025-01-02T03:04:05+00:00'],
                'DateTimeImmutable instance' => [new DateTimeImmutable('2025-01-02T03:04:05+00:00'), 'UTC', '2025-01-02T03:04:05+00:00'],
                'Stringable object' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '2025-01-02T03:04:05+00:00';
                        }
                    },
                    'UTC',
                    '2025-01-02T03:04:05+00:00',
                ],
            ]);

            it('kills the DateTimeImmutable instance mutant in tryFromMixed', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = DateTimeAtom::tryFromMixed($dt);
                expect($vo)->toBeInstanceOf(DateTimeAtom::class)
                    ->and($vo->value()->format(\DATE_ATOM))->toBe($dt->format(\DATE_ATOM));
            });

            it('kills the Stringable instance mutant in tryFromMixed', function () {
                $s = '2025-01-02T03:04:05+00:00';
                $stringable = new class($s) implements Stringable {
                    public function __construct(private string $s)
                    {
                    }

                    public function __toString(): string
                    {
                        return $this->s;
                    }
                };
                $vo = DateTimeAtom::tryFromMixed($stringable);
                expect($vo)->toBeInstanceOf(DateTimeAtom::class)
                    ->and($vo->toString())->toBe($s);
            });

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = DateTimeAtom::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [['x']],
                'stdClass' => [new stdClass()],
                'anonymous non-stringable' => [new class {}],
            ]);
        });

        describe('getFormat', function () {
            it('returns ATOM format', function () {
                expect(DateTimeAtom::getFormat())->toBe(\DATE_ATOM);
            });
        });
    });

    describe('Instance Methods', function () {
        it('value() returns internal DateTimeImmutable (normalized to UTC)', function () {
            $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
            $vo = DateTimeAtom::fromDateTime($dt);
            expect($vo->value()->format(\DATE_ATOM))->toBe($dt->format(\DATE_ATOM))
                ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
        });

        it('toString and __toString return ATOM formatted string', function () {
            $s = '2025-01-02T03:04:05+00:00';
            $vo = DateTimeAtom::fromString($s);

            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s)
                ->and($vo->__toString())->toBe($s);
        });

        it('jsonSerialize returns string', function () {
            $s = '2025-01-02T03:04:05+00:00';
            expect(DateTimeAtom::fromString($s)->jsonSerialize())->toBe($s);
        });

        it('isEmpty is always false', function () {
            $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
            expect($vo->isEmpty())->toBeFalse();

            // Kills the FalseToTrue mutant in isEmpty
            if ($vo->isEmpty() !== false) {
                throw new Exception('isEmpty mutant!');
            }
        });

        it('isUndefined is always false', function () {
            $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
            expect($vo->isUndefined())->toBeFalse();

            // Kills the FalseToTrue mutant in isUndefined
            if ($vo->isUndefined() !== false) {
                throw new Exception('isUndefined mutant!');
            }
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(DateTimeAtom::class)
                    ->and($vo2->toString())->toBe('2025-01-02T03:04:05+00:00')
                    ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');

                // original is immutable
                expect($vo->toString())->toBe('2025-01-02T03:04:05+00:00');
            });
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
                expect($vo->isTypeOf(DateTimeAtom::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
                expect($vo->isTypeOf('NonExistentClass', DateTimeAtom::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
