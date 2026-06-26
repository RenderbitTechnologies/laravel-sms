# GitHub Copilot Instructions

## Priority Guidelines

When generating code for this repository:

1. **Version Compatibility**: Always detect and respect the exact versions of languages, frameworks, and libraries used in this project
2. **Context Files**: Prioritize patterns and standards defined in the .github/copilot directory
3. **Codebase Patterns**: When context files don't provide specific guidance, scan the codebase for established patterns
4. **Architectural Consistency**: Maintain our layered architectural style and established boundaries
5. **Code Quality**: Prioritize maintainability, performance, security, accessibility, and testability in all generated code

## Technology Version Detection

Before generating code, scan the codebase to identify:

1. **Language Versions**: Detect the exact versions of programming languages in use
   - Examine project files, configuration files, and package managers
   - Look for language-specific version indicators (e.g., `<LangVersion>` in .NET projects)
   - Never use language features beyond the detected version

2. **Framework Versions**: Identify the exact versions of all frameworks
   - Check package.json, .csproj, pom.xml, requirements.txt, etc.
   - Respect version constraints when generating code
   - Never suggest features not available in the detected framework versions

3. **Library Versions**: Note the exact versions of key libraries and dependencies
   - Generate code compatible with these specific versions
   - Never use APIs or features not available in the detected versions

## Context Files

Prioritize the following files in .github/copilot directory (if they exist):

- **architecture.md**: System architecture guidelines
- **tech-stack.md**: Technology versions and framework details
- **coding-standards.md**: Code style and formatting standards
- **folder-structure.md**: Project organization guidelines
- **exemplars.md**: Exemplary code patterns to follow

## Codebase Scanning Instructions

When context files don't provide specific guidance:

1. Identify similar files to the one being modified or created
2. Analyze patterns for:
   - Naming conventions
   - Code organization
   - Error handling
   - Logging approaches
   - Documentation style
   - Testing patterns
   
3. Follow the most consistent patterns found in the codebase
4. When conflicting patterns exist, prioritize patterns in newer files or files with higher test coverage
5. Never introduce patterns not found in the existing codebase

## Code Quality Standards

### Maintainability
- Write self-documenting code with clear naming
- Follow the naming and organization conventions evident in the codebase
- Follow established patterns for consistency
- Keep functions focused on single responsibilities
- Limit function complexity and length to match existing patterns

### Performance
- Follow existing patterns for memory and resource management
- Match existing patterns for handling computationally expensive operations
- Follow established patterns for asynchronous operations
- Apply caching consistently with existing patterns
- Optimize according to patterns evident in the codebase

### Security
- Follow existing patterns for input validation
- Apply the same sanitization techniques used in the codebase
- Use parameterized queries matching existing patterns
- Follow established authentication and authorization patterns
- Handle sensitive data according to existing patterns

### Testability
- Follow established patterns for testable code
- Match dependency injection approaches used in the codebase
- Apply the same patterns for managing dependencies
- Follow established mocking and test double patterns
- Match the testing style used in existing tests

## Documentation Requirements

- Follow the exact documentation format found in the codebase
- Match the XML/JSDoc style and completeness of existing comments
- Document parameters, returns, and exceptions in the same style
- Follow existing patterns for usage examples
- Match class-level documentation style and content

## Testing Approach

### Unit Testing
- Match the exact structure and style of existing unit tests
- Follow the same naming conventions for test classes and methods
- Use the same assertion patterns found in existing tests
- Apply the same mocking approach used in the codebase
- Follow existing patterns for test isolation

### Integration Testing
- Follow the same integration test patterns found in the codebase
- Match existing patterns for test data setup and teardown
- Use the same approach for testing component interactions
- Follow existing patterns for verifying system behavior

## Technology-Specific Guidelines

### PHP Guidelines
- Detect and adhere to the specific PHP version in use (requires ^8.1)
- Follow PSR-12 coding style as documented in CONTRIBUTING.md
- Use strict typing and type declarations matching existing patterns
- Follow the exact same namespace organization found in the codebase
- Apply the same error handling patterns found in existing code
- Match collection types and approaches found in existing code
- Follow Laravel service provider patterns exactly as implemented

### Laravel Guidelines
- Detect and adhere to the specific Laravel version in use (10, 11, or 12)
- Follow the same service provider patterns found in the codebase
- Match facade implementation exactly as seen in existing code
- Apply the same dependency injection patterns found in the codebase
- Follow the exact config publishing and management approach
- Match the same singleton binding patterns used in existing code

## Version Control Guidelines

- Follow Semantic Versioning patterns as applied in the codebase
- Match existing patterns for documenting breaking changes
- Follow the same approach for deprecation notices

## General Best Practices

- Follow naming conventions exactly as they appear in existing code
- Match code organization patterns from similar files
- Apply error handling consistent with existing patterns
- Follow the same approach to testing as seen in the codebase
- Match logging patterns from existing code
- Use the same approach to configuration as seen in the codebase

## Project-Specific Guidance

- Scan the codebase thoroughly before generating any code
- Respect existing architectural boundaries without exception
- Match the style and patterns of surrounding code
- When in doubt, prioritize consistency with existing code over external best practices

## Specific Project Details

### Package Information
- **Name**: renderbit/laravel-sms
- **Type**: Laravel package (library)
- **Namespace**: Renderbit\Sms
- **License**: MIT

### Technology Stack
- **PHP**: ^8.1
- **Laravel**: 10, 11, or 12
- **HTTP Client**: Guzzle ^7.0
- **Testing**: PHPUnit ^10.0|^11.0|^12.0, Orchestra Testbench ^8.0|^9.0|^10.0, Mockery ^1.0

### Architecture
- **Service Provider Pattern**: SmsServiceProvider registers SmsClient as singleton
- **Facade Pattern**: Sms facade provides static access to SmsClient
- **Config-Driven**: All configuration via config/sms.php with env() support
- **Template Substitution**: Supports `{{ variable }}` placeholders in messages

### File Organization
```
src/
  SmsClient.php         — Core SMS sending logic
  SmsServiceProvider.php — Laravel service provider
  Facades/
    Sms.php              — Facade accessor
config/
  sms.php                — Default configuration
tests/
  TestCase.php           — Base test case using Orchestra Testbench
  Unit/                  — Unit tests for individual classes
  Feature/               — Integration tests through Laravel container
```

### Code Patterns to Follow

1. **Service Provider Registration**:
   ```php
   $this->app->singleton(SmsClient::class, fn() => new SmsClient(new Client()));
   ```

2. **Facade Implementation**:
   ```php
   protected static function getFacadeAccessor()
   {
       return SmsClient::class;
   }
   ```

3. **Error Handling**:
   - Catch `\Throwable` for broad exception handling
   - Log errors with context (phone number, error message)
   - Return boolean success/failure

4. **Testing Patterns**:
   - Use PHPUnit Attributes (#[Test])
   - Mock Guzzle HTTP calls with MockHandler
   - Test both success and failure scenarios
   - Verify configuration handling
   - Test template variable substitution

5. **Configuration Approach**:
   - Use env() with sensible defaults
   - Publish config via vendor:publish
   - Allow field name customization (number_field, message_field)

6. **Logging Patterns**:
   - Use Laravel's Log facade
   - Log warnings for empty inputs
   - Log info for successful sends and disabled state
   - Log errors with exception details

### Testing Guidelines

- **Test Base Class**: Extend `Renderbit\Sms\Tests\TestCase` (uses Orchestra Testbench)
- **Test Structure**: Unit tests in `tests/Unit/`, integration tests in `tests/Feature/`
- **Mocking**: Use Mockery for mocking, Guzzle MockHandler for HTTP testing
- **Assertions**: Use PHPUnit assertions, verify both return values and side effects
- **Configuration**: Test with different config values to ensure flexibility

### Commit Message Format

Use Conventional Commits:
```
<type>: <description>
```

Types: feat, fix, docs, refactor, test, ci

Examples:
- feat: add support for custom HTTP headers
- fix: handle empty message gracefully
- docs: update configuration examples
- test: add edge case tests for template substitution

### Pull Request Guidelines

- Keep PRs focused on a single change
- Include a clear description of what changed and why
- Reference any related issues (e.g., `Closes #12`)
- Ensure all tests pass in CI
- Update documentation if your change affects the public API
