### Project Standards & Workflow

**Every prompt setting**
* DO NOT CHANGE BASE CLASSES in `src/Base`!!!
* AVOID TO CHANGE OTHER CLASSES in `src`!!!
* Try to fix existing test-files in the first place, in exceptional cases, fix src classes.
    
**Add a new type workflow**
* Create a new type class in `src/{TypeName}/` or deeper to `Specific` etc. directory.
* Create a new type class in `src/{TypeName}/Alias/` or deeper directory.
* Create a new exception class in `src/Exception/{TypeName}/` or deeper directory.
* Create a new test class in `tests/Unit/{TypeName}/{TypeName}Test.php` file. Make 100% code/type/mutation coverage, copy the nearest tests file cases.
* Type class should not contain data formatters, validate data as it is.
* Run `docker-compose exec -ti php cs` to fix code style.
* Run `docker-compose exec -ti php composer sca` to check Psalm static analysis.
* Run `docker-compose exec -ti php composer test -- --filter="newTypeClassName"` to run tests.
* Run `docker-compose exec -ti php composer type -- --filter="newTypeClassName"` to run type coverage.
* Run `docker-compose exec -ti php composer coverage` to run code coverage.
* Run `docker-compose exec -ti php ./vendor/bin/pest tests/Unit/"NewClassType" --mutate --covered-only --parallel --min=100` to run mutation tests.

**"Fix psalm" prompt**
* Run `docker-compose exec -ti php composer sca` to check Psalm static analysis.

**"Fix test" or "Cover with tests" or "Kill mutants" prompt**
* FIX ONLY TEST FILES, DO NOT CREATE NEW TEST FILES, DO NOT CHANGE CLASSES FROM `src`.
* **Tests style** Use PEST syntax, wrap all tests in describe(), use it() and with() for datasets.
* Try to find a proper describe() group for a new test.
* If a class is created in a test – choose a name "ClassName" + Test, bc CSFixer will rename it.
* Choose short but meaningful names for test\describe methods, must be unique in a file.
* Avoid duplicate test cases or same data in a single test.
* Run `docker-compose exec -ti php composer test -- --filter="failedTypeClassName"` to run tests.
* Run `docker-compose exec -ti php composer type -- --filter="failedTypeClassName"` to run type coverage.
* Run `docker-compose exec -ti php composer coverage` to run code coverage.
* For mutation fails: run for each `docker-compose exec -ti php composer mutate -- --id=****` with a mutation error id.

**Tech-Stack & Style**
* **Core:** PHP 8.4 with `declare(strict_types=1);`.
* **Standards:** Follow PSR-12. Use clean, meaningful naming conventions.
* **Static Analysis:** Psalm v6 (Level 3).
* **Tests:** PEST v3.
* **Environment:** Linux bash.
