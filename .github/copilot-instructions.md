# School Management System - AI Agent Instructions

## Project Architecture

This is a modern, PHP 8+ School ERP (42 modular modules) built on a LAMP stack and designed for maintainability, security and extensibility. Key traits:
- Backend: PHP 8+ using PDO (no ORM) with a singleton db() helper for all DB access.
- Modular design: 42 self-contained modules (academics, finance, library, transport, hostel, HR, etc.) with clear interfaces so modules can be added/disabled without breaking core.
- Theming: Dual UI themes (Cyberpunk / Nature) implemented as CSS theme bundles + lightweight view helpers (no frontend frameworks).
- Integrations: LTI 1.3, REST endpoints for external systems, PWA-ready (service worker + manifest).
- Config & secrets: .env-driven config (getenv()), strict secret handling, and semantic versioning for releases.
- Security & ops: Secure defaults (prepared statements, XSS escaping, CSRF tokens, file‑upload validation), uses unix socket for MySQL (/opt/lampp/var/mysql/mysql.sock), and includes PHPUnit hooks for testing.
- Conventions: PSR-like coding style, single-entry auth boilerplate, and simple asset pipeline — intentionally framework-light to keep deployment simple on Apache/LAMPP.

### Core Technology Stack

- **Backend**: PHP 8.0+ with PDO (no ORM)
- **Database**: MySQL 8.0 (`attendance_system` database, socket at `/opt/lampp/var/mysql/mysql.sock`)
- **Frontend**: Vanilla JS + Cyberpunk/Nature CSS themes (no framework)
- **Server**: Apache 2.4 via LAMPP at `/opt/lampp/htdocs/attendance`
- **Auth**: Session-based with role-based access control (18 roles)

### Critical File Patterns

**Database Connection** - Singleton pattern with unix socket support:

```php
// Always use db() helper, never direct PDO
db()->fetchAll("SELECT * FROM users WHERE id = ?", [$user_id]);
db()->insert('table_name', ['column' => 'value']);
```

**Configuration** - Environment variables via getenv():

```php
// includes/config.php loads from .env (NEVER hardcode credentials!)
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
```

**Navigation** - Role-specific sidebars in `includes/*-nav.php`:

- `cyber-nav.php` - Cyberpunk theme (admin, teacher, etc.)
- `nature-nav.php` - Nature theme variant
- `student-nav.php` - Student-specific navigation
- `general-nav.php` - General users

Each nav file MUST include hamburger menu button + toggle script for mobile responsiveness.

**Authentication** - Every page requires:

```php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';
require_admin('../login.php'); // or require_teacher(), require_student()
```

## Development Workflows

### Local Development

```bash
# Start LAMPP
sudo /opt/lampp/lampp start

# Access app
http://localhost/attendance

# Database CLI
mysql -u root -h localhost --socket=/opt/lampp/var/mysql/mysql.sock
```

### Database Migrations

```bash
# Apply schema changes
mysql -u root -p attendance_system < database/your_migration.sql

# Verify
mysql -u root -p attendance_system -e "SHOW TABLES;"
```

### Git Workflow (IMPORTANT - Secrets Management)

```bash
# Before ANY commit, verify no secrets leaked
grep -r "AC063981" . --exclude-dir=.git --exclude-dir=vendor
grep -r "@gmail.com" . --exclude-dir=.git --exclude-dir=vendor

# Credentials MUST use getenv() or be in .env (git-ignored)
# See docs/GITHUB_PUSH_GUIDE.md for push protection bypass procedures
```

## Project-Specific Conventions

### File Naming

- **Pages**: `kebab-case.php` (e.g., `class-enrollment.php`)
- **Includes**: `lowercase.php` (e.g., `database.php`)
- **CSS**: `kebab-case.css` (e.g., `cyberpunk-ui.css`)
- **Role dirs**: `lowercase/` (e.g., `admin/`, `teacher/`)

### CSS Architecture - Dual Theming System

1. **cyberpunk-ui.css** - Neon, dark, holographic effects
2. **nature-theme.css** - Green, organic, earth tones
3. **admin-style.css** - Classic professional theme

**Critical**: All themes MUST have:

```css
html {
  overflow-y: scroll;
  overflow-x: hidden;
}
body {
  overflow-y: auto;
  overflow-x: hidden;
}
.hamburger-btn {
  display: none;
} /* Show on mobile via @media(max-width:1024px) */
```

### Database Schema Patterns

- **No migrations framework** - Direct SQL in `database/` folder
- **50+ tables** across modules (`verdant-sms-schema.sql` is master schema)
- **Naming**: `snake_case` tables, `camelCase` or `snake_case` columns
- **Foreign keys**: Explicit relationships with `ON DELETE CASCADE/SET NULL`

### Security Patterns

```php
// XSS Prevention - ALWAYS escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// SQL Injection - ALWAYS use prepared statements
db()->fetchAll("SELECT * FROM users WHERE email = ?", [$email]);

// CSRF - Implement tokens for state-changing operations
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// File Uploads - Validate type, size, rename
move_uploaded_file($temp, 'uploads/' . uniqid() . '_' . basename($file));
```

## Role-Based Architecture

### 18 User Roles (each has dedicated dashboard)

- **admin** - Full system access
- **teacher** - Class management, grading
- **student** - View grades, attendance
- **parent** - Child monitoring
- **principal**, **vice-principal** - School oversight
- **librarian**, **transport**, **hostel**, **canteen**, **nurse**, **counselor** - Module-specific
- **accountant**, **admin-officer**, **class-teacher**, **subject-coordinator** - Support roles
- **owner**, **superadmin** - Multi-school management
- **alumni** - Alumni portal

**Navigation**: Each role has tailored menu in `includes/cyber-nav.php` (lines 34-220)

## Integration Points

### LMS Integration (LTI 1.3)

- **Config**: `includes/lti.php` - JWT validation, session handling
- **Tables**: `lti_configurations`, `lti_sessions`, `lti_user_mappings`
- **Endpoints**: `api/lti.php` (launch, deep-link, grade-passback)
- **Docs**: `docs/LMS_INTEGRATION_GUIDE.md` (800+ lines)

### PWA (Progressive Web App)

- **Manifest**: `manifest.json` with icons
- **Service Worker**: `sw.js` (offline caching)
- **Scripts**: `assets/js/pwa-manager.js`, `pwa-analytics.js`
- **Docs**: `docs/PWA_IMPLEMENTATION_COMPLETE.md`

### Email/SMS

- **SMTP**: PHPMailer via `includes/functions.php::send_email()`
- **Twilio**: WhatsApp integration (configured via .env)

## Common Tasks

### Adding a New Page

1. Create file in role directory (e.g., `admin/new-feature.php`)
2. Include auth + database boilerplate
3. Add to navigation in `includes/cyber-nav.php`
4. Use `cyber-bg` body class for cyberpunk theme
5. Wrap content in `.cyber-layout > .cyber-main` structure

### Fixing Scrolling Issues

**Root cause**: `overflow: hidden` on layout containers blocks scrolling.
**Solution**: Ensure `html` and `body` have `overflow-y: scroll/auto`, NOT `overflow: hidden`.

### Adding Hamburger Menu to New Nav File

```html
<!-- Before sidebar -->
<button class="hamburger-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
  <i class="fas fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Add id to sidebar -->
<aside class="cyber-sidebar" id="cyberSidebar">
  <!-- After sidebar close tag -->
  <script>
    (function () {
      const sidebar = document.getElementById("cyberSidebar");
      const toggle = document.getElementById("sidebarToggle");
      const overlay = document.getElementById("sidebarOverlay");

      toggle?.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
      });

      overlay?.addEventListener("click", () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
      });
    })();
  </script>
</aside>
```

## Documentation Map

- **README.md** - Project overview, quick start
- **docs/SETUP_GUIDE.md** - Installation, config (700+ lines)
- **docs/LMS_INTEGRATION_GUIDE.md** - LTI 1.3 setup (800+ lines)
- **docs/ENVIRONMENT_SETUP.md** - Credentials, .env config
- **docs/GITHUB_PUSH_GUIDE.md** - Secret scanning bypass procedures
- **PROJECT_STATUS.md** - Current state, git stats

## Critical "Gotchas"

1. **DB Socket Path**: Use `/opt/lampp/var/mysql/mysql.sock`, NOT `localhost:3306`
2. **Secrets in Git**: GitHub blocks pushes with hardcoded Twilio/SMTP credentials - use .env!
3. **CSS Overflow**: Pages won't scroll if `body` has `overflow: hidden` - always use `overflow-y: auto`
4. **Navigation**: Changes to nav menu require editing `includes/*-nav.php` role sections (arrays at top)
5. **Responsive**: All CSS files need `@media(max-width: 1024px)` breakpoints + hamburger menu styles
6. **Database Name**: Code uses `attendance_system` but some docs say `attendance_db` - check `includes/config.php`

## When Debugging

1. **DB Errors**: Check `/opt/lampp/logs/mysql_error.log`
2. **PHP Errors**: Enable in `includes/config.php`: `error_reporting(E_ALL);`
3. **LTI Issues**: Check `lti_sessions` table, enable debug in `includes/lti.php`
4. **Missing Pages**: Verify role access in `includes/functions.php` auth helpers
5. **CSS Not Loading**: Check `<link>` paths are relative to document root (`../assets/css/`)

## Build/Test Commands

```bash
# No build step - raw PHP served by Apache

# Manual testing (no automated tests configured)
# Test DB connection:
php -r "require 'includes/database.php'; var_dump(db());"

# Test admin login:
curl -X POST http://localhost/attendance/login.php -d "email=admin@school.edu&password=Admin@123"

# Check PHPUnit (if tests exist):
./vendor/bin/phpunit tests/
```

---

**Last Updated**: Nov 25, 2025 | **Project Version**: 3.0.0 | **Status**: Production-Ready
