Develop
=======

This guide explains how to work on the project locally: running tests, static analysis, and code style.

Requirements
------------

- PHP 8.2+
- Composer 2.x

Install dependencies
--------------------

```
composer install
```

Composer scripts
----------------

Convenient scripts are defined in composer.json:

- composer test — run test suite (Pest)
- composer type — enforce 100% type coverage via Pest plugin
- composer coverage — run tests with coverage threshold
- composer sca — static analysis via Psalm
- composer cs — fix coding style with PHP-CS-Fixer

You can chain pre-commit checks via:

```
composer oncommit
```

Project layout
--------------

- src/ — library source code
- tests/ — test suite (Pest + PHPUnit)
- docs/ — documentation

Running specific tests
----------------------

```
./vendor/bin/pest --filter PositiveInt
```

Static analysis
---------------

Psalm is configured via psalm.xml. Run:

```
composer sca
```

Code style
----------

PHP-CS-Fixer is configured via .php-cs-fixer.dist.php. To fix styles:

```
composer cs
```

Optional: Docker
----------------

The repo contains a docker-compose.yml and a docker/php setup you can adapt. Typical workflow:

```
docker compose up -d
docker compose exec php composer install
docker compose exec php composer test
```

Contributing
------------

1. Fork and create a feature branch
2. Make changes with tests
3. Run composer oncommit to ensure checks pass
4. Open a PR
