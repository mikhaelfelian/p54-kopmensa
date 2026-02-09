# Index.php and Application Entry Point Analysis

## Overview
This document provides a comprehensive analysis of the application entry point (`index.php`), routing structure, security configuration, and bootstrap process for the Kopmensa application.

---

## 1. Root Index.php Analysis

### 1.1 File Location and Purpose
**File:** `index.php` (root directory)

**Purpose:** Front controller that intercepts all requests and routes them to `public/index.php` while handling static file serving and security headers.

### 1.2 Code Structure

```php
<?php
date_default_timezone_set('Asia/Jakarta');
/**
 * Front Controller
 * Redirect all requests to public/index.php
 */

// Security headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Check if request is for a static file
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicPath = __DIR__ . '/public' . $uri;

// Let CodeIgniter handle the environment
if (file_exists(__DIR__ . '/app/Config/Boot/development.php') || 
    file_exists(__DIR__ . '/app/Config/Boot/testing.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if (file_exists($publicPath) && is_file($publicPath)) {
    // Serve static files directly
    return false;
} else {
    // Forward to public/index.php
    require_once __DIR__ . '/public/index.php';
}
```

### 1.3 Key Features

**1. Timezone Configuration**
- Sets default timezone to `Asia/Jakarta`
- Ensures consistent datetime handling across the application

**2. Security Headers**
- `X-Frame-Options: SAMEORIGIN` - Prevents clickjacking attacks
- `X-XSS-Protection: 1; mode=block` - Enables XSS filtering in browsers
- `X-Content-Type-Options: nosniff` - Prevents MIME type sniffing

**3. Static File Handling**
- Checks if requested file exists in `/public` directory
- Serves static files directly (images, CSS, JS) without routing through CodeIgniter
- Improves performance by bypassing framework for static assets

**4. Environment Detection**
- Checks for development or testing bootstrap files
- Loads Composer autoloader if in development/testing mode
- Allows early autoloading for debugging tools

**5. Request Forwarding**
- All non-static requests forwarded to `public/index.php`
- Maintains clean URL structure

---

## 2. Public Index.php Analysis

### 2.1 File Location
**File:** `public/index.php`

### 2.2 Code Structure

```php
<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 * CHECK PHP VERSION
 */
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

/*
 * SET THE CURRENT DIRECTORY
 */
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 * BOOTSTRAP THE APPLICATION
 */
require FCPATH . '../app/Config/Paths.php';
$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';
exit(Boot::bootWeb($paths));
```

### 2.3 Key Features

**1. PHP Version Check**
- Requires PHP 8.1 or higher
- Returns 503 error if version is insufficient
- Prevents runtime errors from unsupported PHP features

**2. Path Constants**
- Defines `FCPATH` (Front Controller Path)
- Ensures current working directory matches front controller location
- Critical for relative path resolution

**3. Bootstrap Process**
- Loads `Paths.php` configuration
- Loads CodeIgniter `Boot.php` class
- Executes `Boot::bootWeb()` to initialize the application

---

## 3. Bootstrap Process Flow

### 3.1 Boot Sequence

```
1. Root index.php
   ├── Set timezone
   ├── Set security headers
   ├── Check for static files
   └── Forward to public/index.php

2. public/index.php
   ├── Check PHP version
   ├── Define FCPATH
   ├── Load Paths.php
   └── Execute Boot::bootWeb()

3. Boot::bootWeb()
   ├── Define path constants
   ├── Load constants
   ├── Check missing extensions
   ├── Load .env file
   ├── Define environment
   ├── Load environment bootstrap
   ├── Load common functions
   ├── Load autoloader
   ├── Set exception handler
   ├── Initialize Kint (debugging)
   ├── Load config cache (if enabled)
   ├── Autoload helpers
   ├── Initialize CodeIgniter
   └── Run CodeIgniter application
```

### 3.2 Environment Bootstrap Files

**Development (`app/Config/Boot/development.php`):**
```php
error_reporting(E_ALL);
ini_set('display_errors', '1');
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);
defined('CI_DEBUG') || define('CI_DEBUG', true);
```

**Production (`app/Config/Boot/production.php`):**
```php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');
defined('CI_DEBUG') || define('CI_DEBUG', false);
```

**Key Differences:**
- Development: Shows all errors, enables debug backtrace, enables CI_DEBUG
- Production: Hides errors, disables debug mode, suppresses deprecated warnings

---

## 4. Paths Configuration

### 4.1 File Location
**File:** `app/Config/Paths.php`

### 4.2 Configuration

```php
public string $systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';
public string $appDirectory = __DIR__ . '/..';
public string $writableDirectory = __DIR__ . '/../../writable';
public string $testsDirectory = __DIR__ . '/../../tests';
public string $viewDirectory = __DIR__ . '/../Views';
```

**Directory Structure:**
```
project-root/
├── app/                    # Application code
├── public/                 # Public assets and index.php
├── vendor/                 # Composer dependencies
│   └── codeigniter4/
│       └── framework/
│           └── system/      # Framework core
├── writable/               # Writable files (logs, cache, sessions)
└── tests/                  # Test files
```

---

## 5. Routing Configuration

### 5.1 Routes File
**File:** `app/Config/Routes.php`

### 5.2 Route Structure

**API Routes:**
- `/api/anggota/*` - Member API endpoints
- `/api/pos/*` - POS API endpoints (protected by JWT)

**Web Routes:**
- Defined in `Routes.php` with controller namespaces
- Uses CodeIgniter routing system

### 5.3 Route Flow

```
Request → public/index.php → Boot::bootWeb() → 
CodeIgniter Router → Route Matching → Controller → Response
```

---

## 6. Security Configuration

### 6.1 Security Headers (Multiple Layers)

**1. Root index.php:**
```php
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
```

**2. Root .htaccess:**
```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Frame-Options "SAMEORIGIN"
</IfModule>
```

**3. CodeIgniter SecureHeaders Filter:**
- Configured in `app/Config/Filters.php`
- Can be applied globally or per-route

### 6.2 CSRF Protection

**Configuration (`app/Config/Security.php`):**
```php
public string $csrfProtection = 'session';
public bool $tokenRandomize = false;
public string $tokenName = 'csrf_test_name';
public string $headerName = 'X-CSRF-TOKEN';
public string $cookieName = 'csrf_cookie_name';
public int $expires = 7200; // 2 hours
public bool $regenerate = true;
public bool $redirect = (ENVIRONMENT === 'production');
```

**CSRF Exceptions (`app/Config/Filters.php`):**
- `api/*` - All API routes excluded
- Various transaction endpoints excluded
- Bulk delete endpoints excluded

### 6.3 Session Security

**Root .htaccess:**
```apache
<IfModule mod_php.c>
    php_value session.cookie_httponly 1
    php_value session.use_only_cookies 1
    php_value session.cookie_samesite "Lax"
</IfModule>
```

**Security Features:**
- `HttpOnly` - Prevents JavaScript access to cookies
- `SameSite=Lax` - CSRF protection
- Cookies only (no URL-based sessions)

---

## 7. URL Rewriting (.htaccess)

### 7.1 Root .htaccess

**File:** `htaccess` (root directory)

**Key Rules:**
1. **Static File Serving:**
   ```apache
   RewriteCond %{REQUEST_FILENAME} -f [OR]
   RewriteCond %{REQUEST_FILENAME} -d
   RewriteRule ^ - [L]
   ```

2. **Public Asset Routing:**
   ```apache
   RewriteRule ^file/(.*)$ public/file/$1 [L]
   RewriteRule ^assets/(.*)$ public/assets/$1 [L]
   ```

3. **Front Controller Routing:**
   ```apache
   RewriteRule ^(.*)$ public/index.php [QSA,L]
   ```

**Issues Found:**
- Duplicate rewrite rules (lines 16 and 24)
- Both rules route to different targets (conflicting)

### 7.2 Public .htaccess

**File:** `public/.htaccess`

**Key Rules:**
1. **Trailing Slash Redirect:**
   ```apache
   RewriteCond %{REQUEST_URI} (.+)/$
   RewriteRule ^ %1 [L,R=301]
   ```

2. **WWW Removal:**
   ```apache
   RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
   RewriteRule ^ http://%1%{REQUEST_URI} [R=301,L]
   ```

3. **Front Controller:**
   ```apache
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^([\s\S]*)$ index.php/$1 [L,NC,QSA]
   ```

4. **Authorization Header:**
   ```apache
   RewriteCond %{HTTP:Authorization} .
   RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
   ```

---

## 8. Filter Configuration

### 8.1 Global Filters

**File:** `app/Config/Filters.php`

**Before Filters:**
- `forcehttps` - Force HTTPS (if configured)
- `pagecache` - Web page caching
- `csrf` - CSRF protection (with exceptions)

**After Filters:**
- `pagecache` - Web page caching
- `performance` - Performance metrics

### 8.2 Custom Filters

**Available Filters:**
- `auth` - Authentication filter (`App\Filters\AuthFilter`)
- `disableSession` - Disable session (`App\Filters\DisableSessionFilter`)
- `jwtauth` - JWT authentication (`App\Filters\JWTAuthFilter`)
- `shift` - Shift validation (`App\Filters\ShiftFilter`)

---

## 9. Application Configuration

### 9.1 Base URL

**File:** `app/Config/App.php`

```php
public string $baseURL = 'http://localhost/p54-kopmensa/';
public string $indexPage = ''; // Clean URLs
public string $uriProtocol = 'REQUEST_URI';
public string $permittedURIChars = 'a-z 0-9~%.:_\-';
public string $defaultLocale = 'en';
```

**Key Settings:**
- `indexPage = ''` - Enables clean URLs (no index.php in URL)
- `uriProtocol = 'REQUEST_URI'` - Uses standard REQUEST_URI
- Permitted URI characters restricted for security

---

## 10. Issues and Recommendations

### 10.1 Critical Issues

**1. Duplicate Rewrite Rules in Root .htaccess**
- **Issue:** Lines 16 and 24 both have rewrite rules that conflict
- **Impact:** May cause routing issues
- **Fix:** Remove duplicate rule on line 24

**2. Security Headers Duplication**
- **Issue:** Security headers set in both `index.php` and `.htaccess`
- **Impact:** Redundant but not harmful
- **Recommendation:** Choose one location (prefer `.htaccess` for server-level enforcement)

**3. Missing HTTPS Enforcement**
- **Issue:** `forcehttps` filter enabled but may not be configured
- **Impact:** Application may not enforce HTTPS in production
- **Recommendation:** Verify HTTPS configuration in production

### 10.2 Performance Issues

**1. Static File Routing**
- **Current:** Root `index.php` checks for static files before forwarding
- **Issue:** PHP execution overhead for every request
- **Recommendation:** Let web server handle static files directly (better performance)

**2. Composer Autoloader**
- **Current:** Loaded conditionally in root `index.php`
- **Issue:** May not be loaded when needed
- **Recommendation:** Always load in `public/index.php` via Boot process

### 10.3 Security Recommendations

**1. Content Security Policy (CSP)**
- **Current:** CSP configured but may not be enforced
- **Recommendation:** Enable CSP headers with appropriate policies

**2. HSTS Header**
- **Current:** Not configured
- **Recommendation:** Add `Strict-Transport-Security` header for HTTPS sites

**3. X-XSS-Protection Deprecation**
- **Current:** Still using deprecated `X-XSS-Protection` header
- **Recommendation:** Rely on Content-Security-Policy instead

**4. CSRF Token Randomization**
- **Current:** `tokenRandomize = false`
- **Recommendation:** Enable token randomization for enhanced security

### 10.4 Code Quality Issues

**1. Root index.php Logic**
- **Issue:** Environment detection happens before autoloader
- **Impact:** May cause issues if environment files reference classes
- **Recommendation:** Move environment detection after autoloader loading

**2. Error Handling**
- **Issue:** No error handling in root `index.php`
- **Impact:** Fatal errors may expose sensitive information
- **Recommendation:** Add try-catch blocks and error logging

---

## 11. Request Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    HTTP Request                             │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              Root .htaccess (htaccess)                      │
│  - Check if file/directory exists                          │
│  - Route /file/* and /assets/* to public/                 │
│  - Route all else to public/index.php                      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              Root index.php                                │
│  - Set timezone (Asia/Jakarta)                            │
│  - Set security headers                                    │
│  - Check for static files in public/                      │
│  - Load autoloader (if dev/test)                          │
│  - Forward to public/index.php                            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│         public/.htaccess                                    │
│  - Redirect trailing slashes                               │
│  - Remove www prefix                                       │
│  - Route to index.php                                      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│         public/index.php                                    │
│  - Check PHP version (>= 8.1)                             │
│  - Define FCPATH                                           │
│  - Load Paths.php                                          │
│  - Load Boot.php                                           │
│  - Execute Boot::bootWeb()                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│         Boot::bootWeb()                                     │
│  - Define path constants                                   │
│  - Load .env file                                          │
│  - Define environment                                      │
│  - Load environment bootstrap                              │
│  - Load autoloader                                         │
│  - Initialize CodeIgniter                                   │
│  - Run application                                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│         CodeIgniter Router                                  │
│  - Match route                                             │
│  - Apply filters                                           │
│  - Execute controller                                      │
│  - Return response                                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 12. Testing Checklist

### Security Testing
- [ ] Verify security headers are present in responses
- [ ] Test CSRF protection on forms
- [ ] Verify session cookie security settings
- [ ] Test HTTPS enforcement (if configured)
- [ ] Verify XSS protection headers

### Routing Testing
- [ ] Test static file serving (images, CSS, JS)
- [ ] Test clean URLs (no index.php)
- [ ] Test API routes
- [ ] Test trailing slash redirects
- [ ] Test 404 handling

### Performance Testing
- [ ] Verify static files are served efficiently
- [ ] Check autoloader performance
- [ ] Test page caching
- [ ] Verify Gzip compression (if enabled)

### Environment Testing
- [ ] Test development environment detection
- [ ] Test production environment detection
- [ ] Verify error display settings
- [ ] Test debug mode settings

---

## 13. Recommendations Summary

### High Priority
1. **Fix duplicate rewrite rules** in root `.htaccess`
2. **Enable CSRF token randomization** in Security.php
3. **Add error handling** to root `index.php`
4. **Verify HTTPS enforcement** in production

### Medium Priority
5. **Consolidate security headers** (choose one location)
6. **Add HSTS header** for HTTPS sites
7. **Update CSP configuration** and enforce it
8. **Remove deprecated X-XSS-Protection** header

### Low Priority
9. **Optimize static file serving** (let web server handle)
10. **Add request logging** for debugging
11. **Document environment setup** process
12. **Add health check endpoint** for monitoring

---

## Conclusion

The application entry point is well-structured with multiple layers of security and proper routing. However, there are some issues with duplicate rewrite rules and security header configuration that should be addressed. The bootstrap process follows CodeIgniter 4 best practices and properly handles environment detection and error reporting.

The main areas for improvement are:
1. Fixing routing conflicts
2. Enhancing security headers
3. Improving error handling
4. Optimizing performance for static files
