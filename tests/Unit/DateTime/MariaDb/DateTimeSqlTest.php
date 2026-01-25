<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\MariaDb\DateTimeSql;
use PhpTypedValues\Exception\DateTime\DateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('DateTimeSql', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('returns same instant and toString is SQL format', function () {
                $dt = new DateTimeImmutable('2025-01-02 03:04:05');
                $vo = DateTimeSql::fromDateTime($dt);

                expect($vo->value()->format('Y-m-d H:i:s'))->toBe('2025-01-02 03:04:05')
                    ->and($vo->toString())->toBe('2025-01-02 03:04:05');
            });
        });

        describe('fromString', function () {
            it('parses valid SQL string', function () {
                $vo = DateTimeSql::fromString('2030-12-31 23:59:59');

                expect($vo->toString())->toBe('2030-12-31 23:59:59')
                    ->and($vo->value()->format('Y-m-d H:i:s'))->toBe('2030-12-31 23:59:59');
            });

            it('throws on invalid date parts', function () {
                expect(fn() => DateTimeSql::fromString('2025-13-02 03:04:05'))
                    ->toThrow(DateTimeTypeException::class);
            });
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = DateTimeSql::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(DateTimeSql::class)
                        ->and($result->toString())->toBe($input);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid SQL' => ['2025-01-02 03:04:05', true],
                'invalid format' => ['2025-01-02T03:04:05+00:00', false],
            ]);

            it('accepts custom timezone', function () {
                $s = '2025-01-02 04:04:05';
                $vo = DateTimeSql::tryFromString($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(DateTimeSql::class)
                    ->and($vo->toString())->toBe('2025-01-02 03:04:05')
                    ->and($vo->value()->getOffset())->toBe(0);
            });
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = DateTimeSql::tryFromMixed($input);
                expect($result)->toBeInstanceOf(DateTimeSql::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'valid string' => ['2025-01-02 03:04:05', '2025-01-02 03:04:05'],
                'DateTimeImmutable' => [new DateTimeImmutable('2025-01-02 03:04:05'), '2025-01-02 03:04:05'],
                'Stringable' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '2025-01-02 03:04:05';
                        }
                    },
                    '2025-01-02 03:04:05',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = DateTimeSql::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [['x']],
                'object' => [new stdClass()],
            ]);

            it('accepts custom timezone', function () {
                $s = '2025-01-02 04:04:05';
                $vo = DateTimeSql::tryFromMixed($s, 'Europe/Berlin');
                expect($vo)->toBeInstanceOf(DateTimeSql::class)
                    ->and($vo->toString())->toBe('2025-01-02 03:04:05')
                    ->and($vo->value()->getOffset())->toBe(0);
            });

            it('kills mutants in tryFromMixed', function () {
                $customDefault = Undefined::create();

                // Kill InstanceOfToFalse for DateTimeImmutable
                $dt = new DateTimeImmutable('2025-01-01 00:00:00');
                expect(DateTimeSql::tryFromMixed($dt, 'UTC', $customDefault))
                    ->toBeInstanceOf(DateTimeSql::class);

                // Kill InstanceOfToTrue for Stringable by passing non-stringable
                expect(DateTimeSql::tryFromMixed([], 'UTC', $customDefault))
                    ->toBe($customDefault);

                // Kill RemoveStringCast for Stringable
                $stringable = new class implements Stringable {
                    public function __toString(): string
                    {
                        return '2025-01-01 00:00:00';
                    }
                };
                expect(DateTimeSql::tryFromMixed($stringable, 'UTC', $customDefault))
                    ->toBeInstanceOf(DateTimeSql::class);

                // Kill InstanceOfToTrue for DateTimeImmutable in tryFromMixed
                expect(DateTimeSql::tryFromMixed('2025-01-01 00:00:00', 'UTC', $customDefault))
                    ->toBeInstanceOf(DateTimeSql::class);
            });
        });

        describe('getFormat', function () {
            it('returns SQL format', function () {
                expect(DateTimeSql::getFormat())->toBe('Y-m-d H:i:s');
            });
        });
    });

    describe('Instance Methods', function () {
        it('value() returns internal DateTimeImmutable (normalized to UTC)', function () {
            $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
            expect($vo->value()->format('Y-m-d H:i:s'))->toBe('2025-01-02 03:04:05')
                ->and($vo->value()->getTimezone()->getName())->toBe('UTC');
        });

        it('toString and __toString return SQL formatted string', function () {
            $s = '2025-01-02 03:04:05';
            $vo = DateTimeSql::fromString($s);

            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s)
                ->and($vo->__toString())->toBe($s);
        });

        it('jsonSerialize returns string', function () {
            $s = '2025-01-02 03:04:05';
            expect(DateTimeSql::fromString($s)->jsonSerialize())->toBe($s);
        });

        it('isEmpty is always false', function () {
            $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
            expect($vo->isEmpty())->toBeFalse();

            // Kills the FalseToTrue mutant in isEmpty
            if ($vo->isEmpty() !== false) {
                throw new Exception('isEmpty mutant!');
            }
        });

        it('isUndefined is always false', function () {
            $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
            expect($vo->isUndefined())->toBeFalse();

            // Kills the FalseToTrue mutant in isUndefined
            if ($vo->isUndefined() !== false) {
                throw new Exception('isUndefined mutant!');
            }
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = DateTimeSql::fromString('2025-01-02 03:04:05', 'UTC');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(DateTimeSql::class)
                    ->and($vo2->toString())->toBe('2025-01-02 03:04:05')
                    ->and($vo2->value()->getTimezone()->getName())->toBe('UTC');

                // original is immutable
                expect($vo->toString())->toBe('2025-01-02 03:04:05');
            });
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
                expect($vo->isTypeOf(DateTimeSql::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
                expect($vo->isTypeOf('NonExistentClass', DateTimeSql::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = DateTimeSql::fromString('2025-01-02 03:04:05');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
