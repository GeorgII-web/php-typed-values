# Usage examples

#### Fallback value

```php
$email = StringEmail::tryFromString($someString, StringEmpty::fromString('')); // Or email or empty string
$email->isEmpty();
$email->isUndefined();
```

#### Compose value objects

```php
use PhpTypedValues\Base\ValueObjectInterface;use PhpTypedValues\Float\FloatPositive;use PhpTypedValues\Integer\IntegerPositive;use PhpTypedValues\String\StringNonEmpty;use PhpTypedValues\Undefined\Alias\Undefined;

final readonly class Profile implements ValueObjectInterface
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {}

    public static function fromArray(array $value): self {
        return new self(
            IntegerPositive::fromInt($value['id']),                    // early fail (must be valid)
            StringNonEmpty::tryFromMixed($value['firstName']),         // late fail (maybe undefined)
            ($value['height'] ?? null) !== null
                ? FloatPositive::fromString((string) $value['height']) // early fail if provided
                : Undefined::create(),                                 // late fail when accessed
        );
    }
    public function toArray(): array { return ['id' => $this->id->value()]; }
    public function jsonSerialize(): array { return $this->toArray(); }
    
    public function getId(): IntegerPositive { return $this->id; }
    public function getFirstName(): StringNonEmpty|Undefined { return $this->firstName; }
    public function getHeight(): FloatPositive|Undefined { return $this->height; }
}
```

##### Early fail (invalid input prevents creation)

```php
Profile::fromArray(['id' => -1, 'firstName' => 'Alice', 'height' => 172.5]); // throws exception, id not positive
```

##### Late fail with `Undefined` (an object exists, but fail on access)

```php
$profile = Profile::fromArray(['id' => 101, 'firstName' => '', 'height' => '172.5']); // created with Undefined firstName
$profile->getFirstName()->value(); // throws an exception on access the Undefined value
```

##### Optional fail (only fail if the optional value is provided and invalid)

Ideal for partial data handling (e.g., requests where only specific fields, like ID, are required), allowing access to valid fields without failing on missing ones.

```php
Profile::fromArray(['id' => 101, 'firstName' => 'Alice', 'height' => -1]); // invalid provided value -> early fail

$profile = Profile::fromArray(['id' => 101, 'firstName' => 'Alice', 'height' => null]); // value omitted -> created, fails only on access
$profile->getHeight()->value(); // throws an exception on access the Undefined value
```