Usage
=====

This page shows concise, up-to-date examples for the available Integer, String, and Float value objects.

Namespaces
----------

All classes live under the base namespace:

```
PhpTypedValues
```

Available integer types
-----------------------

- PhpTypedValues\Integer\IntegerBasic — any PHP integer
- PhpTypedValues\Integer\PositiveInt — positive integer (> 0)
- PhpTypedValues\Integer\NonNegativeInt — zero or positive integer (>= 0)
- PhpTypedValues\Integer\WeekDayInt — integer in range 1..7

Available string types
----------------------

- PhpTypedValues\String\StringBasic — any PHP string
- PhpTypedValues\String\NonEmptyStr — non-empty string

Available float types
---------------------

- PhpTypedValues\Float\FloatBasic — any PHP float
- PhpTypedValues\Float\NonNegativeFloat — zero or positive float (>= 0)

Available DateTime types
------------------------

- PhpTypedValues\DateTime\DateTimeBasic — immutable DateTime value; parses common ISO-8601 formats

Quick start
-----------

```php
// Integers
use PhpTypedValues\Integer\IntegerBasic;
use PhpTypedValues\Integer\NonNegativeInt;
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\WeekDayInt;

$any = new IntegerBasic(-10);     // ok
$pos = new PositiveInt(1);        // ok
$nn  = new NonNegativeInt(0);     // ok
$wd  = new WeekDayInt(7);         // ok (1..7)

// From string
$posFromString = PositiveInt::fromString('123');

// Strings
use PhpTypedValues\String\StringBasic;
use PhpTypedValues\String\NonEmptyStr;

$greeting = StringBasic::fromString('hello');
$name     = new NonEmptyStr('Alice'); // throws if empty

// Floats
use PhpTypedValues\Float\FloatBasic;
use PhpTypedValues\Float\NonNegativeFloat;

$price = FloatBasic::fromString('19.99');
$ratio = new NonNegativeFloat(0.5);  // >= 0 allowed

// DateTime
use PhpTypedValues\DateTime\DateTimeAtom;

$dt = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');
echo $dt->toString(); // "2025-01-02T03:04:05+00:00"

// Accessing the raw value and string form
echo $pos->value();     // 1
echo $wd->toString();   // "7"
echo $price->toString(); // "19.99"
```

Validation errors
-----------------

Invalid input throws an exception with a helpful message.

```php
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\Float\NonNegativeFloat;
use PhpTypedValues\DateTime\DateTimeAtom;

new PositiveInt(0);              // throws: Value must be a positive integer
PositiveInt::fromString('12.3'); // throws: String has no valid integer

new NonEmptyStr('');             // throws: Value must be a non-empty string

NonNegativeFloat::fromString('abc'); // throws: String has no valid float

DateTimeAtom::fromString('not-a-date'); // throws: String has no valid datetime
```

Notes
-----

- All value objects are immutable (readonly) and type-safe.
- Utility constructors: fromInt(int)/fromString(string), fromFloat(float)/fromString(string) are provided where applicable.
