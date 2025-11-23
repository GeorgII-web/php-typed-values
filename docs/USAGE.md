Usage
=====

This page shows concise examples for all available value objects and how to create your own.

Namespace
---------

All classes live under the base namespace:

```
PhpTypedValues
```

Available types
---------------

Integers (PhpTypedValues\Integer):

- IntegerBasic — any PHP integer
- PositiveInt — positive integer (> 0)
- NonNegativeInt — zero or positive integer (>= 0)
- WeekDayInt — integer in range 1..7

Strings (PhpTypedValues\String):

- StringBasic — any PHP string
- NonEmptyStr — non-empty string

Floats (PhpTypedValues\Float):

- FloatBasic — any PHP float
- NonNegativeFloat — zero or positive float (>= 0)

DateTime (PhpTypedValues\DateTime):

- DateTimeAtom — immutable DateTime in RFC3339 (ATOM) format

Static usage examples
---------------------

```php
use PhpTypedValues\Integer\IntegerBasic;
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\NonNegativeInt;
use PhpTypedValues\Integer\WeekDayInt;
use PhpTypedValues\String\StringBasic;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\Float\FloatBasic;
use PhpTypedValues\Float\NonNegativeFloat;
use PhpTypedValues\DateTime\DateTimeAtom;

// Integers
$any = IntegerBasic::fromInt(-10);
$pos = PositiveInt::fromInt(1);
$nn  = NonNegativeInt::fromInt(0);
$wd  = WeekDayInt::fromInt(7);        // 1..7

// From string (integers)
$posFromString = PositiveInt::fromString('123');
$wdFromString  = WeekDayInt::fromString('5');

// Strings
$greeting = StringBasic::fromString('hello');
$name     = NonEmptyStr::fromString('Alice');

// Floats
$price = FloatBasic::fromString('19.99');
$ratio = NonNegativeFloat::fromFloat(0.5);  // >= 0

// DateTime (RFC 3339 / ATOM)
$dt = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00');

// Accessing value and string form
$posValue = $pos->value();        // 1 (int)
$wdText   = $wd->toString();      // "7"
$priceStr = $price->toString();   // "19.99"
$isoText  = $dt->toString();      // "2025-01-02T03:04:05+00:00"
```

Validation errors (static constructors)
--------------------------------------

Invalid input throws an exception with a helpful message.

```php
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\WeekDayInt;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\Float\NonNegativeFloat;
use PhpTypedValues\DateTime\DateTimeAtom;

PositiveInt::fromInt(0);              // throws: must be > 0
PositiveInt::fromString('12.3');      // throws: String has no valid integer

WeekDayInt::fromInt(0);               // throws: Value must be between 1 and 7

NonEmptyStr::fromString('');          // throws: Value must be a non-empty string

NonNegativeFloat::fromString('abc');  // throws: String has no valid float

DateTimeAtom::fromString('not-a-date'); // throws: String has no valid datetime
```

Create your own type: alias of PositiveInt
------------------------------------------

If you want a domain-specific alias (e.g., UserId) that behaves like PositiveInt, extend PositiveInt and override static factories so they return the subclass instance (because the base uses `new self(...)`).

```php
<?php
declare(strict_types=1);

namespace App\Domain;

use PhpTypedValues\Integer\PositiveInt;

final class UserId extends PositiveInt
{
    public static function fromInt(int $value): self
    {
        // PositiveInt's constructor validates (> 0)
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        // Reuse the core numeric-string assertion
        parent::assertNumericString($value);
        // PositiveInt's constructor (inherited) validates (> 0)
        return new self((int) $value);
    }
}

// Usage
$userId = UserId::fromInt(42);
```

Create your own class based on integers
---------------------------------------

You can also create a brand-new integer type by extending the base IntType and implementing your rule. Below is an EvenPositiveInt example:

```php
<?php
declare(strict_types=1);

namespace App\Domain;

use PhpTypedValues\Code\Assert\Assert;
use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Code\Integer\IntType;

final class EvenPositiveInt extends IntType
{
    /** @var positive-int */
    protected int $value;

    /**
     * @throws NumericTypeException
     */
    public function __construct(int $value)
    {
        Assert::greaterThanEq($value, 1);
        Assert::true($value % 2 === 0, 'Value must be even');

        /** @var positive-int $value */
        $this->value = $value;
    }

    /** @return positive-int */
    public function value(): int
    {
        return $this->value;
    }

    /** @throws NumericTypeException */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /** @throws NumericTypeException */
    public static function fromString(string $value): self
    {
        parent::assertNumericString($value);
        return new self((int) $value);
    }
}

// Usage
$v = EvenPositiveInt::fromInt(6);
```

Notes
-----

- All value objects are immutable (readonly) and type-safe.
- Prefer static constructors (fromInt/fromFloat/fromString, etc.).
