# CLAUDE.md - Pasta Project Documentation

## Project Overview

**Pasta** is an opinionated pastebin application (or "pastabin") built with Laravel 9.x. It's a personal project that allows users to share code snippets and text content with optional encryption, syntax highlighting, burn-on-read functionality, and expiration dates.

**Author:** Mateusz Micał (mateusz@mical.pl)
**License:** MIT
**Repository:** micalm/pasta

### Key Features
- Create and share text/code pastas with unique UUID-based URLs
- Optional password-based encryption using Argon2id and AES-256-GCM
- Syntax highlighting via CodeMirror (100+ languages)
- Burn-on-read (one-time view) functionality
- Pasta expiration with automatic cleanup
- Diff viewing for pasta revisions (parent-child relationships)
- Both web UI and REST API interfaces

---

## Technology Stack

### Backend
- **Framework:** Laravel 9.x (PHP 8.0.2+)
- **Database:** MySQL 8.0+ (other databases supported via Laravel)
- **Encryption:** Sodium (libsodium) with Argon2id for key derivation
- **Authentication:** Laravel Sanctum (for future user features)
- **Debugging:** Laravel Telescope (development only)

### Frontend
- **CSS Framework:** Tailwind CSS 3.x with custom Nord theme
- **Build Tool:** Laravel Mix (webpack wrapper)
- **JavaScript:** Vanilla JS with Alpine.js patterns
- **Code Editor:** CodeMirror 5.x
- **Date Picker:** Flatpickr 4.x
- **Fonts:** Comfortaa (sans), Fira Mono (monospace)

### Development Tools
- **Package Manager (PHP):** Composer
- **Package Manager (JS):** npm
- **Testing:** PHPUnit 9.5+
- **Browser Sync:** For local development hot-reloading

---

## Directory Structure

```
pasta/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── PastaClean.php          # Cleanup expired pastas (artisan command)
│   ├── Encryption/
│   │   └── PasswordEncrypter.php        # Custom password-based encryption
│   ├── Exceptions/
│   │   └── Handler.php                  # Global exception handling
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                     # API endpoints (JSON responses)
│   │   │   │   ├── PastaDiffController.php
│   │   │   │   ├── PastaShowController.php
│   │   │   │   └── PastaStoreController.php
│   │   │   └── Web/                     # Web endpoints (Blade views)
│   │   │       ├── PastaCreateController.php
│   │   │       ├── PastaShowController.php
│   │   │       └── PastaStoreController.php
│   │   ├── Middleware/                  # HTTP middleware
│   │   └── Kernel.php                   # HTTP kernel configuration
│   ├── Models/
│   │   ├── Pasta.php                    # Main Pasta model
│   │   └── User.php                     # User model (for future auth)
│   ├── Providers/                       # Service providers
│   └── Traits/
│       └── UsesUuidKeys.php             # UUID primary key trait
├── bootstrap/                           # Laravel bootstrap files
├── config/                              # Application configuration
├── database/
│   ├── factories/                       # Model factories for testing
│   ├── migrations/                      # Database migrations
│   └── seeders/                         # Database seeders
├── lang/                                # Localization files
├── public/                              # Public web root
│   ├── css/                             # Compiled CSS (gitignored)
│   ├── js/                              # Compiled JS (gitignored)
│   ├── img/                             # Static images
│   └── index.php                        # Application entry point
├── resources/
│   ├── css/
│   │   └── app.scss                     # Main stylesheet (uses Tailwind)
│   ├── js/
│   │   ├── app.js                       # Main JavaScript entry
│   │   ├── bootstrap.js                 # JS bootstrap (axios, etc.)
│   │   └── codemirror-langs.js          # CodeMirror language modes
│   └── views/                           # Blade templates
│       ├── app/                         # Reusable app components
│       ├── components/                  # Blade components
│       ├── errors/                      # Error pages (404, 500, etc.)
│       ├── layouts/                     # Layout templates
│       └── pasta/                       # Pasta-specific views
│           ├── create.blade.php         # New pasta form
│           └── show.blade.php           # Display pasta
├── routes/
│   ├── api.php                          # API routes (/api/*)
│   ├── channels.php                     # Broadcast channels
│   ├── console.php                      # Console commands
│   └── web.php                          # Web routes
├── storage/                             # Application storage
│   ├── app/                             # Application files
│   ├── framework/                       # Framework files (cache, sessions)
│   └── logs/                            # Application logs
├── tests/                               # PHPUnit tests
│   ├── Feature/                         # Feature tests
│   └── Unit/                            # Unit tests
├── .env.example                         # Environment configuration template
├── composer.json                        # PHP dependencies
├── package.json                         # JavaScript dependencies
├── phpunit.xml                          # PHPUnit configuration
├── tailwind.config.js                   # Tailwind CSS configuration
└── webpack.mix.js                       # Laravel Mix build configuration
```

---

## Key Architectural Patterns

### 1. Single-Action Controllers
Controllers follow the single-action pattern with a `handle()` method:

```php
class PastaStoreController extends Controller
{
    public function handle(Request $request)
    {
        // Single responsibility: store a pasta
    }
}
```

### 2. UUID Primary Keys
All pastas use UUIDs instead of auto-incrementing IDs for better security and obfuscation:

```php
// app/Traits/UsesUuidKeys.php
trait UsesUuidKeys
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
```

### 3. Password-Based Encryption
Custom encryption using Argon2id for key derivation:

```php
// app/Encryption/PasswordEncrypter.php
// Uses sodium_crypto_pwhash with ARGON2ID13
// Generates AES-256-GCM encrypted content
```

**Important:** The encryption key is derived from user passwords and NEVER stored. Only the salt is stored in the database.

### 4. Model Event Hooks
Automatic expiration checking on model retrieval:

```php
// app/Models/Pasta.php
protected static function booted()
{
    static::retrieved(function (Pasta $pasta) {
        // Delete expired pastas on fetch
        if (now()->greaterThanOrEqualTo(new Carbon($pasta->expires_at))) {
            $pasta->delete();
            throw new NotFoundHttpException('Pasta not found.');
        }
    });
}
```

### 5. Parent-Child Relationships
Pastas can have parent-child relationships for revision tracking:

```php
public function parent()
{
    return $this->hasOne(Pasta::class, 'uuid', 'parent_id');
}
```

---

## Database Schema

### Pastas Table

| Column        | Type         | Nullable | Description                           |
|---------------|--------------|----------|---------------------------------------|
| uuid          | UUID         | No       | Primary key                           |
| parent_id     | UUID         | Yes      | Reference to parent pasta (for diffs) |
| author        | VARCHAR(255) | Yes      | Author name (anonymous by default)    |
| content       | MEDIUMTEXT   | No       | Pasta content (encrypted or plaintext)|
| language      | VARCHAR(255) | Yes      | Syntax highlighting mode              |
| salt          | VARCHAR(255) | Yes      | Salt for password encryption          |
| encrypted     | BOOLEAN      | No       | Encryption flag (default: false)      |
| burn_on_read  | BOOLEAN      | No       | One-time view flag (added later)      |
| expires_at    | TIMESTAMP    | Yes      | Expiration timestamp (added later)    |
| created_at    | TIMESTAMP    | No       | Creation timestamp                    |
| updated_at    | TIMESTAMP    | No       | Last update timestamp                 |

**Migration Files:**
- `2022_03_25_072801_create_pastas_table.php` - Initial table
- `2022_05_28_060049_add_parent_to_pastas_table.php` - Parent relationship
- `2022_05_31_103215_add_burn_on_read_to_pastas_table.php` - Burn on read
- `2022_06_13_095044_add_expires_at_column_to_pastas_table.php` - Expiration

---

## Routing Structure

### Web Routes (`routes/web.php`)
```php
GET  /                   # Pasta creation form
GET  /p/{uuid}/{key?}    # View pasta (key optional for encrypted)
POST /p                  # Store new pasta
```

### API Routes (`routes/api.php`)
```php
GET  /api/p/{uuid}/{key?}         # Get pasta JSON
POST /api/p                       # Create pasta JSON
GET  /api/p/diff/{uuid}/{parent}  # Get diff between pastas
```

**URL Pattern:** Pastas are accessed via `/p/{uuid}` or `/p/{uuid}/{key}` for encrypted pastas.

---

## Development Workflows

### Initial Setup
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
# Edit .env with database credentials

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run dev        # Development build
npm run watch      # Watch mode with hot-reload
npm run production # Production build with minification
```

### Running the Application
```bash
# Development server
php artisan serve  # Serves at http://localhost:8000

# Production setup
# Serve from public/ directory using Apache/Nginx/Caddy
```

### Asset Compilation
```bash
npm run dev         # Quick development build
npm run watch       # Watch for changes
npm run production  # Minified production build (sets mix.version())
```

**Browser Sync:** In development, webpack.mix.js configures BrowserSync for `https://pasta.test`

### Database Management
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (WARNING: destroys data)
php artisan migrate:fresh
```

### Maintenance Commands
```bash
# Clean expired pastas
php artisan pasta:clean

# This should be scheduled in production (add to cron)
```

### Testing
```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/Unit/ExampleTest.php
```

---

## Key Features Implementation

### 1. Encryption Flow

**Creating Encrypted Pasta:**
1. User provides password (min 8 chars) in web form
2. Password is sent to server (over HTTPS only!)
3. Server generates random salt (SODIUM_CRYPTO_PWHASH_SALTBYTES)
4. Argon2id derives 32-byte key from password + salt
5. Content encrypted with AES-256-GCM
6. Salt stored (base64 encoded), password discarded
7. UUID returned to user, optionally with key in URL

**Decrypting Pasta:**
1. User provides UUID and password
2. Server retrieves pasta and salt from database
3. Same key derivation process (Argon2id)
4. Content decrypted with derived key
5. If decryption fails, wrong password error shown

**Security Note:** The password is transmitted to the server but NEVER stored. This is server-side encryption, not client-side. Users must trust the server operator.

### 2. Burn on Read

When `burn_on_read` is enabled:
- First view is tracked in session (`firstView` session key)
- If viewer is NOT the creator (session check), pasta is deleted after viewing
- Creator gets one "safe" view before the pasta burns

**Implementation location:** `app/Http/Controllers/Web/PastaShowController.php`

### 3. Expiration Handling

**Two-tier system:**
1. **On-retrieval check:** Model `retrieved` event deletes expired pastas immediately when fetched
2. **Scheduled cleanup:** `php artisan pasta:clean` command deletes expired pastas in bulk

**Recommended:** Schedule the cleanup command to run hourly/daily in production.

### 4. Diff Viewing

Uses `sebastian/diff` library (same library used by PHPUnit):
- Parent pasta referenced via `parent_id` field
- Diff generated using StrictUnifiedDiffOutputBuilder
- Shows changes between two pasta versions
- API endpoint: `/api/p/diff/{uuid}/{parent}`

### 5. Syntax Highlighting

**CodeMirror Integration:**
- 100+ language modes available (see `resources/js/codemirror-langs.js`)
- Mode selection in pasta creation form
- Some languages have duplicate entries for user convenience (e.g., "JavaScript", "JS", "Node.js")

---

## Frontend Architecture

### Tailwind Configuration

**Custom Color Palette - Nord Theme:**
```javascript
// tailwind.config.js
colors: {
  'nord': {
    'night': { base, light, lighter, lightest },   // Dark backgrounds
    'storm': { base, light, lighter },              // Light backgrounds
    'frost': { green, cyan, blue, bluer },          // Primary colors
    'aurora': { red, orange, yellow, green, purple } // Accent colors
  }
}
```

**Custom Fonts:**
- Sans: Comfortaa
- Mono: Fira Mono, Courier New

### Build Configuration

**webpack.mix.js:**
- Compiles `resources/js/app.js` → `public/js/app.js`
- Compiles `resources/css/app.scss` → `public/css/app.css`
- Extracts vendor libraries to separate bundle
- Versioning enabled in production (`mix.version()`)
- BrowserSync at `https://pasta.test` for local dev

### JavaScript Structure

**Main files:**
- `app.js` - Application entry point
- `bootstrap.js` - Loads axios, sets CSRF token
- `codemirror-langs.js` - CodeMirror language mode configurations

**Dependencies:**
- axios - HTTP client
- lodash - Utility library
- codemirror - Code editor
- flatpickr - Date/time picker

---

## Testing

### Current Test Coverage
- Basic Example tests exist in `tests/Feature/` and `tests/Unit/`
- **Note:** Test coverage is minimal; this is an area for improvement

### Running Tests
```bash
./vendor/bin/phpunit
```

### Test Environment
- Configuration: `phpunit.xml`
- Uses in-memory SQLite database (configured in phpunit.xml)
- Inherits Laravel's TestCase and CreatesApplication trait

---

## Security Considerations

### 1. Encryption Model
- **NOT client-side encryption** - password sent to server
- Trust model: Users must trust server operator
- Key derivation: Argon2id with moderate ops/mem limits
- Encryption: AES-256-GCM (authenticated encryption)

### 2. CSRF Protection
- Laravel CSRF middleware active on all POST routes
- CSRF token in all web forms
- API routes can optionally use Sanctum tokens

### 3. Input Validation
```php
protected $rules = [
    'author' => 'min:1|max:255',
    'content' => 'required|min:1|max:16777215',  // MEDIUMTEXT limit
    'key' => 'nullable|min:8',                    // Min password length
];
```

### 4. UUID Obfuscation
- No sequential IDs exposed
- Harder to enumerate pastas
- UUIDs are cryptographically random

### 5. Common Vulnerabilities
- **XSS:** Blade templates auto-escape output (`{{ }}`)
- **SQL Injection:** Eloquent ORM with prepared statements
- **CSRF:** Laravel middleware protection
- **Session Fixation:** Laravel handles session regeneration

**AI Assistant Note:** When adding features, maintain these security standards:
- Always validate and sanitize input
- Use Eloquent ORM, never raw queries with user input
- Use `{{ }}` for output, never `{!! !!}` unless absolutely necessary
- Keep CSRF protection enabled
- Validate file uploads if that feature is added

---

## Configuration Files

### Environment Variables (.env)

**Critical settings:**
```bash
APP_ENV=production          # Set to 'production' in prod
APP_DEBUG=false             # MUST be false in production
APP_KEY=base64:...          # Generated by php artisan key:generate
APP_URL=https://pasta.app   # Your domain

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pasta
DB_USERNAME=pasta
DB_PASSWORD=securepassword
```

**Note:** The APP_KEY is NOT used for pasta encryption. It's used by Laravel for session encryption, cookie encryption, etc.

### .gitignore

**Important ignored files:**
- `/node_modules` - npm packages
- `/vendor` - Composer packages
- `.env` - Environment config (contains secrets)
- `/public/js/` and `/public/css/` - Compiled assets
- `/storage/*.key` - Encryption keys

**AI Assistant Note:** Never commit these files. If you create new sensitive config, add to .gitignore.

---

## Common Development Tasks

### Adding a New Migration
```bash
php artisan make:migration create_table_name --create=table_name
php artisan make:migration add_column_to_table --table=table_name
php artisan migrate
```

### Creating a New Controller
```bash
php artisan make:controller ControllerName
```

Follow the single-action pattern:
```php
namespace App\Http\Controllers\Web;

class NewController extends Controller
{
    public function handle(Request $request)
    {
        // Single action logic
    }
}
```

### Adding a New Route
**Web routes:** Edit `routes/web.php`
```php
Route::get('/path', [ControllerName::class, 'handle'])->name('route.name');
```

**API routes:** Edit `routes/api.php`
```php
Route::get('/api/path', [ControllerName::class, 'handle']);
```

### Adding a New Blade View
Create in `resources/views/` with `.blade.php` extension:
```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        {{ $variable }}
    </div>
@endsection
```

### Adding Frontend Dependencies
```bash
npm install package-name --save
npm install package-name --save-dev  # For dev dependencies
npm run dev  # Rebuild after adding packages
```

### Adding Backend Dependencies
```bash
composer require vendor/package
composer require --dev vendor/package  # For dev dependencies
```

---

## Code Style Conventions

### PHP
- **PSR-4 autoloading:** Namespace matches directory structure
- **Class naming:** PascalCase (e.g., `PastaStoreController`)
- **Method naming:** camelCase (e.g., `handle()`, `getDiff()`)
- **Properties:** camelCase with visibility modifiers
- **Constants:** SCREAMING_SNAKE_CASE
- **Indentation:** 4 spaces (standard Laravel)

### JavaScript
- **Variable naming:** camelCase
- **Constants:** SCREAMING_SNAKE_CASE or camelCase
- **Indentation:** 4 spaces (matches PHP for consistency)

### Blade Templates
- **Indentation:** 4 spaces
- **Echo:** Use `{{ $var }}` for escaped output
- **Raw echo:** Only use `{!! $var !!}` when absolutely necessary (security risk)
- **Comments:** `{{-- Comment --}}`

### Database
- **Table names:** Plural, snake_case (e.g., `pastas`)
- **Column names:** snake_case (e.g., `burn_on_read`, `expires_at`)
- **Foreign keys:** `{related}_id` (e.g., `parent_id`)
- **Timestamps:** `created_at`, `updated_at` (Laravel convention)

---

## AI Assistant Guidelines

### When Contributing to This Project

1. **Read Before Writing**
   - Always read existing files before modifying
   - Understand the existing patterns and conventions
   - Match the coding style of surrounding code

2. **Security First**
   - Validate all user input
   - Use Eloquent ORM for database queries
   - Never disable CSRF protection
   - Escape output in Blade templates
   - Consider encryption implications

3. **Follow Laravel Conventions**
   - Use Eloquent models for database operations
   - Follow RESTful routing patterns
   - Use single-action controllers with `handle()` method
   - Leverage Laravel's built-in features (validation, middleware, etc.)

4. **Testing**
   - Write tests for new features
   - Run existing tests before committing: `./vendor/bin/phpunit`
   - Ensure migrations are reversible

5. **Database Changes**
   - Always create migrations, never modify database directly
   - Use `up()` and `down()` methods properly
   - Test migration rollbacks

6. **Frontend Development**
   - Use Tailwind CSS classes (avoid custom CSS when possible)
   - Follow Nord theme color palette
   - Maintain accessibility standards
   - Test in multiple browsers

7. **Dependencies**
   - Justify new dependencies
   - Prefer Laravel ecosystem packages
   - Update `composer.json` or `package.json` appropriately
   - Document breaking changes

8. **Git Workflow**
   - Write clear, descriptive commit messages
   - Branch naming: `feature/feature-name`, `fix/issue-description`
   - Don't commit `.env`, compiled assets, or vendor directories
   - Keep commits focused and atomic

9. **Documentation**
   - Update README.md if user-facing features change
   - Update this CLAUDE.md if architecture changes
   - Comment complex logic
   - Use PHPDoc for public methods

10. **Performance**
    - Use eager loading to prevent N+1 queries
    - Consider caching for expensive operations
    - Optimize database queries
    - Minimize asset bundle sizes

### Common Pitfalls to Avoid

1. **Don't hardcode configuration** - Use `.env` and `config/*` files
2. **Don't store sensitive data in Git** - Use environment variables
3. **Don't bypass Laravel's security features** - CSRF, validation, etc.
4. **Don't use raw SQL with user input** - Use Eloquent or query builder
5. **Don't commit compiled assets** - `/public/js/` and `/public/css/` are gitignored
6. **Don't modify vendor code** - Use service providers or extend classes
7. **Don't skip migrations** - Always create migrations for schema changes
8. **Don't ignore errors** - Handle exceptions appropriately

### Questions to Ask Before Making Changes

1. Does this follow Laravel conventions?
2. Is this secure (especially for user input)?
3. Will this break existing functionality?
4. Do I need to update the database schema?
5. Should I write a test for this?
6. Does this need to be configurable via .env?
7. Am I introducing new dependencies?
8. Will this affect performance?
9. Do I need to update documentation?
10. Is there a simpler Laravel-native way to do this?

---

## Future Roadmap

According to README.md, planned features include:

1. **Client-side encryption** - True end-to-end encryption
2. **User login/registration** - Full user system with authentication
3. **Web frontend using Alpine.js** - Enhanced interactivity
4. **Clean up initial Laravel files** - Remove boilerplate

### Considerations for AI Assistants Working on These Features

**Client-side encryption:**
- Will require JavaScript crypto libraries
- Key management UX is critical
- Consider backward compatibility with server-side encryption
- Update documentation about trust model changes

**User system:**
- Laravel Sanctum already included
- User model exists but unused
- Consider user-owned pastas
- Privacy implications (logging, paste ownership)
- Migration path for anonymous pastas

**Alpine.js integration:**
- Already using Tailwind (Alpine.js pairs well)
- Progressive enhancement approach
- Maintain accessibility
- Consider build process changes

---

## Troubleshooting

### Common Issues

**Issue:** "No application encryption key has been specified"
```bash
php artisan key:generate
```

**Issue:** Database connection errors
- Check `.env` database credentials
- Ensure MySQL is running
- Verify database exists: `CREATE DATABASE pasta;`

**Issue:** "Class not found" errors
```bash
composer dump-autoload
```

**Issue:** Assets not loading
```bash
npm run dev
# Or for production:
npm run production
```

**Issue:** Permission errors on storage/
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Linux/Apache
```

**Issue:** Migrations fail
- Check database credentials in `.env`
- Ensure migration files are in correct order
- Check for syntax errors in migration files

---

## Resources

### Official Documentation
- [Laravel 9.x Documentation](https://laravel.com/docs/9.x)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [CodeMirror Documentation](https://codemirror.net/5/doc/manual.html)

### Key Laravel Concepts for This Project
- [Eloquent ORM](https://laravel.com/docs/9.x/eloquent)
- [Routing](https://laravel.com/docs/9.x/routing)
- [Blade Templates](https://laravel.com/docs/9.x/blade)
- [Migrations](https://laravel.com/docs/9.x/migrations)
- [Validation](https://laravel.com/docs/9.x/validation)
- [Encryption](https://laravel.com/docs/9.x/encryption)

### Contributing
Per README.md: "Any contributions are welcome, but you really should consider helping other FLOSS projects first. They need it more."

---

## Contact

**Project Maintainer:** Mateusz Micał
**Email:** mateusz@mical.pl

---

*Last Updated: 2025-11-15*
*This document should be updated when significant architectural changes occur.*
