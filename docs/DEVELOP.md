Develop
=======

This guide explains how to work on the project locally: running tests, static analysis, and code style.

Requirements
------------

- PHP 8.4
- Composer 2.x

Install dependencies
--------------------

```
composer install
```

After checkout: prepare the project
-----------------------------------

Before you start developing or running examples/tests, run the following Composer script to ensure your local environment is ready and all the latest packages are installed:

```
composer oncheckout
```

This script will:

- install/update Composer dependencies
- regenerate the autoloader (composer dump-autoload)
- clear Psalm cache to avoid stale analysis state

Run it right after cloning the repository or switching branches so that you work against the latest dependencies and tools.

Before PR: run full checks
--------------------------

Before opening a Pull Request, run the following Composer script to execute the full local QA pipeline and ensure your changes pass all checks:

```
composer oncommit
```

This script will run, in order:

- composer validate (strict mode)
- coding style fix/checks (PHP-CS-Fixer)
- static analysis (Psalm)
- test suite (Pest)
- type coverage check (100% via Pest plugin)
- code coverage check (100% minimum)
- mutation tests (100% minimum)

If this command succeeds locally, your PR should pass CI checks.

Optional: Docker
----------------

The repo contains a docker-compose.yml and a docker/php setup you can adapt. Typical workflow:

```
docker compose up -d
docker compose exec php composer install
docker compose exec php composer oncommit
```

Contributing
------------

1. Fork and create a feature branch
2. Make changes with tests
3. Run `composer oncommit` to ensure checks pass
4. Open a PR
