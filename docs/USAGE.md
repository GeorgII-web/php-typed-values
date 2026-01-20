# Usage examples

This guide demonstrates common patterns for using typed values in your application, organized by topic.

### Table of Contents

1. [Basic Usage](#basic-usage)
2. [Validation Semantics](#validation-semantics)
3. [Composite Objects & DTOs](#composite-objects--dtos)
4. [Collections (Arrays of Objects)](#collections-arrays-of-objects)
5. [Fallback & Error Handling](#fallback--error-handling)

---

### Basic Usage

Existing typed values provide multiple factory methods to create objects from various inputs.

#### Creating from strings and primitives

```php
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;

// Direct factories (throw exceptions on invalid input)
$id = IntegerPositive::fromInt(123);
$name = StringNonEmpty::fromString('Alice');

// Casting from mixed types
$price = FloatPositive::tryFromMixed('19.99'); // Returns FloatPositive or Undefined
```

#### Idempotent conversions

Typed values guarantee that `fromString($s)->toString() === $s` for valid inputs, ensuring no precision loss or formatting surprises.

```php
use PhpTypedValues\Float\FloatStandard;

$v = FloatStandard::fromString('0.10000000000000001');
echo $v->toString(); // "0.10000000000000001"
```

---

### Validation Semantics

The library supports different strategies for handling invalid data, allowing you to choose between strict early failure or lenient late failure.

#### Early Fail

Invalid input prevents object creation immediately. Best for strictly required data.

```php
// Throws IntegerTypeException immediately
$id = IntegerPositive::fromInt(-1); 
```

#### Late Fail with `Undefined`

The `tryFrom*` methods return an `Undefined` object instead of throwing an exception. Failure only happens when you try to access the primitive value.

```php
use PhpTypedValues\Undefined\Alias\Undefined;

$name = StringNonEmpty::tryFromString(''); // Returns Undefined instance

if ($name->isUndefined()) {
    echo "Name is missing or invalid";
}

$name->value(); // Throws UndefinedTypeException
```

#### Optional Fail

A combination where you only fail if a value is provided but invalid. If it's `null`, it becomes `Undefined`.

```php
$height = ($input !== null) 
    ? FloatPositive::fromFloat($input) // Early fail if provided and <= 0
    : Undefined::create();             // Late fail if accessed
```

---

### Composite Objects & DTOs

Combine multiple typed values into domain objects.

```php
use PhpTypedValues\Base\ValueObjectInterface;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Undefined\Alias\Undefined;

final readonly class UserProfile implements ValueObjectInterface
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty $username,
        private FloatPositive|Undefined $rating,
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            IntegerPositive::fromInt($data['id']),           // Required
            StringNonEmpty::fromString($data['username']),   // Required
            FloatPositive::tryFromMixed($data['rating'] ?? null), // Optional
        );
    }

    public function jsonSerialize(): array 
    {
        return [
            'id' => $this->id->value(),
            'username' => $this->username->value(),
            'rating' => $this->rating->isUndefined() ? null : $this->rating->value(),
        ];
    }
    
    public function isEmpty(): bool { return false; }
    public function isUndefined(): bool { return false; }
    public function toString(): string { return $this->username->value(); }
}
```

---

### Collections (Arrays of Objects)

Use `ArrayOfObjects` to manage immutable lists of typed values.

```php
use PhpTypedValues\ArrayType\ArrayOfObjects;
use PhpTypedValues\String\StringNonEmpty;

$tags = ArrayOfObjects::fromItems(
    StringNonEmpty::fromString('php'),
    StringNonEmpty::fromString('types'),
);

foreach ($tags as $tag) {
    echo $tag->value(); // "php", then "types"
}

// Convert back to a native array of strings
$rawTags = $tags->toArray(); // ['php', 'types']
```

#### Safe check for defined values

```php
/** @var ArrayOfObjects<StringNonEmpty|Undefined> $list */
$definedOnly = $list->getDefinedItems(); // Filters out all Undefined objects
```

---

### Fallback & Error Handling

Use `tryFrom*` with a default value to provide a graceful fallback.

#### Custom Fallback Value

```php
$email = StringEmail::tryFromString($input, StringEmpty::fromString('')); // Email string OR Empty string
```
