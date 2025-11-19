Usage
=====

This page shows concise, up-to-date examples for the available integer value objects.

Namespaces
----------

All classes live under the base namespace:

```
PhpTypedValues
```

Available integer types
-----------------------

- PhpTypedValues\Integer\Integer — any PHP integer
- PhpTypedValues\Integer\PositiveInt — positive integer (> 0)
- PhpTypedValues\Integer\NonNegativeInt — zero or positive integer (>= 0)
- PhpTypedValues\Integer\WeekDayInt — integer in range 1..7

Quick start
-----------

```php
use PhpTypedValues\Integer\Integer;
use PhpTypedValues\Integer\NonNegativeInt;
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\WeekDayInt;

$any = new Integer(-10);          // ok
$pos = new PositiveInt(1);        // ok
$nn  = new NonNegativeInt(0);     // ok
$wd  = new WeekDayInt(7);         // ok (1..7)

// From string
$posFromString = PositiveInt::fromString('123');

// Accessing the raw value and string form
echo $pos->value();     // 1
echo $wd->toString();   // "7"
```

Validation errors
-----------------

Invalid input throws an exception with a helpful message.

```php
use PhpTypedValues\Integer\PositiveInt;

new PositiveInt(0);     // throws: Value must be a positive integer
PositiveInt::fromString('12.3'); // throws: String has no valid integer
```

Notes
-----

- All value objects are immutable (readonly) and type-safe.
- Utility constructors: fromInt(int) and fromString(string) are provided where applicable.
