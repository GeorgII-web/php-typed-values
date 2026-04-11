<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\TimeIso8601;
use PhpTypedValues\Exception\DateTime\Iso8601TimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(TimeIso8601::class);

describe('TimeIso8601', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('returns same time and toString is H:i:s', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = TimeIso8601::fromDateTime($dt);

                expect($vo->toString())->toBe('03:04:05');
            });
        });

        describe('fromString', function () {
            it('parses valid H:i:s', function () {
                $vo = TimeIso8601::fromString('23:59:59');
                expect($vo->toString())->toBe('23:59:59');
            });

            it('throws exception on invalid input', function (string $input) {
                expect(fn() => TimeIso8601::fromString($input))
                    ->toThrow(Iso8601TimeTypeException::class);
            })->with([
                'invalid hour' => ['25:01:01'],
                'invalid minute' => ['10:61:01'],
                'with date' => ['2025-01-01 10:00:00'],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = TimeIso8601::tryFromMixed($input);
                expect($result)->toBeInstanceOf(TimeIso8601::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'valid string' => ['15:52:01', '15:52:01'],
                'DateTimeImmutable instance' => [new DateTimeImmutable('15:52:01'), '15:52:01'],
                'Stringable object' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '15:52:01';
                        }
                    },
                    '15:52:01',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = TimeIso8601::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class);
            })->with([
                'null' => [null],
                'array' => [['x']],
                'stdClass' => [new stdClass()],
                'anonymous non-stringable' => [new class {}],
            ]);
        });

        describe('Null checks', function () {
            it('fromNull throws exception', function () {
                expect(fn() => TimeIso8601::fromNull(null))
                    ->toThrow(Iso8601TimeTypeException::class, 'TimeIso8601 type cannot be created from null');
            });

            it('toNull throws exception', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                expect(fn() => $vo::toNull())
                    ->toThrow(Iso8601TimeTypeException::class, 'TimeIso8601 type cannot be converted to null');
            });
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = TimeIso8601::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(TimeIso8601::class);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['15:52:01', true],
                'invalid' => ['bad', false],
            ]);
        });

        describe('getFormat', function () {
            it('returns H:i:s format', function () {
                expect(TimeIso8601::getFormat())->toBe('H:i:s');
            });
        });
    });

    describe('Instance Methods', function () {
        it('jsonSerialize returns string', function () {
            $s = '15:52:01';
            expect(TimeIso8601::fromString($s)->jsonSerialize())->toBe($s);
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(TimeIso8601::class)
                    ->and($vo2->toString())->toBe('15:52:01');
            });
        });
        it('toString returns H:i:s formatted string', function () {
            $s = '15:52:01';
            $vo = TimeIso8601::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s);
        });

        it('isEmpty is always false', function () {
            $vo = TimeIso8601::fromString('15:52:01');
            expect($vo->isEmpty())->toBeFalse();
        });

        it('isUndefined is always false', function () {
            $vo = TimeIso8601::fromString('15:52:01');
            expect($vo->isUndefined())->toBeFalse();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                expect($vo->isTypeOf(TimeIso8601::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                expect($vo->isTypeOf('NonExistentClass', TimeIso8601::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = TimeIso8601::fromString('15:52:01');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
