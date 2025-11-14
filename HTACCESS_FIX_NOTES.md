# .htaccess and Routing Fixes

## Issues Fixed

### 1. Internal Server Error (.htaccess)
**Problem:** The .htaccess files contained deprecated Apache 2.2 directives that caused "Internal Server Error" on Apache 2.4+ servers.

**Solution:** 
- Removed the deprecated `Order Allow,Deny` and `Deny from all` directives
- Replaced with Apache 2.4 syntax: `Require all denied`
- Added backward compatibility check using `<IfModule mod_version.c>` for servers that might run older Apache versions
- Wrapped mod_rewrite directives in `<IfModule mod_rewrite.c>` to prevent errors if the module isn't loaded

### 2. 404 Page Not Found (Login and other routes)
**Problem:** The routing system had several configuration issues:
- The `RewriteBase /public/` directive in `public/.htaccess` was too rigid and assumed a specific installation path
- The 404 error page had an incorrect URL (`BASE_URL . '/public/'` instead of `BASE_URL . '/'`)
- The test_connection.php also had the double `/public/` in the URL

**Solution:**
- Removed the `RewriteBase` directive entirely - it's not needed with the current routing setup
- Simplified the rewrite rules to be more flexible
- Fixed URLs in 404.php and test_connection.php to use `BASE_URL . '/'` correctly

## How It Works

### Apache with mod_rewrite (Production)

1. **Root .htaccess** (`/.htaccess`):
   - Redirects all requests to the `/public/` directory
   - Skips redirection if the request is already for `/public/`
   - Allows direct access to files that exist in the root (like test_connection.php)

2. **Public .htaccess** (`/public/.htaccess`):
   - Routes all non-file, non-directory requests to `index.php`
   - Blocks access to sensitive files (.htaccess, .sql, .ini, etc.)
   - Adds security headers (if mod_headers is available)

3. **URL Flow Example:**
   - User visits: `http://example.com/login`
   - Root .htaccess redirects internally to: `/public/login`
   - Public .htaccess routes to: `/public/index.php`
   - Router.php processes the request and calls: `AuthController::login()`

### PHP Built-in Server (Development)

For local development without Apache, use the provided router script:

```bash
cd public
php -S localhost:8080 router.php
```

The `router.php` script simulates .htaccess behavior by:
- Serving real files directly
- Routing all other requests to `index.php`

## Testing

### Test Routing Logic
```bash
php test_routing.php
```

### Test with PHP Server
```bash
cd public
php -S localhost:8080 router.php
```

Then visit:
- http://localhost:8080/test_routes.php - See available routes
- http://localhost:8080/login - Login page
- http://localhost:8080/debug.php - Debug server variables

## Requirements

### Apache
- Apache 2.2+ (2.4+ recommended)
- mod_rewrite enabled
- AllowOverride set to allow .htaccess files

### Enable mod_rewrite (if not already enabled)
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Apache Configuration
Ensure your virtual host or directory configuration allows .htaccess:
```apache
<Directory /var/www/html/comedoresindustriales>
    AllowOverride All
</Directory>
```

## Verification

The routing system is working correctly if:
1. ✅ Accessing `/login` shows the login page (or database error if DB isn't configured)
2. ✅ Accessing `/nonexistent` shows the 404 error page
3. ✅ Accessing `/dashboard` redirects to `/login` (if not authenticated)
4. ✅ No "Internal Server Error" messages

## Changes Made

### Files Modified:
1. `.htaccess` - Simplified root redirect logic
2. `public/.htaccess` - Removed RewriteBase, updated to Apache 2.4 syntax
3. `app/views/errors/404.php` - Fixed URL from `BASE_URL . '/public/'` to `BASE_URL . '/'`
4. `test_connection.php` - Fixed URL from `BASE_URL . '/public/index.php'` to `BASE_URL . '/'`

### Files Added:
1. `public/router.php` - Router for PHP built-in server
2. `public/test_routes.php` - Route testing page
3. `public/debug.php` - Debug page for server variables
4. `test_routing.php` - Command-line routing test script
5. `HTACCESS_FIX_NOTES.md` - This file

## Notes

- The routing logic in `app/Router.php` correctly handles both scenarios (with and without `/public/` in the URL)
- `BASE_URL` is auto-detected based on `$_SERVER['SCRIPT_NAME']` and will correctly include `/public` when accessed via Apache
- The system is designed to work whether accessed from root (`/`) or public (`/public/`) URLs
