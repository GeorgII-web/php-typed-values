Usage
=====

This page shows concise examples for available value objects and how to create your own.

Static usage examples
---------------------

```php
$any = IntegerStandard::fromInt(-10);
$pos = IntegerPositive::fromInt(1);
$nn  = IntegerNonNegative::fromInt(0);
$wd  = IntegerWeekDay::fromInt(7);        // 1..7
$posFromString = IntegerPositive::fromString('123');
$wdFromString  = IntegerWeekDay::fromString('5');
$greeting = StringStandard::fromString('hello');
$name     = StringNonEmpty::fromString('Alice');
$price = FloatStandard::fromString('19.99');
$ratio = FloatNonNegative::fromFloat(0.5);  // >= 0
$dt = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00'); // DateTime (RFC 3339 / ATOM)
$unix = TimestampSeconds::fromString('1735787045'); // DateTime (Unix timestamp, seconds)

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
IntegerPositive::fromInt(0);              // throws: must be > 0
IntegerPositive::fromString('12.3');      // throws: String has no valid integer
IntegerWeekDay::fromInt(0);               // throws: Value must be between 1 and 7
StringNonEmpty::fromString('');          // throws: Value must be a non-empty string
FloatNonNegative::fromString('abc');  // throws: String has no valid float
DateTimeAtom::fromString('not-a-date'); // throws: String has no valid datetime
```

Create your own type: alias of PositiveInt
------------------------------------------

If you want a domain-specific alias (e.g., UserId) that behaves like PositiveInt, extend PositiveInt.

```php
<?php
final class UserId extends IntegerPositive {}

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

use PhpTypedValues\Abstract\Assert\Assert;
use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Abstract\Integer\IntType;

readonly class EvenPositiveInt extends IntType
{
    /** @var positive-int */
    protected int $value;

    /**
     * @throws IntegerTypeException
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

use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\DateTime\DateTimeAtom;

final class Profile
{
    public function __construct(
        public readonly IntegerPositive $id,
        public readonly StringNonEmpty $firstName,
        public readonly StringNonEmpty $lastName,
        public readonly ?StringNonEmpty $middleName,     // nullable field
        public readonly ?DateTimeAtom $birthDate,      // nullable field
        public readonly ?FloatNonNegative $heightM     // nullable field
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
            IntegerPositive::fromInt($id),
            StringNonEmpty::fromString($firstName),
            StringNonEmpty::fromString($lastName),
            $middleName !== null ? StringNonEmpty::fromString($middleName) : null,
            $birthDateAtom !== null ? DateTimeAtom::fromString($birthDateAtom) : null,
            $heightM !== null ? FloatNonNegative::fromString((string)$heightM) : null,
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
    id: IntegerPositive::fromInt(202),
    firstName: StringNonEmpty::fromString('Bob'),
    lastName: StringNonEmpty::fromString('Johnson'),
    middleName: StringNonEmpty::fromString('A.'),
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
