### Project Standards & Workflow

**Tech Stack & Style**
*   **Core:** PHP 8.2 with `declare(strict_types=1);`.
*   **Standards:** Follow PSR-12. Use clean, meaningful naming conventions.
*   **Static Analysis:** Psalm v6 (Level 1).

**Testing Strategy**
*   **Frameworks:** PEST v3 (use `it` syntax) & PHPUnit v11.
*   **Structure:** Test files must mirror the `src` directory structure.
*   **Requirements:** Maintain **100%** code coverage, type coverage, and mutation score.

**Workflow: Adding a New Type**
* **Implementation:** Add the new type class to `src/{TypeName}/`.
* **Exception:** Create a corresponding exception in `src/Exception/`.
* **Testing:** Add unit tests to `tests/Unit/{TypeName}/{TypeName}Test.php`.

**Directory Structure**
*   `src/Abstract`: Internal framework base classes.
*   `src/{Type}`: Concrete type implementations (e.g., `src/Integer`).
*   `src/{Type}/Alias`: Concrete type aliases, just a new name (e.g., `src/Integer/Positive`).
*   `src/Usage`: Usage examples (prevents "unused code" false positives in Psalm).