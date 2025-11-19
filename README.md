PHP Typed Values
================

Typed value objects for common PHP data types. Make primitives explicit, safe, and self-documenting with tiny immutable value objects.

- Requires PHP 8.2+
- Zero runtime dependencies
- Tooling: Pest, PHPUnit, Psalm, PHP-CS-Fixer

Quick links:
- Install: docs/INSTALL.md
- Usage: docs/USAGE.md
- Development: docs/DEVELOP.md

Install
-------

Using Composer:

```
composer require georgii-web/php-typed-values
```

Usage
-----

Create and use typed values with validation built in.

```php
// Integers
use PhpTypedValues\Integer\IntegerBasic;
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\NonNegativeInt;
use PhpTypedValues\Integer\WeekDayInt;

$age      = new PositiveInt(27);       // ok (positive-int)
$retries  = new NonNegativeInt(0);     // ok (0 or positive)
$weekday  = new WeekDayInt(5);         // ok (1..7)
$anyInt   = new IntegerBasic(-42);     // ok (any integer)

$fromString = PositiveInt::fromString('123');

// Strings
use PhpTypedValues\String\StringBasic;
use PhpTypedValues\String\NonEmptyStr;

$greeting = StringBasic::fromString('hello');
$name     = new NonEmptyStr('Alice');  // throws if empty

// Floats
use PhpTypedValues\Float\FloatBasic;
use PhpTypedValues\Float\NonNegativeFloat;

$price    = FloatBasic::fromString('19.99');
$ratio    = new NonNegativeFloat(0.5);    // > 0 required

// Access the underlying scalar value / string form
$ageValue = $age->value();        // 27
echo $weekday->toString();        // "5"
echo $price->toString();          // "19.99"
```

All value objects are immutable; invalid input throws an exception with a helpful message.

Provided types (so far):

- Integers
  - IntegerBasic — any PHP integer
  - PositiveInt — positive integer (> 0)
  - NonNegativeInt — zero or positive integer (>= 0)
  - WeekDayInt — integer in range 1..7
- Strings
  - StringBasic — any PHP string
  - NonEmptyStr — non-empty string
- Floats
  - FloatBasic — any PHP float
  - PositiveFloat — positive float (> 0)

Why
---

- Replace loose primitives with explicit, intention-revealing types
- Centralize validation in one place
- Improve static analysis and readability

Scripts
-------

Helpful Composer scripts are included:

- composer test - run tests (Pest)
- composer type - 100% type coverage gate
- composer coverage - code coverage gate
- composer sca - static analysis (Psalm)
- composer cs - coding standards (PHP-CS-Fixer)

See docs/DEVELOP.md for details.

License
-------

MIT
