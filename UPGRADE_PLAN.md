# Package Upgrade Plan - Pasta Project

**Last Updated:** 2025-11-15
**Current State:** Laravel 9.5.1, PHP 8.0.2+, Laravel Mix 6
**Target State:** Laravel 11.x, PHP 8.3+, Vite

---

## Executive Summary

This document outlines a phased approach to upgrading the Pasta project to modern package versions. The upgrade addresses:

- **Critical Security Issues:** Axios 0.25 has known vulnerabilities
- **Framework Modernization:** Laravel 9 → Laravel 11 (current LTS)
- **Build Tool Migration:** Laravel Mix (deprecated) → Vite (Laravel's new standard)
- **PHP Version:** 8.0.2 → 8.3 (or 8.4 for future-proofing)
- **Dependency Updates:** All packages to latest stable versions

---

## Current Package Inventory

### PHP Dependencies (composer.json)

| Package | Current | Latest | Status |
|---------|---------|--------|--------|
| php | ^8.0.2 | 8.4.x | Can upgrade to ^8.2 or ^8.3 |
| laravel/framework | ^9.2 (9.5.1) | ^11.37 | Major upgrade needed |
| laravel/sanctum | ^2.14.1 | ^4.0 | Major upgrade with Laravel 11 |
| laravel/tinker | ^2.7 | ^2.10 | Minor upgrade |
| guzzlehttp/guzzle | ^7.2 | ^7.9 | Minor upgrade |
| sebastian/diff | ^4.0 | ^6.0 | Major upgrade available |

### PHP Dev Dependencies

| Package | Current | Latest | Status |
|---------|---------|--------|--------|
| fakerphp/faker | ^1.9.1 | ^1.23 | Minor upgrade |
| laravel/sail | ^1.0.1 | ^1.37 | Major upgrade available |
| laravel/telescope | ^4.9 | ^5.2 | Major upgrade with Laravel 11 |
| mockery/mockery | ^1.4.4 | ^1.6 | Minor upgrade |
| nunomaduro/collision | ^6.1 | ^8.5 | Major upgrade with Laravel 11 |
| phpunit/phpunit | ^9.5.10 | ^11.4 | Major upgrade available |
| spatie/laravel-ignition | ^1.0 | ^2.8 | Major upgrade with Laravel 11 |

### JavaScript Dependencies (package.json)

| Package | Current | Latest | Status |
|---------|---------|--------|--------|
| axios | ^0.25 | ^1.7 | **SECURITY CRITICAL** |
| browser-sync | ^2.27.9 | ^3.0 | Major upgrade available |
| laravel-mix | ^6.0.6 | **DEPRECATED** | Migrate to Vite |
| lodash | ^4.17.19 | ^4.17.21 | Patch update |
| postcss | ^8.1.14 | ^8.4 | Minor upgrade |
| sass | ^1.49.9 | ^1.80 | Minor upgrade |
| sass-loader | ^12.6.0 | ^16.0 | Major upgrade (with Webpack 5) |
| tailwindcss | ^3.0.23 | ^3.4.16 | Minor upgrade |
| @tailwindcss/forms | ^0.5.2 | ^0.5.9 | Patch update |
| codemirror | ^5.65.2 | ^6.x | Major rewrite (optional) |
| flatpickr | ^4.6.13 | ^4.6.13 | Up to date |

---

## Upgrade Phases

### Phase 1: Critical Security & Minor Updates (Low Risk)

**Objective:** Fix security vulnerabilities and update compatible packages
**Risk Level:** LOW
**Estimated Time:** 2-4 hours
**Downtime:** None (development only)

#### Steps:

1. **Update Axios (CRITICAL)**
   ```bash
   npm install axios@^1.7.7
   ```
   - **Breaking Changes:**
     - Axios 1.x changes default `validateStatus` behavior
     - Error response structure slightly different
   - **Testing Required:** All API calls in resources/js/

2. **Update Minor PHP Packages**
   ```bash
   composer update guzzlehttp/guzzle
   composer update laravel/tinker
   composer update mockery/mockery
   composer update fakerphp/faker
   ```
   - **Breaking Changes:** None expected
   - **Testing Required:** Run PHPUnit tests

3. **Update Minor JavaScript Packages**
   ```bash
   npm update lodash
   npm update @tailwindcss/forms
   npm update tailwindcss
   npm update postcss
   npm update sass
   ```
   - **Breaking Changes:** None expected (Tailwind may have new utilities)
   - **Testing Required:** Rebuild assets and visual testing

4. **Verification**
   ```bash
   ./vendor/bin/phpunit
   npm run dev
   # Manual testing of encryption, burn-on-read, expiration
   ```

#### Files to Review:
- `resources/js/bootstrap.js` - Axios configuration
- Any controllers using Guzzle HTTP client
- Frontend components using Axios

---

### Phase 2: PHP 8.2/8.3 Upgrade (Medium Risk)

**Objective:** Upgrade to PHP 8.2 or 8.3 for better performance and features
**Risk Level:** MEDIUM
**Estimated Time:** 4-6 hours
**Downtime:** Deployment only

#### Prerequisites:
- Phase 1 completed
- Server/environment supports PHP 8.2+

#### Steps:

1. **Update PHP Requirement**
   ```json
   // composer.json
   "require": {
       "php": "^8.2",
       // ... other packages
   }
   ```

2. **Test for Deprecated Features**
   ```bash
   # Enable deprecation warnings
   # Check logs for dynamic properties, null parameter warnings
   ```

3. **Update PHP-Specific Code**
   - Review all classes for dynamic properties (use attributes if needed)
   - Check for deprecated string interpolation syntax
   - Review null coalescing usage

4. **Composer Update**
   ```bash
   composer update
   ```

5. **Testing**
   ```bash
   ./vendor/bin/phpunit
   # Manual testing of all features
   ```

#### Potential Breaking Changes:
- **Dynamic Properties:** PHP 8.2 deprecates dynamic properties
  - May affect: Model attributes if not properly defined in `$fillable`
- **Null Parameter Deprecations:** Functions expecting non-null may warn
- **String Interpolation:** Deprecated `${}` syntax in strings

#### Files to Review:
- `app/Models/Pasta.php` - Ensure all properties declared
- `app/Encryption/PasswordEncrypter.php` - Check for null handling
- All controllers and middleware

---

### Phase 3: Laravel 11 Upgrade (High Risk)

**Objective:** Upgrade to Laravel 11.x LTS
**Risk Level:** HIGH
**Estimated Time:** 16-24 hours
**Downtime:** Deployment + potential rollback time

#### Prerequisites:
- Phase 1 & 2 completed
- PHP 8.2+ installed
- Full backup of database and code
- Staging environment for testing

#### Major Changes in Laravel 11:

1. **Directory Structure Changes**
   - `app/Http/Middleware` - Some middleware moved to bootstrap
   - `config/` - Streamlined configuration files
   - Service providers consolidated

2. **New Features**
   - SQLite as default database (we use MySQL, no impact)
   - Health route at `/up`
   - Improved rate limiting
   - Per-second rate limiting

3. **Removed/Changed**
   - Some middleware auto-registered
   - `TrustProxies` and `TrustHosts` configuration changes
   - Route model binding changes

#### Steps:

1. **Update composer.json Dependencies**
   ```json
   {
       "require": {
           "php": "^8.2",
           "laravel/framework": "^11.0",
           "laravel/sanctum": "^4.0",
           "laravel/tinker": "^2.10"
       },
       "require-dev": {
           "fakerphp/faker": "^1.23",
           "laravel/sail": "^1.37",
           "laravel/telescope": "^5.2",
           "mockery/mockery": "^1.6",
           "nunomaduro/collision": "^8.0",
           "phpunit/phpunit": "^11.0",
           "spatie/laravel-ignition": "^2.0"
       }
   }
   ```

2. **Install Dependencies**
   ```bash
   composer update
   ```

3. **Update Configuration Files**
   - Review `config/app.php` - Service providers may change
   - Update `config/sanctum.php` for v4.0
   - Review `config/database.php`
   - Check `config/session.php` for new options

4. **Update Middleware Registration**
   - Laravel 11 auto-registers common middleware
   - Review `app/Http/Kernel.php` (or new bootstrap structure)
   - May need to remove duplicate registrations

5. **Update PHPUnit Configuration**
   - PHPUnit 11 has breaking changes
   - Update `phpunit.xml` for new syntax
   - Review test assertions (some deprecated methods removed)

6. **Review Model Changes**
   - Check `app/Models/Pasta.php` for new attributes
   - Review `$fillable` and `$guarded` patterns
   - Test UUID trait compatibility

7. **Database Migration Review**
   - All migrations should still work
   - Laravel 11 uses anonymous migration classes (already present)

8. **Testing Strategy**
   ```bash
   # Unit tests
   ./vendor/bin/phpunit

   # Manual feature testing checklist:
   # - Create pasta (unencrypted)
   # - Create pasta (encrypted)
   # - View pasta (encrypted, correct password)
   # - View pasta (encrypted, wrong password)
   # - Burn on read functionality
   # - Expiration functionality
   # - Diff viewing
   # - API endpoints (/api/p/{uuid}, /api/p/diff)
   # - pasta:clean command
   ```

#### Files Requiring Review:

**Critical:**
- `app/Http/Kernel.php` → May move to `bootstrap/app.php`
- `app/Providers/*.php` - Service provider registration
- `config/*.php` - All config files
- `routes/*.php` - Route registration (should be compatible)
- `phpunit.xml` - PHPUnit 11 compatibility

**Medium Priority:**
- `app/Models/Pasta.php` - Model attributes and casts
- `app/Encryption/PasswordEncrypter.php` - Encryption compatibility
- `app/Console/Commands/PastaClean.php` - Command signature
- All controllers - Validate request/response handling

**Low Priority:**
- `tests/**/*.php` - Update test assertions if needed
- `.env.example` - New environment variables

#### Rollback Plan:
```bash
# If issues arise:
1. git revert <commit-hash>
2. composer install (restore composer.lock)
3. php artisan migrate:rollback (if migrations run)
4. Deploy previous version
```

#### Known Breaking Changes to Address:

1. **Sanctum 4.0:**
   - Configuration changes in `config/sanctum.php`
   - Token abilities syntax may differ

2. **PHPUnit 11:**
   - `expectException()` behavior changes
   - Some assertion methods removed
   - `@test` annotation removed (use `test` prefix or `#[Test]` attribute)

3. **Collision 8.0:**
   - Error reporting format may differ (visual only)

---

### Phase 4: Laravel Mix → Vite Migration (High Risk)

**Objective:** Migrate from deprecated Laravel Mix to Vite
**Risk Level:** HIGH
**Estimated Time:** 12-16 hours
**Downtime:** None (development only)

#### Why Migrate?
- Laravel Mix is deprecated and no longer maintained
- Vite is Laravel's official build tool (since Laravel 9.19)
- Vite provides faster HMR (Hot Module Replacement)
- Better developer experience
- Future Laravel versions will not support Mix

#### Prerequisites:
- Phases 1-3 completed (or can be done independently)
- Node.js 18+ installed

#### Steps:

1. **Install Vite and Laravel Plugin**
   ```bash
   npm install --save-dev vite laravel-vite-plugin
   ```

2. **Remove Laravel Mix**
   ```bash
   npm uninstall laravel-mix browser-sync browser-sync-webpack-plugin
   npm uninstall resolve-url-loader sass-loader
   ```

3. **Create vite.config.js**
   ```javascript
   import { defineConfig } from 'vite';
   import laravel from 'laravel-vite-plugin';

   export default defineConfig({
       plugins: [
           laravel({
               input: [
                   'resources/css/app.scss',
                   'resources/js/app.js',
               ],
               refresh: true,
           }),
       ],
       resolve: {
           alias: {
               '~': '/node_modules',
           },
       },
   });
   ```

4. **Update package.json Scripts**
   ```json
   {
       "scripts": {
           "dev": "vite",
           "build": "vite build",
           "preview": "vite preview"
       }
   }
   ```

5. **Update Blade Templates**

   Replace Mix asset helpers with Vite directives:

   **Before (Mix):**
   ```blade
   <link rel="stylesheet" href="{{ mix('css/app.css') }}">
   <script src="{{ mix('js/app.js') }}" defer></script>
   ```

   **After (Vite):**
   ```blade
   @vite(['resources/css/app.scss', 'resources/js/app.js'])
   ```

6. **Update Asset References**

   If using `asset()` or `public_path()` for compiled assets:
   - Vite uses different output directory structure
   - Update references to use `@vite` directive

7. **Update .gitignore**
   ```
   # Remove Mix-specific
   /public/mix-manifest.json

   # Add Vite-specific
   /public/build/
   /public/hot
   ```

8. **Handle SCSS/PostCSS**

   Vite handles PostCSS automatically. Update if needed:
   ```bash
   npm install --save-dev sass
   ```

   PostCSS config (`postcss.config.js`) should work as-is:
   ```javascript
   module.exports = {
       plugins: {
           tailwindcss: {},
           autoprefixer: {},
       },
   }
   ```

9. **Update Environment Variables**

   Vite uses `VITE_` prefix for env variables exposed to frontend:
   ```env
   # .env - if you expose any variables to JS
   VITE_APP_NAME="${APP_NAME}"
   ```

   Access in JS:
   ```javascript
   // Before (Mix)
   process.env.MIX_APP_NAME

   // After (Vite)
   import.meta.env.VITE_APP_NAME
   ```

10. **BrowserSync Replacement**

    Vite has built-in HMR. For proxy setup:
    ```javascript
    // vite.config.js
    export default defineConfig({
        server: {
            hmr: {
                host: 'localhost',
            },
            proxy: {
                // If needed for local development
            },
        },
        // ...
    });
    ```

11. **Handle CodeMirror Assets**

    If CodeMirror loads CSS from node_modules:
    ```javascript
    // resources/js/app.js
    // Before (Mix)
    require('codemirror/lib/codemirror.css');

    // After (Vite)
    import 'codemirror/lib/codemirror.css';
    ```

12. **Testing**
    ```bash
    # Development mode
    npm run dev
    # Visit app, check all assets load

    # Production build
    npm run build
    # Check public/build/ directory
    # Test with production build
    ```

#### Files to Update:

**Critical:**
- `webpack.mix.js` → DELETE (replace with `vite.config.js`)
- `package.json` - Scripts and dependencies
- `resources/views/layouts/app.blade.php` - Asset loading
- `.gitignore` - Output directories

**Medium Priority:**
- `resources/js/app.js` - Change `require` to `import`
- `resources/js/bootstrap.js` - Module syntax
- `resources/css/app.scss` - Should work as-is
- Any Blade templates using `mix()` helper

**Configuration:**
- `postcss.config.js` - Should work as-is
- `tailwind.config.js` - Should work as-is

#### Potential Issues:

1. **Vendor Extraction:**
   - Mix: Used `.extract()` to split vendor bundles
   - Vite: Automatically code-splits, may need manual config

   ```javascript
   // vite.config.js - if needed
   export default defineConfig({
       build: {
           rollupOptions: {
               output: {
                   manualChunks: {
                       vendor: ['axios', 'lodash'],
                       codemirror: ['codemirror'],
                   },
               },
           },
       },
   });
   ```

2. **Public Assets:**
   - Move images to `resources/` and import them
   - Or use Vite's `public/` directory (not bundled)

3. **Module Resolution:**
   - Vite requires explicit file extensions for local imports
   - May need to add `.js` to import statements

#### Testing Checklist:
- [ ] CSS loads correctly (Tailwind, custom styles)
- [ ] JavaScript executes (CodeMirror, Flatpickr, Axios)
- [ ] CodeMirror syntax highlighting works
- [ ] Flatpickr date picker works
- [ ] Nord theme colors applied
- [ ] Custom fonts load (Comfortaa, Fira Mono)
- [ ] Production build optimizes assets
- [ ] Hot module replacement works in dev mode
- [ ] All API calls function (Axios)
- [ ] Form submissions work (CSRF token)

---

## Optional: CodeMirror 6 Upgrade

**Status:** OPTIONAL - Not required
**Risk Level:** HIGH
**Estimated Time:** 20-30 hours

CodeMirror 6 is a complete rewrite with better performance and extensibility, but requires significant code changes.

### Why Consider?
- Better performance
- Better mobile support
- More modern API
- Tree-shaking support (smaller bundles)

### Why Skip (For Now)?
- CodeMirror 5 is still maintained
- Complete API rewrite required
- Language mode configuration entirely different
- Not critical for functionality

### If Pursuing Later:
1. CodeMirror 6 uses a modular system
2. Each language mode is a separate package
3. Configuration is different from CM5
4. Will need to rewrite `resources/js/codemirror-langs.js` entirely

**Recommendation:** Defer until other upgrades complete and stable.

---

## Recommended Upgrade Sequence

### Option A: Conservative (Lowest Risk)
1. **Phase 1** → Test → Deploy
2. **Phase 2** → Test → Deploy
3. **Phase 4** → Test → Deploy (Vite migration)
4. **Phase 3** → Test → Deploy (Laravel 11)

**Pros:** Each change isolated, easier to debug
**Cons:** Multiple deployments, longer timeline
**Timeline:** 6-8 weeks

### Option B: Moderate (Balanced)
1. **Phase 1 + Phase 2** → Test → Deploy
2. **Phase 3** → Test → Deploy
3. **Phase 4** → Test → Deploy

**Pros:** Fewer deployments, good risk balance
**Cons:** Slightly harder to debug if issues
**Timeline:** 4-6 weeks

### Option C: Aggressive (Fastest)
1. **Phase 1 + Phase 2 + Phase 4** → Test → Deploy
2. **Phase 3** → Test → Deploy

**Pros:** Faster completion, modernizes build stack first
**Cons:** More complex debugging
**Timeline:** 3-4 weeks

### Option D: All-At-Once (Highest Risk)
1. **All Phases** → Test → Deploy

**Pros:** Single deployment, fastest
**Cons:** Hardest to debug, highest rollback risk
**Timeline:** 2-3 weeks
**Not Recommended** for production applications

---

## Testing Strategy

### Automated Testing
```bash
# PHP Unit Tests
./vendor/bin/phpunit

# If adding JS tests (recommended):
npm run test
```

### Manual Testing Checklist

#### Core Features:
- [ ] Create pasta (plaintext)
- [ ] Create pasta (encrypted with password)
- [ ] View pasta (plaintext)
- [ ] View pasta (encrypted, correct password)
- [ ] View pasta (encrypted, wrong password) - should fail gracefully
- [ ] Burn on read - creator can view once
- [ ] Burn on read - second viewer triggers deletion
- [ ] Expiration - create with 1 minute expiry, verify deletion
- [ ] Diff view - create parent/child pastas
- [ ] Syntax highlighting - test multiple languages

#### API Endpoints:
- [ ] GET `/api/p/{uuid}` - retrieve pasta
- [ ] GET `/api/p/{uuid}/{key}` - retrieve encrypted pasta
- [ ] POST `/api/p` - create pasta
- [ ] GET `/api/p/diff/{uuid}/{parent}` - get diff

#### Commands:
- [ ] `php artisan pasta:clean` - removes expired pastas

#### UI/UX:
- [ ] Nord theme colors applied correctly
- [ ] CodeMirror editor loads and highlights
- [ ] Flatpickr date picker works
- [ ] Forms submit correctly (CSRF protection)
- [ ] Responsive design works on mobile
- [ ] Custom fonts load (Comfortaa, Fira Mono)

#### Security:
- [ ] CSRF protection active
- [ ] Password encryption/decryption works
- [ ] Salt stored but not password
- [ ] XSS protection (output escaped)
- [ ] Input validation enforced

### Performance Testing
```bash
# Before upgrade - benchmark
ab -n 1000 -c 10 http://pasta.test/

# After upgrade - compare
ab -n 1000 -c 10 http://pasta.test/
```

---

## Rollback Procedures

### For Each Phase:

1. **Git Revert**
   ```bash
   git revert <commit-hash>
   # Or
   git reset --hard <previous-commit>
   ```

2. **Restore Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Rebuild Assets**
   ```bash
   npm run production
   ```

4. **Database Rollback (if migrations run)**
   ```bash
   php artisan migrate:rollback
   ```

5. **Deploy Previous Version**
   - Use deployment tool to redeploy previous release
   - Verify functionality restored

### Emergency Hotfix:
If critical bug found post-deployment:
1. Assess impact (can it wait?)
2. If urgent: rollback entire deployment
3. If not urgent: create hotfix branch, test, deploy patch

---

## Post-Upgrade Tasks

### After Each Phase:

1. **Monitor Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   - Check for deprecation warnings
   - Check for errors
   - Monitor performance

2. **Update Documentation**
   - Update `CLAUDE.md` with new versions
   - Update `README.md` if setup instructions change
   - Update `.env.example` with new variables

3. **Performance Check**
   - Compare before/after metrics
   - Check asset sizes (`public/js/`, `public/css/`, or `public/build/`)
   - Verify page load times

4. **Security Scan**
   ```bash
   # PHP security check
   composer audit

   # JavaScript security check
   npm audit
   ```

### After Full Upgrade:

1. **Update Deployment Scripts**
   - Update `deploy.sh` if it references Mix
   - Update any CI/CD pipelines
   - Update server requirements (PHP version, Node version)

2. **Create Upgrade Notes**
   - Document any custom changes made
   - Note any deviations from standard upgrade path
   - Record any compatibility issues encountered

3. **Schedule Follow-Up Review**
   - 1 week post-deployment: Check logs, monitor errors
   - 1 month post-deployment: Assess stability
   - 3 months post-deployment: Plan next upgrade cycle

---

## Breaking Changes Reference

### Axios 0.25 → 1.7

```javascript
// Breaking: Default validateStatus changed
// Before: Any status code is "success"
// After: Only 2xx is success

// Fix: Explicitly set validateStatus
axios.get('/api/p/123', {
    validateStatus: (status) => status < 500
});

// Breaking: Error response structure
// Before: error.response.data
// After: Same, but ensure error handling catches this
```

### Laravel 9 → 11

```php
// Breaking: Some middleware auto-registered
// Before: Manually register in Kernel
// After: May need to remove duplicates

// Breaking: Service provider consolidation
// Before: Many providers in config/app.php
// After: Fewer providers, more auto-discovery

// Breaking: Rate limiting per-second
// Before: Per-minute only
// After: Can specify per-second (doesn't affect existing code)
```

### PHPUnit 9 → 11

```php
// Breaking: @test annotation removed
// Before:
/** @test */
public function it_creates_pasta() { }

// After:
public function test_it_creates_pasta() { }
// Or use attribute:
#[Test]
public function it_creates_pasta() { }

// Breaking: Some assertions removed
// Check PHPUnit 10/11 changelog for specific methods
```

### Laravel Mix → Vite

```blade
<!-- Breaking: Asset loading in Blade -->
<!-- Before: -->
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
<script src="{{ mix('js/app.js') }}"></script>

<!-- After: -->
@vite(['resources/css/app.scss', 'resources/js/app.js'])
```

```javascript
// Breaking: CommonJS → ES Modules
// Before:
require('./bootstrap');
const axios = require('axios');

// After:
import './bootstrap.js';
import axios from 'axios';

// Breaking: Environment variables
// Before:
process.env.MIX_API_URL

// After:
import.meta.env.VITE_API_URL
```

---

## Additional Resources

### Official Upgrade Guides:
- [Laravel 9 → 10 Upgrade Guide](https://laravel.com/docs/10.x/upgrade)
- [Laravel 10 → 11 Upgrade Guide](https://laravel.com/docs/11.x/upgrade)
- [Vite Migration Guide](https://laravel.com/docs/11.x/vite)
- [PHPUnit 10 Migration](https://docs.phpunit.de/en/10.5/migration.html)
- [Axios Migration Guide](https://axios-http.com/docs/migration_guide)

### Community Resources:
- Laravel News - Upgrade tips and best practices
- Laracasts - Video tutorials on Laravel 11 features
- Stack Overflow - Common upgrade issues

### Tools:
- Laravel Shift - Automated upgrade service (paid)
- Rector - Automated PHP refactoring tool
- PHP CS Fixer - Code style standardization

---

## Cost-Benefit Analysis

### Benefits of Upgrading:

1. **Security**
   - Fixes known vulnerabilities (Axios CVEs)
   - Latest Laravel security patches
   - PHP 8.3 security improvements

2. **Performance**
   - Vite: Faster builds (3-5x in development)
   - PHP 8.3: ~10-15% performance improvement
   - Laravel 11: Optimizations in core

3. **Developer Experience**
   - Vite HMR: Instant feedback during development
   - Modern PHP features: Enums, attributes, etc.
   - Better error messages (Collision 8)

4. **Maintainability**
   - Laravel 11 is LTS (support until 2026)
   - Easier to hire developers (modern stack)
   - Future upgrades easier from current baseline

5. **Features**
   - Laravel 11 improvements (health checks, etc.)
   - PHP 8.3 features (typed class constants, etc.)
   - Modern JavaScript ecosystem

### Costs of Upgrading:

1. **Time Investment**
   - Development: 30-50 hours
   - Testing: 20-30 hours
   - Documentation: 5-10 hours
   - **Total: 55-90 hours**

2. **Risk**
   - Potential bugs introduced
   - Deployment downtime (minimal if planned)
   - Learning curve for new tools

3. **Infrastructure**
   - May need to upgrade PHP on servers
   - May need to update deployment scripts
   - Training for team members

### Costs of NOT Upgrading:

1. **Security Risks**
   - Known vulnerabilities remain
   - No security patches for old versions
   - Potential data breaches

2. **Technical Debt**
   - Harder to upgrade later (larger gaps)
   - Deprecated packages may break
   - Difficulty finding support

3. **Opportunity Cost**
   - Missing modern features
   - Slower development cycles
   - Harder to attract talent

### Recommendation:
**Proceed with upgrade using Option B (Moderate approach)**

The benefits significantly outweigh the costs, especially given the security vulnerabilities in Axios and the end of support for older Laravel versions.

---

## Appendix: Version Compatibility Matrix

| Laravel | PHP | PHPUnit | Sanctum | Vite Plugin |
|---------|-----|---------|---------|-------------|
| 9.x | 8.0-8.2 | 9.x | 2.x-3.x | N/A (Mix) |
| 10.x | 8.1-8.3 | 10.x | 3.x | 0.7-1.x |
| 11.x | 8.2-8.4 | 10.x-11.x | 4.x | 1.x |

| Node.js | npm | Vite | Tailwind |
|---------|-----|------|----------|
| 16.x | 8.x | 4.x | 3.x |
| 18.x | 9.x | 5.x | 3.x |
| 20.x | 10.x | 5.x | 3.x |

---

**End of Upgrade Plan**
