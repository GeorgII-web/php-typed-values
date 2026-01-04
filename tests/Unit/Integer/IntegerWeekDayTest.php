<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Integer\Specific\IntegerWeekDay;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerWeekDay::tryFromString returns value for 1..7', function (): void {
    $v1 = IntegerWeekDay::tryFromString('1');
    $v7 = IntegerWeekDay::tryFromString('7');

    expect($v1)
        ->toBeInstanceOf(IntegerWeekDay::class)
        ->and($v1->value())
        ->toBe(1)
        ->and($v7)
        ->toBeInstanceOf(IntegerWeekDay::class)
        ->and($v7->value())
        ->toBe(7);
});

it('IntegerWeekDay::tryFromString returns Undefined outside 1..7 and for non-integer strings', function (): void {
    expect(IntegerWeekDay::tryFromString('0'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerWeekDay::tryFromString('8'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerWeekDay::tryFromString('3.0'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerWeekDay::tryFromInt returns value for 1..7 and Undefined otherwise', function (): void {
    $ok = IntegerWeekDay::tryFromInt(3);
    $bad = IntegerWeekDay::tryFromInt(0);

    expect($ok)
        ->toBeInstanceOf(IntegerWeekDay::class)
        ->and($ok->value())
        ->toBe(3)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerWeekDay throws on values outside 1..7 in ctor and fromInt', function (): void {
    expect(fn() => new IntegerWeekDay(0))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromInt(8))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('IntegerWeekDay::fromString enforces strict integer and range 1..7', function (): void {
    // Strict integer check
    expect(fn() => IntegerWeekDay::fromString('3.0'))
        ->toThrow(IntegerTypeException::class, 'String "3.0" has no valid strict integer value');

    // Range checks after casting
    expect(fn() => IntegerWeekDay::fromString('0'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromString('8'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');

    // Success path
    $v = IntegerWeekDay::fromString('6');
    expect($v->value())->toBe(6);
});

it('creates WeekDayInt from int 1', function (): void {
    expect(IntegerWeekDay::fromInt(1)->value())->toBe(1);
});

it('creates WeekDayInt from int 7', function (): void {
    expect(IntegerWeekDay::fromInt(7)->value())->toBe(7);
});

it('fails on 8', function (): void {
    expect(fn() => IntegerWeekDay::fromInt(8))->toThrow(IntegerTypeException::class);
});

it('fails on 0', function (): void {
    expect(fn() => IntegerWeekDay::fromInt(0))->toThrow(IntegerTypeException::class);
});

it('creates WeekDayInt from string within range', function (): void {
    expect(IntegerWeekDay::fromString('1')->value())->toBe(1);
    expect(IntegerWeekDay::fromString('7')->value())->toBe(7);
});

it('creates WeekDayInt from integerish string', function (): void {
    expect(fn() => IntegerWeekDay::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating WeekDayInt from out-of-range strings', function (): void {
    expect(fn() => IntegerWeekDay::fromString('0'))->toThrow(IntegerTypeException::class);
    expect(fn() => IntegerWeekDay::fromString('8'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for WeekDayInt', function (): void {
    expect((new IntegerWeekDay(3))->toString())->toBe('3');
});

it('fails creating WeekDayInt from float string', function (): void {
    expect(fn() => IntegerWeekDay::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('jsonSerialize returns integer', function (): void {
    expect(IntegerWeekDay::tryFromString('1')->jsonSerialize())->toBeInt();
});
it('accepts 1..7 and exposes value/toString', function (): void {
    $one = new IntegerWeekDay(1);
    $seven = IntegerWeekDay::fromInt(7);

    expect($one->value())->toBe(1)
        ->and($one->toInt())->toBe(1)
        ->and($one->toString())->toBe('1')
        ->and((string) $one)->toBe('1')
        ->and($seven->value())->toBe(7)
        ->and($seven->toInt())->toBe(7)
        ->and($seven->toString())->toBe('7');
});

it('throws on values out of 1..7 in constructor/fromInt', function (): void {
    expect(fn() => new IntegerWeekDay(0))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromInt(8))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('fromString enforces strict integer parsing and range', function (): void {
    expect(IntegerWeekDay::fromString('2')->value())->toBe(2)
        ->and(IntegerWeekDay::fromString('6')->toString())->toBe('6');

    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerWeekDay::fromString($bad))
            ->toThrow(IntegerTypeException::class);
    }

    // Strict string passes validation but out of range -> domain error
    expect(fn() => IntegerWeekDay::fromString('0'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromString('8'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('tryFromInt/tryFromString return Undefined on invalid and instance on valid', function (): void {
    $okI = IntegerWeekDay::tryFromInt(3);
    $badI = IntegerWeekDay::tryFromInt(9);
    $okS = IntegerWeekDay::tryFromString('4');
    $badS = IntegerWeekDay::tryFromString('01');

    expect($okI)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okI->value())->toBe(3)
        ->and($okS)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okS->value())->toBe(4)
        ->and($badI)->toBeInstanceOf(Undefined::class)
        ->and($badS)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns native int', function (): void {
    expect(IntegerWeekDay::fromInt(5)->jsonSerialize())->toBe(5);
});

it('tryFromMixed returns instance for integer-like inputs (1..7) and Undefined otherwise', function (): void {
    $okInt = IntegerWeekDay::tryFromMixed(1);
    $okStr = IntegerWeekDay::tryFromMixed('7');
    $fromTrue = IntegerWeekDay::tryFromMixed(true);
    $fromFalse = IntegerWeekDay::tryFromMixed(false);
    $badLow = IntegerWeekDay::tryFromMixed(0);
    $badHigh = IntegerWeekDay::tryFromMixed(8);
    $badFloatish = IntegerWeekDay::tryFromMixed('1.0');
    $badArr = IntegerWeekDay::tryFromMixed(['x']);
    $badNull = IntegerWeekDay::tryFromMixed(null);
    $badObj = IntegerWeekDay::tryFromMixed(new stdClass());

    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '3';
        }
    };
    $okStringable = IntegerWeekDay::tryFromMixed($stringable);

    expect($okInt)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okInt->value())->toBe(1)
        ->and($okStr)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okStr->value())->toBe(7)
        ->and($fromTrue)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($fromTrue->value())->toBe(1)
        ->and($fromFalse)->toBeInstanceOf(Undefined::class)
        ->and($okStringable)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okStringable->value())->toBe(3)
        ->and($badLow)->toBeInstanceOf(Undefined::class)
        ->and($badHigh)->toBeInstanceOf(Undefined::class)
        ->and($badFloatish)->toBeInstanceOf(Undefined::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class)
        ->and($badObj)->toBeInstanceOf(Undefined::class);
});

it('isEmpty returns false for IntegerWeekDay', function (): void {
    $a = new IntegerWeekDay(1);
    $b = IntegerWeekDay::fromInt(7);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined is always false', function (): void {
    expect(IntegerWeekDay::fromInt(7)->isUndefined())->toBeFalse()
        ->and(IntegerWeekDay::fromInt(1)->isUndefined())->toBeFalse();
});

it('fromFloat creates instance from float with exact integer value', function (): void {
    $v = IntegerWeekDay::fromFloat(5.0);
    expect($v->value())->toBe(5);
});

it('toFloat converts to float and kills RemoveDoubleCast mutant', function (): void {
    $v = new IntegerWeekDay(3);
    $f = $v->toFloat();
    expect($f)->toBe(3.0)
        ->and($f)->toBeFloat();

    expect(\is_float($v->toFloat()))->toBeTrue();

    // Test all valid weekday values to ensure precision check logic is covered
    foreach ([1, 2, 3, 4, 5, 6, 7] as $day) {
        $weekday = new IntegerWeekDay($day);
        expect($weekday->toFloat())->toBe((float) $day);
    }
});

it('toBool converts to bool', function (): void {
    $positive = new IntegerWeekDay(5);
    expect($positive->toBool())->toBeTrue();
});

it('fromBool creates instance from boolean value', function (): void {
    $fromTrue = IntegerWeekDay::fromBool(true);
    expect($fromTrue->value())->toBe(1);
});

it('fromBool throws on false', function (): void {
    expect(fn() => IntegerWeekDay::fromBool(false))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"');
});

it('fromFloat throws on out of range value', function (): void {
    expect(fn() => IntegerWeekDay::fromFloat(8.0))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('round-trip conversion preserves value: int → string → int', function (): void {
    $original = 5;
    $v1 = IntegerWeekDay::fromInt($original);
    $str = $v1->toString();
    $v2 = IntegerWeekDay::fromString($str);

    expect($v2->value())->toBe($original);
});

it('round-trip conversion preserves value: string → int → string', function (): void {
    $original = '3';
    $v1 = IntegerWeekDay::fromString($original);
    $int = $v1->toInt();
    $v2 = IntegerWeekDay::fromInt($int);

    expect($v2->toString())->toBe($original);
});

it('multiple round-trips preserve value integrity for all valid weekdays', function (): void {
    $values = [1, 2, 3, 4, 5, 6, 7];

    foreach ($values as $original) {
        // int → string → int → string → int
        $result = IntegerWeekDay::fromString(
            IntegerWeekDay::fromInt(
                IntegerWeekDay::fromString(
                    IntegerWeekDay::fromInt($original)->toString()
                )->toInt()
            )->toString()
        )->value();

        expect($result)->toBe($original);
    }
});

it('fromLabel creates instance from valid weekday names', function (): void {
    $monday = IntegerWeekDay::fromLabel('Monday');
    $friday = IntegerWeekDay::fromLabel('Friday');
    $sunday = IntegerWeekDay::fromLabel('Sunday');

    expect($monday->value())->toBe(1)
        ->and($friday->value())->toBe(5)
        ->and($sunday->value())->toBe(7);
});

it('fromLabel handles all weekday names correctly', function (string $label, int $expectedValue): void {
    $weekday = IntegerWeekDay::fromLabel($label);
    expect($weekday->value())->toBe($expectedValue);
})->with([
    ['Monday', 1],
    ['Tuesday', 2],
    ['Wednesday', 3],
    ['Thursday', 4],
    ['Friday', 5],
    ['Saturday', 6],
    ['Sunday', 7],
]);

it('fromLabel throws on invalid weekday labels', function (): void {
    expect(fn() => IntegerWeekDay::fromLabel('monday'))
        ->toThrow(IntegerTypeException::class, 'Invalid weekday label "monday"')
        ->and(fn() => IntegerWeekDay::fromLabel('Mon'))
        ->toThrow(IntegerTypeException::class, 'Invalid weekday label "Mon"')
        ->and(fn() => IntegerWeekDay::fromLabel(''))
        ->toThrow(IntegerTypeException::class, 'Invalid weekday label ""')
        ->and(fn() => IntegerWeekDay::fromLabel('InvalidDay'))
        ->toThrow(IntegerTypeException::class, 'Invalid weekday label "InvalidDay"');
});

it('toLabel returns correct weekday name', function (): void {
    $monday = new IntegerWeekDay(1);
    $wednesday = new IntegerWeekDay(3);
    $sunday = new IntegerWeekDay(7);

    expect($monday->toLabel())->toBe('Monday')
        ->and($wednesday->toLabel())->toBe('Wednesday')
        ->and($sunday->toLabel())->toBe('Sunday');
});

it('toLabel returns correct names for all weekdays', function (int $value, string $expectedLabel): void {
    $weekday = new IntegerWeekDay($value);
    expect($weekday->toLabel())->toBe($expectedLabel);
})->with([
    [1, 'Monday'],
    [2, 'Tuesday'],
    [3, 'Wednesday'],
    [4, 'Thursday'],
    [5, 'Friday'],
    [6, 'Saturday'],
    [7, 'Sunday'],
]);

it('round-trip conversion preserves value: label → int → label', function (): void {
    $original = 'Wednesday';
    $weekday = IntegerWeekDay::fromLabel($original);
    $value = $weekday->value();
    $label = IntegerWeekDay::fromInt($value)->toLabel();

    expect($label)->toBe($original);
});

it('round-trip conversion preserves value: int → label → int', function (): void {
    $original = 5;
    $weekday = IntegerWeekDay::fromInt($original);
    $label = $weekday->toLabel();
    $value = IntegerWeekDay::fromLabel($label)->value();

    expect($value)->toBe($original);
});

it('multiple round-trips preserve integrity: label → int → label → int', function (): void {
    $labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    foreach ($labels as $original) {
        // label → int → label → int → label
        $result = IntegerWeekDay::fromInt(
            IntegerWeekDay::fromLabel(
                IntegerWeekDay::fromInt(
                    IntegerWeekDay::fromLabel($original)->value()
                )->toLabel()
            )->value()
        )->toLabel();

        expect($result)->toBe($original);
    }
});

it('isTypeOf returns true when class matches', function (): void {
    $v = IntegerWeekDay::fromInt(5);
    expect($v->isTypeOf(IntegerWeekDay::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = IntegerWeekDay::fromInt(5);
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = IntegerWeekDay::fromInt(5);
    expect($v->isTypeOf('NonExistentClass', IntegerWeekDay::class, 'AnotherClass'))->toBeTrue();
});
