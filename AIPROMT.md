# Main project instructions

## Events

### On new type generation

- add new type to `src/"type name"` folder
- add new type assertions to `src/Code/Assert/Assert.php` if needed
- add new type tests to `tests/Unit/"type name"/"TypeName"Test.php`
- add new Exception class to `src/Exception` folder and use it

## Coding style

- PHP 8.2 used
- use strict typing
- follow PSR-12 coding standards
- use meaningful variable and function names
- keep code clean and readable
- PSALM v6 static analysis is used

## Folder structure 

- `src/Code` folder contains the framework internal code
- other folders like `src/"type"` contains specific types 
- `src/psalmTest.php` contains types usage to avoid Psalm issues like `unused method`
- `src/Code/Assert/Assert.php` contains `assert` methods, add new methods instead of checking conditions directly in classes. Prefer to use Assert::"methos()". 

## Tests

- are in `tests` folder
- used PhpUnit v11 and PEST v3
- use PEST syntax `it` instead of `test`
- keep the folder structure as tested src files
- test coverage should be 100%
- mutation tests used, avoid fails 

## Documentation

- keep it simple
- use Markdown format
- include project overview in README.md, short examples of how to install, use, and extend
- installation instructions, usage examples, develop instructions in `docs` folder