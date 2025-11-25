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
- DateTimeTimestamp — immutable DateTime represented as a Unix timestamp (seconds since epoch, UTC)

Static usage examples
---------------------

```php
use PhpTypedValues\DateTime\DateTimeAtom;use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;use PhpTypedValues\Float\FloatBasic;use PhpTypedValues\Float\NonNegativeFloat;use PhpTypedValues\Integer\IntegerBasic;use PhpTypedValues\Integer\NonNegativeInt;use PhpTypedValues\Integer\PositiveInt;use PhpTypedValues\Integer\WeekDayInt;use PhpTypedValues\String\NonEmptyStr;use PhpTypedValues\String\StringBasic;

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

// DateTime (Unix timestamp, seconds)
$unix = TimestampSeconds::fromString('1735787045');

// Accessing value and string form
$posValue = $pos->value();        // 1 (int)
$wdText   = $wd->toString();      // "7"
$priceStr = $price->toString();   // "19.99"
$isoText  = $dt->toString();      // "2025-01-02T03:04:05+00:00"
$unixText = $unix->toString();    // e.g. "1735787045"
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

If you want a domain-specific alias (e.g., UserId) that behaves like PositiveInt, extend PositiveInt.

```php
<?php
declare(strict_types=1);

namespace App\Domain;

use PhpTypedValues\Integer\PositiveInt;

final class UserId extends PositiveInt {}

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
use PhpTypedValues\Code\Exception\FloatTypeException;
use PhpTypedValues\Code\Integer\IntType;

final class EvenPositiveInt extends IntType
{
    /** @var positive-int */
    protected int $value;

    /**
     * @throws FloatTypeException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new IntegerTypeException(sprintf('Expected positive integer, got "%d"', $value));
        }

        if ($value % 2 !== 0) {
            throw new IntegerTypeException(sprintf('Expected even integer, got "%d"', $value));
        }

        $this->value = $value;
    }

    /** @return positive-int */
    public function value(): int
    {
        return $this->value;
    }

    /** @throws IntegerTypeException */
    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    /** @throws IntegerTypeException */
    public static function fromString(string $value): self
    {
        parent::assertIntegerString($value);
        
        return new self((int) $value);
    }
}

// Usage
$v = EvenPositiveInt::fromInt(6);
```

Composite value object (with nullable fields)
--------------------------------------------

You can compose several primitive value objects into a richer domain object. The example below shows a simple Profile value object that uses multiple primitives and also supports nullable fields.

```php
<?php
declare(strict_types=1);

namespace App\Domain;

use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\Float\NonNegativeFloat;
use PhpTypedValues\DateTime\DateTimeAtom;

final class Profile
{
    public function __construct(
        public readonly PositiveInt $id,
        public readonly NonEmptyStr $firstName,
        public readonly NonEmptyStr $lastName,
        public readonly ?NonEmptyStr $middleName,     // nullable field
        public readonly ?DateTimeAtom $birthDate,      // nullable field
        public readonly ?NonNegativeFloat $heightM     // nullable field
    ) {}

    // Convenience named constructor that accepts raw scalars and builds primitives internally
    public static function fromScalars(
        int $id,
        string $firstName,
        string $lastName,
        ?string $middleName,
        ?string $birthDateAtom,   // e.g. "2025-01-02T03:04:05+00:00"
        int|float|string|null $heightM
    ): self {
        return new self(
            PositiveInt::fromInt($id),
            NonEmptyStr::fromString($firstName),
            NonEmptyStr::fromString($lastName),
            $middleName !== null ? NonEmptyStr::fromString($middleName) : null,
            $birthDateAtom !== null ? DateTimeAtom::fromString($birthDateAtom) : null,
            $heightM !== null ? NonNegativeFloat::fromString((string)$heightM) : null,
        );
    }
}

// Usage
$p1 = Profile::fromScalars(
    id: 101,
    firstName: 'Alice',
    lastName: 'Smith',
    middleName: null,                                  // nullable
    birthDateAtom: '1990-05-10T00:00:00+00:00',        // optional, can be null
    heightM: '1.70',                                   // string, int or float supported
);

$p2 = new Profile(
    id: PositiveInt::fromInt(202),
    firstName: NonEmptyStr::fromString('Bob'),
    lastName: NonEmptyStr::fromString('Johnson'),
    middleName: NonEmptyStr::fromString('A.'),
    birthDate: null,
    heightM: null,
);

// Accessing wrapped values
$first = $p1->firstName->toString();           // "Alice"
$height = $p1->heightM?->toString();           // "1.70" or null
$birthIso = $p1->birthDate?->toString();       // RFC3339 string or null
```

Notes
-----

- All value objects are immutable (readonly) and type-safe.
- Prefer static constructors (fromInt/fromFloat/fromString, etc.).
