# 🌿 Verdant SMS - System Overview

## Version 3.0 - Evergreen Edition

Last Updated: November 2025

---

## Architecture Overview

Verdant School Management System is a comprehensive, modular PHP 8+ application designed for educational institutions of all sizes.

### Core Principles

1. **Zero Framework Bloat** - Native PHP for maximum performance and minimal hosting requirements
2. **Modular Design** - 42 self-contained modules that can be enabled/disabled independently
3. **Role-Based Access** - 25 distinct user roles with granular permissions
4. **Theme-Centric UI** - 8 built-in themes with live preview and database persistence
5. **Security First** - Prepared statements, XSS escaping, CSRF tokens throughout

---

## 🎨 Theme System

Verdant SMS v3.0 introduces a powerful 8-theme selection system providing personalized visual experiences for all users.

### Available Themes

| Theme             | CSS File             | Primary Color       | Description                                 |
| ----------------- | -------------------- | ------------------- | ------------------------------------------- |
| 🌿 Verdant Nature | `verdant-nature.css` | `#10b981` (Emerald) | Default theme with floating leaf animations |
| 🌃 Dark Cyber     | `dark-cyber.css`     | `#00ffff` (Cyan)    | Neon cyberpunk with grid overlays           |
| 🌊 Ocean Blue     | `ocean-blue.css`     | `#3b82f6` (Blue)    | Professional with wave animations           |
| 🌅 Sunset Warm    | `sunset-warm.css`    | `#f97316` (Orange)  | Warm gradients, sun glow effects            |
| ⬜ Minimal White  | `minimal-white.css`  | `#2563eb` (Blue)    | Clean light theme, no decorations           |
| 💻 Matrix Green   | `matrix-green.css`   | `#00ff00` (Green)   | Terminal/hacker retro style                 |
| 🌌 Purple Galaxy  | `purple-galaxy.css`  | `#a855f7` (Purple)  | Nebula backgrounds, starry effects          |
| ♿ High Contrast  | `high-contrast.css`  | `#ffffff` (White)   | WCAG AAA accessible                         |

### Theme Architecture

```
/assets/css/themes/
├── verdant-nature.css   # Default emerald theme
├── dark-cyber.css       # Cyberpunk neon theme
├── ocean-blue.css       # Professional blue theme
├── sunset-warm.css      # Warm orange theme
├── minimal-white.css    # Light minimal theme
├── matrix-green.css     # Terminal retro theme
├── purple-galaxy.css    # Creative purple theme
└── high-contrast.css    # Accessibility theme

/includes/
├── theme-loader.php     # Theme management functions
└── theme-selector.php   # Modal UI component

/api/
└── save-theme.php       # AJAX theme save endpoint
```

### Theme CSS Variables

Each theme defines the following CSS custom properties:

```css
:root {
  --theme-primary: /* Main accent color */
  --theme-primary-hover: /* Hover state */
  --theme-primary-muted: /* Subdued version */
  --theme-bg-body: /* Page background */
  --theme-bg-card: /* Card backgrounds */
  --theme-bg-sidebar: /* Sidebar background */
  --theme-text-primary: /* Main text */
  --theme-text-secondary: /* Muted text */
  --theme-text-accent: /* Highlighted text */
  --theme-border: /* Border color */
  --theme-glow: /* Glow effect color */
}
```

### Theme Storage

- **Logged-in users**: Theme stored in `users.theme` column (VARCHAR 50)
- **Non-logged-in users**: Theme stored in `verdant_theme` cookie (30 days)
- **System preference**: Respects `prefers-color-scheme` media query

### PHP Functions

```php
// Get current user's theme
$theme = get_user_theme();

// Validate theme name
$valid = validate_theme('dark-cyber'); // true/false

// Get all available themes with metadata
$themes = get_available_themes();

// Update user's theme in database
update_user_theme($user_id, 'ocean-blue');

// Output theme CSS link tag
output_theme_css();

// Get body class for current theme
$class = get_theme_body_class(); // "theme-verdant-nature"
```

---

## 👥 Role System (25 Roles)

### Role Categories

1. **Leadership** (4 roles): SuperAdmin, Owner, Principal, Vice-Principal
2. **Administration** (3 roles): Admin, Admin Officer, Accountant
3. **Academic Staff** (3 roles): Teacher, Class Teacher, Subject Coordinator
4. **Support Services** (3 roles): Librarian, Counselor, Nurse
5. **Facility Management** (4 roles): Transport, Hostel, Canteen, General
6. **Community** (3 roles): Student, Parent, Alumni

### Role Permissions

Each role has access to specific modules and dashboards defined in:

- `includes/functions.php` - Auth helper functions
- `includes/cyber-nav.php` - Navigation menu per role

---

## 📦 Module System

Verdant SMS contains 42+ modules organized by domain:

### Academic Modules

- Attendance (Biometric, QR, Manual)
- Grades & Assessments
- Timetable & Scheduling
- Curriculum Management
- Homework & Assignments

### Administrative Modules

- Student Information System
- Staff Management
- Admissions & Registration
- Document Management
- Communication Center

### Financial Modules

- Fee Collection & Invoicing
- Payroll Management
- Expense Tracking
- Financial Reports

### Facility Modules

- Transport Management
- Hostel Administration
- Library System
- Canteen/POS
- Asset Management

### Support Modules

- Health Records (Nurse)
- Counseling Notes
- Parent Portal
- Alumni Network
- Events & Calendar

---

## 🔐 Security Implementation

### Authentication

- Session-based with role verification
- Password hashing with `password_hash()`
- Email verification option
- Password reset flow

### Authorization

- Role-based access control (RBAC)
- Function-level permission checks
- URL-based access validation

### Data Protection

- PDO prepared statements (SQL injection prevention)
- `htmlspecialchars()` output encoding (XSS prevention)
- CSRF token validation for state-changing operations
- File upload validation (type, size, rename)

---

## 📱 PWA Features

- Installable on mobile devices
- Offline capability via Service Worker
- Push notifications (configurable)
- App manifest with icons

---

## 🔗 Integration Points

### LTI 1.3 (Learning Management Systems)

- Moodle, Canvas, Blackboard integration
- Grade passback
- Deep linking

### REST API

- JSON endpoints at `/api/*`
- Token-based authentication
- Rate limiting

### Email/SMS

- PHPMailer for SMTP
- Twilio for WhatsApp (optional)

---

## 📁 Directory Structure

```
/opt/lampp/htdocs/attendance/
├── admin/              # Admin role pages
├── teacher/            # Teacher role pages
├── student/            # Student role pages
├── parent/             # Parent role pages
├── [other-roles]/      # Role-specific directories
├── api/                # REST API endpoints
├── assets/
│   ├── css/
│   │   ├── themes/     # 8 theme CSS files
│   │   ├── cyberpunk-ui.css
│   │   └── admin-style.css
│   ├── js/
│   └── images/
├── includes/
│   ├── config.php      # Environment configuration
│   ├── database.php    # PDO singleton
│   ├── functions.php   # Auth & utility functions
│   ├── theme-loader.php
│   ├── theme-selector.php
│   └── cyber-nav.php   # Sidebar navigation
├── database/           # SQL schema files
├── docs/               # Documentation
└── vendor/             # Composer dependencies
```

---

## 🚀 Quick Commands

```bash
# Start LAMPP
sudo /opt/lampp/lampp start

# Access MySQL
mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock

# Connect to database
mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock attendance_system

# Check PHP syntax
/opt/lampp/bin/php -l filename.php

# View Apache error log
tail -f /opt/lampp/logs/error_log
```

---

## 📞 Support

- Documentation: `/docs/` directory
- GitHub Issues: Report bugs and feature requests
- Community Forum: `/forum/` module (when enabled)

---

_Verdant SMS v3.0 - Built with 💚 for Education_
