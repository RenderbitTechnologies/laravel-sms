# Contributing to Laravel SMS

Thanks for your interest in contributing! This guide will help you get started.

## Requirements

- PHP 8.1+
- Composer
- Laravel 10, 11, or 12 (for testing via Orchestra Testbench)

## Getting Started

1. **Fork and clone** the repository:

   ```bash
   git clone https://github.com/<your-username>/laravel-sms.git
   cd laravel-sms
   ```

2. **Install dependencies**:

   ```bash
   composer install
   ```

3. **Run the tests** to make sure everything works:

   ```bash
   vendor/bin/phpunit
   ```

## Project Structure

```
src/
  SmsClient.php         — Core SMS sending logic and template substitution
  SmsServiceProvider.php — Laravel service provider
  Facades/
    Sms.php              — Facade accessor for SmsClient
config/
  sms.php                — Default package configuration
tests/
  TestCase.php           — Base test case using Orchestra Testbench
  Unit/                  — Unit tests for individual classes
  Feature/               — Integration tests through the Laravel container
```

## Development Workflow

### 1. Create a Branch

```bash
git checkout -b feature/my-feature
```

Use a descriptive branch name:
- `feature/description` for new features
- `fix/description` for bug fixes
- `docs/description` for documentation changes

### 2. Make Your Changes

- Follow **PSR-12** coding style
- Keep changes focused — one logical change per commit
- Add or update tests for any new functionality
- Ensure all existing tests still pass

### 3. Run Tests

```bash
vendor/bin/phpunit
```

All tests must pass before submitting a pull request.

### 4. Commit

Use [Conventional Commits](https://www.conventionalcommits.org/) format:

```
<type>: <description>
```

Common types:
| Type       | Use for                          |
|------------|----------------------------------|
| `feat`     | New feature                      |
| `fix`      | Bug fix                          |
| `docs`     | Documentation only               |
| `refactor` | Code change that neither fixes nor adds |
| `test`     | Adding or updating tests         |
| `ci`       | CI/config changes                |

Examples:
```
feat: add support for custom HTTP headers
fix: handle empty message gracefully
docs: update configuration examples
test: add edge case tests for template substitution
```

### 5. Push and Open a Pull Request

```bash
git push origin feature/my-feature
```

Then open a pull request against `master`. Fill out the PR template completely.

## Pull Request Guidelines

- Keep PRs focused on a single change
- Include a clear description of what changed and why
- Reference any related issues (e.g., `Closes #12`)
- Ensure all tests pass in CI
- Update documentation if your change affects the public API

## Adding Tests

Tests live in `tests/Unit/` or `tests/Feature/` and extend `Renderbit\Sms\Tests\TestCase`.

```php
<?php

namespace Renderbit\Sms\Tests\Unit;

use Renderbit\Sms\Tests\TestCase;
use Renderbit\Sms\SmsClient;

class MyTest extends TestCase
{
    public function test_something(): void
    {
        // Your test here
    }
}
```

- **Unit tests** — test individual classes in isolation (mock HTTP calls with Mockery)
- **Feature tests** — test through the Laravel container using Orchestra Testbench

## Reporting Issues

Use the [issue templates](https://github.com/RenderbitTechnologies/laravel-sms/issues/new/choose) when reporting bugs or requesting features. Include:

- PHP and Laravel versions
- Package version
- Steps to reproduce (for bugs)
- Code samples where relevant

## Code of Conduct

Be respectful and constructive. We're here to build something useful together.

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE).
