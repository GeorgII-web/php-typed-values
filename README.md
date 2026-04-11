### PHP Typed Values

Typed value objects for PHP. Build precise, immutable, and validated data for DTOs, Value Objects, and Entities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)
[![Tests](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml/badge.svg)](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)

Code quality:

![tests](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/georgii-web/75977b7515de20d7382f6855d44a1d97/raw/tests_count.json)
![types](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/georgii-web/75977b7515de20d7382f6855d44a1d97/raw/types.json)
![coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/georgii-web/75977b7515de20d7382f6855d44a1d97/raw/coverage.json)
![mutations](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/georgii-web/75977b7515de20d7382f6855d44a1d97/raw/mutations.json)
![psalm](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/georgii-web/75977b7515de20d7382f6855d44a1d97/raw/psalm.json)
![cs-fixer](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/georgii-web/75977b7515de20d7382f6855d44a1d97/raw/cs_fixer.json)

---

### Install

![Version 3.x](https://img.shields.io/badge/Version-3.x-777BB4)
![PHP >=8.4](https://img.shields.io/badge/PHP->=8.4-8892BF?logo=php)

```bash
composer require georgii-web/php-typed-values:^3
```

![Version 2.x](https://img.shields.io/badge/Version-2.x-777BB4)
![PHP >=8.2 <8.4](https://img.shields.io/badge/PHP->=8.2--<8.4-8892BF?logo=php)

```bash
composer require georgii-web/php-typed-values:^2
```

![Version 1.x](https://img.shields.io/badge/Version-1.x-777BB4)
![PHP >=7.4 <8.2](https://img.shields.io/badge/PHP->=7.4--<8.2-8892BF?logo=php)

```bash
composer require georgii-web/php-typed-values:^1
```

### Why

- Strong typing for scalars with runtime validation
- Immutable and self‑documenting values
- Safer constructors for your DTOs/VOs/Entities
- Great fit for static analysis
- Safe type conversion, no silent errors

### Quick start

#### Use existing typed values

```php
use PhpTypedValues\Integer\IntegerPositive;

$id = IntegerPositive::fromString('123');
```

Instead of spreading validation across an application

```php
$id = (int) '123';
if ($id <= 0) {
    throw new InvalidArgumentException('Invalid ID');
}
```

#### Create an alias (in your domain)

```php
use PhpTypedValues\Integer\IntegerPositive;

readonly class Id extends IntegerPositive {}

Id::fromInt(123);
```

#### Create composite objects

```php
final readonly class Profile
{
    public function __construct(
        private IntegerPositive $id,
        private StringUsername $username,
        private FloatPositive|Undefined $rating,
    ) {}
}
```

#### Undefined

Prefer using the `Undefined` type over `null` to maintain consistency and improve type safety within the codebase [null-is-evil](https://sidburn.github.io/blog/2016/03/20/null-is-evil).

#### Other usage examples 

[docs/USAGE.md](docs/USAGE.md)

### Key features

- Idempotent conversion on fromString() > toString(): "1" > 1 > "1"
- Static analysis friendly
- Strict types
- Validation on construction; no invalid state
- Immutable, readonly objects
- No external runtime dependencies
- Easy to extend with your own types and composites
- Heavily tested 

### Performance note

- Objects vs Scalars:
    - ~2.3× slower for large arrays of objects
    - ~1.5× higher memory usage
- Use value objects for domain boundaries, validation, and clarity
- Use raw scalars in hot loops or large data processing paths

### Documentation

- Development guide: [docs/DEVELOP.md](docs/DEVELOP.md)
- More usage examples in [tests/Unit](tests/Unit)

### Types structure

```MD
Types
├── ArrayType
│   ├── ArrayEmpty
│   ├── ArrayNonEmpty
│   ├── ArrayOfObjects
│   └── ArrayUndefined
├── Bool
│   ├── Alias
│   │   └── BooleanType
│   ├── Specific
│   │   ├── BoolSwitch
│   │   └── BoolToggle
│   ├── BoolStandard
│   ├── FalseStandard
│   └── TrueStandard
├── DateTime
│   ├── MariaDb
│   │   └── DateTimeSql
│   ├── Timestamp
│   │   ├── TimestampMicroseconds
│   │   ├── TimestampMilliseconds
│   │   └── TimestampSeconds
│   ├── DateIso8601
│   ├── DateTimeAtom
│   ├── DateTimeCookie
│   ├── DateTimeRFC1123
│   ├── DateTimeRFC2822
│   ├── DateTimeRFC3339
│   ├── DateTimeRFC3339Extended
│   ├── DateTimeW3C
│   └── TimeIso8601
├── Decimal
│   ├── Alias
│   │   └── Decimal
│   ├── Specific
│   │   ├──DecimalMoney
│   │   ├── DecimalPercent
│   │   └── DecimalProbability
│   ├── DecimalNegative
│   ├── DecimalNonNegative
│   ├── DecimalNonPositive
│   ├── DecimalNonZero
│   ├── DecimalPositive
│   ├── DecimalStandard
├── Float
│   ├── Alias
│   │   ├── DoubleType
│   │   └── FloatType
│   ├── Specific
│   │   ├── FloatPercent
│   │   └── FloatProbability
│   ├── FloatNegative
│   ├── FloatNonNegative
│   ├── FloatNonPositive
│   ├── FloatNonZero
│   ├── FloatPositive
│   └── FloatStandard
├── Integer
│   ├── Alias
│   │   └── IntegerType
│   ├── Specific
│   │   ├── IntegerAge
│   │   ├── IntegerDayOfMonth
│   │   ├── IntegerHour
│   │   ├── IntegerHttpStatusCode
│   │   ├── IntegerMinute
│   │   ├── IntegerMonth
│   │   ├── IntegerPercent
│   │   ├── IntegerPort
│   │   ├── IntegerSecond
│   │   ├── IntegerWeekDay
│   │   └── IntegerYear
│   ├── MariaDb
│   │   ├── IntegerBig
│   │   ├── IntegerBigUnsigned
│   │   ├── IntegerMedium
│   │   ├── IntegerMediumUnsigned
│   │   ├── IntegerNormal
│   │   ├── IntegerNormalUnsigned
│   │   ├── IntegerSmall
│   │   ├── IntegerSmallUnsigned
│   │   ├── IntegerTiny
│   │   └── IntegerTinyUnsigned
│   ├── IntegerNegative
│   ├── IntegerNonNegative
│   ├── IntegerNonPositive
│   ├── IntegerNonZero
│   ├── IntegerPositive
│   └── IntegerStandard
├── String
│   ├── Alias
│   │   └── StringType
│   ├── MariaDb
│   │   ├── StringLongText
│   │   ├── StringMediumText
│   │   ├── StringText
│   │   ├── StringTinyText
│   │   └── StringVarChar255
│   ├── Specific
│   │   ├── StringBase64
│   │   ├── StringCountryCode
│   │   ├── StringCurrencyCode
│   │   ├── StringDomain
│   │   ├── StringEmail
│   │   ├── StringFileName
│   │   ├── StringHex
│   │   ├── StringIban
│   │   ├── StringIpV4
│   │   ├── StringIpV6
│   │   ├── StringJson
│   │   ├── StringJwt
│   │   ├── StringLanguageCode
│   │   ├── StringLocaleCode
│   │   ├── StringMacAddress
│   │   ├── StringMd5
│   │   ├── StringMimeType
│   │   ├── StringPath
│   │   ├── StringPhoneE164
│   │   ├── StringSemVer
│   │   ├── StringSha256
│   │   ├── StringSha512
│   │   ├── StringSlug
│   │   ├── StringUrl
│   │   ├── StringUrlPath
│   │   ├── StringUsername
│   │   ├── StringUuidV4
│   │   └── StringUuidV7
│   ├── StringEmpty
│   ├── StringNonBlank
│   ├── StringNonEmpty
│   └── StringStandard
└── Undefined
    ├── Alias
    │   └── Undefined
    └── UndefinedStandard
````

### License

MIT