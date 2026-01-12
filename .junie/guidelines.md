### Project Standards & Workflow

**Every prompt setting**
*   DO NOT RUN ANY TERMINAL COMMANDS!!! 
*   Try to fix existing test-files in the first place, in exceptional cases, fix src classes.

**Tech-Stack & Style**
*   **Core:** PHP 8.4 with `declare(strict_types=1);`.
*   **Standards:** Follow PSR-12. Use clean, meaningful naming conventions.
*   **Static Analysis:** Psalm v6 (Level 3).
*   **Environment:** Linux bash.

**Testing Strategy**
*   **Frameworks:** PEST v3 (use `it` syntax) & PHPUnit v11.
*   **Structure:** Test files must mirror the `src` directory structure.
*   **Requirements:** Maintain **100%** code coverage, type coverage, and mutation score.
*   **Classes in test-files:** Test abstract classes extending them with test implementation and name a new class with the suffix 'Test' 

**Workflow: Adding a New Type**
*   **Implementation:** Add the new type class to `src/{TypeName}/`.
*   **Exception:** Create a corresponding exception in `src/Exception/`.
*   **Testing:** Add unit tests to `tests/Unit/{TypeName}/{TypeName}Test.php`.

**Directory Structure**
*   `src/Base`: Internal framework base classes.
*   `src/{Type}`: Concrete type implementations (e.g., `src/Integer`).
*   `src/{Type}/Alias`: Concrete type aliases, just a new name (e.g., `src/Integer/Positive`).
*   `src/Usage`: Usage examples (prevents "unused code" false positives in Psalm).

**Testing scripts**
*   **Style fix:** Use `composer cs` script.
*   **Psalm check:** Use `composer sca` script.
*   **Usage check:** Use `composer usage` script.
*   **Unit testing:** Use `composer test` script.
*   **Type coverage:** Use `composer type` script.
*   **Test coverage:** Use `composer coverage` script.
*   **Mutation Testing:** Use `composer mutate` script.
