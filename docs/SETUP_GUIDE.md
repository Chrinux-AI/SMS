# 🚀 Complete Setup & Installation Guide

## Student Attendance Management System v2.1.0

This guide covers the complete installation process from scratch, including the new LMS integration features.

---

## 📋 Table of Contents

1. [System Requirements](#system-requirements)
2. [Pre-Installation Checklist](#pre-installation-checklist)
3. [Step-by-Step Installation](#step-by-step-installation)
4. [Database Setup](#database-setup)
5. [Configuration](#configuration)
6. [LMS Integration Setup](#lms-integration-setup)
7. [Initial Admin Setup](#initial-admin-setup)
8. [Testing & Verification](#testing--verification)
9. [Production Deployment](#production-deployment)
10. [Troubleshooting](#troubleshooting)

---

## 💻 System Requirements

### Minimum Requirements

| Component            | Requirement                                                |
| -------------------- | ---------------------------------------------------------- |
| **Operating System** | Linux (Ubuntu 20.04+), Windows 10+, macOS 10.15+           |
| **Web Server**       | Apache 2.4+ with mod_rewrite                               |
| **PHP**              | 8.0 or higher                                              |
| **MySQL**            | 5.7 or higher (8.0 recommended)                            |
| **RAM**              | 2GB minimum, 4GB recommended                               |
| **Disk Space**       | 10GB minimum                                               |
| **SSL Certificate**  | Required for LMS integration (Let's Encrypt or commercial) |

### Required PHP Extensions

```bash
# Check installed extensions
php -m

# Required extensions:
✅ pdo_mysql
✅ mysqli
✅ openssl
✅ curl
✅ json
✅ mbstring
✅ xml
✅ gd
✅ zip
```

### Install Missing Extensions (Ubuntu/Debian)

```bash
sudo apt-get update
sudo apt-get install -y php8.0-mysql php8.0-curl php8.0-mbstring \
                        php8.0-xml php8.0-gd php8.0-zip php8.0-openssl
```

---

## ✅ Pre-Installation Checklist

Before beginning, ensure you have:

- [ ] Root or sudo access to server
- [ ] Apache with mod_rewrite enabled
- [ ] MySQL server installed and running
- [ ] PHP 8.0+ installed with required extensions
- [ ] Domain name (for production with LMS integration)
- [ ] SSL certificate (for LMS integration)
- [ ] Administrator access to your LMS (if using LMS features)

---

## 🛠️ Step-by-Step Installation

### Step 1: Install LAMPP Stack (Linux)

#### Option A: Using XAMPP/LAMPP

```bash
# Download XAMPP for Linux
cd /tmp
wget https://www.apachefriends.org/xampp-files/8.0.30/xampp-linux-x64-8.0.30-0-installer.run

# Make executable
chmod +x xampp-linux-x64-8.0.30-0-installer.run

# Run installer
sudo ./xampp-linux-x64-8.0.30-0-installer.run

# Start services
sudo /opt/lampp/lampp start
```

#### Option B: Manual Installation (Ubuntu/Debian)

```bash
# Update package list
sudo apt-get update

# Install Apache
sudo apt-get install -y apache2

# Install MySQL
sudo apt-get install -y mysql-server

# Install PHP and extensions
sudo apt-get install -y php8.0 php8.0-mysql php8.0-curl php8.0-mbstring \
                        php8.0-xml php8.0-gd php8.0-zip libapache2-mod-php8.0

# Enable Apache modules
sudo a2enmod rewrite
sudo a2enmod ssl

# Start services
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql
```

### Step 2: Clone/Download SAMS

#### Option A: Clone from Repository (if available)

```bash
cd /opt/lampp/htdocs
sudo git clone https://github.com/your-org/attendance.git
sudo chown -R daemon:daemon attendance
```

#### Option B: Extract from Archive

```bash
cd /opt/lampp/htdocs
sudo unzip attendance-v2.1.0.zip
sudo mv attendance-v2.1.0 attendance
sudo chown -R daemon:daemon attendance
```

### Step 3: Set Permissions

```bash
# Set directory permissions
sudo find /opt/lampp/htdocs/attendance -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /opt/lampp/htdocs/attendance -type f -exec chmod 644 {} \;

# Make specific directories writable
sudo chmod -R 777 /opt/lampp/htdocs/attendance/cache
sudo chmod -R 777 /opt/lampp/htdocs/attendance/uploads
sudo chmod -R 777 /opt/lampp/htdocs/attendance/assets/qrcodes

# Set ownership (daemon for LAMPP, www-data for standard Apache)
sudo chown -R daemon:daemon /opt/lampp/htdocs/attendance
# OR for standard Apache:
# sudo chown -R www-data:www-data /var/www/html/attendance
```

---

## 🗄️ Database Setup

### Step 1: Create Database

```bash
# Access MySQL
mysql -u root -p

# Or for LAMPP:
/opt/lampp/bin/mysql -u root -p
```

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS attendance_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with secure password
CREATE USER IF NOT EXISTS 'attendance_user'@'localhost'
IDENTIFIED BY 'your_secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON attendance_db.* TO 'attendance_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Verify
SHOW DATABASES;
USE attendance_db;

-- Exit MySQL
EXIT;
```

### Step 2: Import Main Schema

```bash
cd /opt/lampp/htdocs/attendance

# Import main database schema
mysql -u attendance_user -p attendance_db < database/schema.sql

# Verify tables were created
mysql -u attendance_user -p attendance_db -e "SHOW TABLES;"
```

Expected tables:

```
attendance
biometric_devices
biometric_logs
chat_contacts
chat_messages
classes
email_verifications
forum_categories
forum_posts
forum_replies
messages
message_recipients
notifications
parents
students
teachers
users
```

### Step 3: Import LTI Schema (for LMS Integration)

```bash
# Import LTI/LMS integration schema
mysql -u attendance_user -p attendance_db < database/lti_schema.sql

# Verify LTI tables were created
mysql -u attendance_user -p attendance_db -e "SHOW TABLES LIKE 'lti_%';"
```

Expected LTI tables:

```
lti_configurations
lti_context_mappings
lti_grade_sync_log
lti_nonce_store
lti_resource_links
lti_sessions
lti_user_mappings
```

### Step 4: Verify Database Structure

```sql
-- Check users table structure
DESCRIBE users;

-- Check LTI configurations table
DESCRIBE lti_configurations;

-- Count existing records
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as lti_configs FROM lti_configurations;
```

---

## ⚙️ Configuration

### Step 1: Database Configuration

Edit `/includes/config.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'attendance_db');
define('DB_USER', 'attendance_user');
define('DB_PASS', 'your_secure_password_here');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Student Attendance Management System');
define('APP_VERSION', '2.1.0');
define('APP_URL', 'https://yourdomain.com/attendance'); // Change for production

// Timezone
date_default_timezone_set('UTC'); // Change to your timezone

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production

// Session Settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Requires HTTPS
ini_set('session.use_strict_mode', 1);

// File Upload Settings
define('MAX_UPLOAD_SIZE', 10485760); // 10MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Email Settings (configure later)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'SAMS Notification');

// LTI Settings
define('LTI_ENABLED', true);
define('LTI_DEBUG_MODE', false); // Set true for debugging
define('LTI_LOG_FILE', '/var/log/sams_lti.log');
```

### Step 2: Create Default Admin Account

```bash
# Run admin creation script
php /opt/lampp/htdocs/attendance/create-default-admin.php
```

Or manually via SQL:

```sql
-- Insert default admin (password: Admin@123)
INSERT INTO users (
    username,
    email,
    password,
    full_name,
    role,
    approved,
    email_verified,
    created_at
) VALUES (
    'admin',
    'admin@sams.edu',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Admin@123
    'System Administrator',
    'admin',
    1,
    1,
    NOW()
);
```

### Step 3: Apache Virtual Host Configuration

Create `/etc/apache2/sites-available/attendance.conf`:

```apache
<VirtualHost *:80>
    ServerName attendance.yourdomain.com
    ServerAdmin webmaster@yourdomain.com

    DocumentRoot /opt/lampp/htdocs/attendance

    <Directory /opt/lampp/htdocs/attendance>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/attendance-error.log
    CustomLog ${APACHE_LOG_DIR}/attendance-access.log combined

    # Redirect to HTTPS (after SSL is configured)
    # RewriteEngine On
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite attendance.conf
sudo systemctl reload apache2
```

---

## 🎓 LMS Integration Setup

### Step 1: Generate RSA Keys for LTI

```bash
# Create keys directory
sudo mkdir -p /opt/lampp/htdocs/attendance/config/keys
cd /opt/lampp/htdocs/attendance/config/keys

# Generate private key (2048-bit RSA)
sudo openssl genrsa -out lti_private_key.pem 2048

# Extract public key
sudo openssl rsa -in lti_private_key.pem -pubout -out lti_public_key.pem

# Set secure permissions
sudo chmod 600 lti_private_key.pem
sudo chmod 644 lti_public_key.pem
sudo chown daemon:daemon lti_*.pem

# Display keys (you'll need these for configuration)
echo "=== PUBLIC KEY (for LMS) ==="
cat lti_public_key.pem

echo ""
echo "=== PRIVATE KEY (for SAMS database) ==="
cat lti_private_key.pem
```

**⚠️ SECURITY WARNING**:

- Never commit private keys to version control
- Store private keys outside the web root if possible
- Use different keys for different environments (dev/staging/prod)

### Step 2: Configure SSL/HTTPS (Required for LTI 1.3)

#### Option A: Let's Encrypt (Free, Recommended for Production)

```bash
# Install Certbot
sudo apt-get install -y certbot python3-certbot-apache

# Obtain certificate
sudo certbot --apache -d attendance.yourdomain.com

# Certbot will automatically:
# - Obtain SSL certificate
# - Configure Apache
# - Set up auto-renewal

# Test auto-renewal
sudo certbot renew --dry-run
```

#### Option B: Self-Signed (Development Only)

```bash
# Generate self-signed certificate
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/attendance-selfsigned.key \
  -out /etc/ssl/certs/attendance-selfsigned.crt

# Create SSL virtual host
sudo nano /etc/apache2/sites-available/attendance-ssl.conf
```

```apache
<VirtualHost *:443>
    ServerName attendance.yourdomain.com

    DocumentRoot /opt/lampp/htdocs/attendance

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/attendance-selfsigned.crt
    SSLCertificateKeyFile /etc/ssl/private/attendance-selfsigned.key

    <Directory /opt/lampp/htdocs/attendance>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/attendance-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/attendance-ssl-access.log combined
</VirtualHost>
```

```bash
# Enable SSL module and site
sudo a2enmod ssl
sudo a2ensite attendance-ssl.conf
sudo systemctl reload apache2
```

### Step 3: Configure LMS Platform (Example: Moodle)

See detailed guide: `/docs/LMS_INTEGRATION_GUIDE.md`

**Quick Summary**:

1. Login to Moodle as Administrator
2. Go to: Site Administration → Plugins → External tool → Manage tools
3. Click "Configure a tool manually"
4. Enter SAMS details:
   - Tool name: Student School Management System
   - Tool URL: `https://attendance.yourdomain.com/api/lti.php?action=launch`
   - LTI version: LTI 1.3
   - Public key: [Paste contents of lti_public_key.pem]
5. Enable services: AGS, NRPS, Deep Linking
6. Save and copy configuration values
7. Enter Moodle config in SAMS (Admin → LMS Settings)

---

## 👨‍💼 Initial Admin Setup

### Step 1: First Login

1. Open browser to: `https://attendance.yourdomain.com`
2. Login with:
   - Email: `admin@sams.edu`
   - Password: `Admin@123`

### Step 2: Change Default Password

1. Click profile icon → Settings
2. Change password to a strong, unique password
3. Update email if needed

### Step 3: Configure System Settings

1. Navigate to: **Admin → System Settings**
2. Configure:
   - School/Institution name
   - Academic year
   - Attendance grading scale
   - Timezone
   - Date/time formats

### Step 4: Setup Email (Optional but Recommended)

1. **Admin → Email Settings**
2. Configure SMTP:
   - Host: `smtp.gmail.com`
   - Port: `587`
   - Username: Your Gmail address
   - Password: App-specific password (not your Gmail password)
3. Test email by sending test message

**Gmail App Password Setup**:

1. Enable 2FA on your Google account
2. Go to: https://myaccount.google.com/apppasswords
3. Generate app password for "Mail"
4. Use this password in SAMS

### Step 5: Create Sample Data (Optional)

```sql
-- Insert sample teacher
INSERT INTO users (username, email, password, full_name, role, approved, email_verified, created_at)
VALUES ('teacher1', 'teacher@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Teacher', 'teacher', 1, 1, NOW());

SET @teacher_user_id = LAST_INSERT_ID();

INSERT INTO teachers (user_id, department, employee_id, created_at)
VALUES (@teacher_user_id, 'Computer Science', 'EMP001', NOW());

-- Insert sample student
INSERT INTO users (username, email, password, full_name, role, approved, email_verified, created_at)
VALUES ('student1', 'student@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Student', 'student', 1, 1, NOW());

SET @student_user_id = LAST_INSERT_ID();

INSERT INTO students (user_id, student_id, grade_level, created_at)
VALUES (@student_user_id, 'STU2025001', 'Grade 10', NOW());

-- Insert sample class
INSERT INTO classes (class_name, grade_level, section, teacher_id, academic_year, created_at)
VALUES ('Computer Science 101', 'Grade 10', 'A', 1, '2025-2026', NOW());
```

---

## ✅ Testing & Verification

### Basic Functionality Tests

```bash
# Test database connection
php -r "
require '/opt/lampp/htdocs/attendance/includes/config.php';
\$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
echo 'Database connection: SUCCESS' . PHP_EOL;
"

# Check PHP version and extensions
php -v
php -m | grep -E "(pdo_mysql|openssl|curl|mbstring)"
```

### Web Interface Tests

1. **Homepage**: Visit `https://attendance.yourdomain.com`
2. **Login**: Test admin login
3. **Registration**: Create test student account
4. **Email**: Verify email is sent
5. **Admin Approval**: Approve test account
6. **Attendance**: Mark sample attendance
7. **Messaging**: Send test message
8. **Reports**: Generate sample report

### LMS Integration Tests

1. **LTI Configuration**: Add LMS config in admin panel
2. **Tool Launch**: Launch SAMS from LMS
3. **SSO**: Verify automatic login
4. **Grade Sync**: Mark attendance, check LMS gradebook
5. **Deep Link**: Embed SAMS in LMS course
6. **Course Sync**: Sync roster from LMS

---

## 🚀 Production Deployment

### Pre-Deployment Checklist

- [ ] SSL certificate installed and valid
- [ ] All passwords changed from defaults
- [ ] Error reporting disabled (`display_errors = 0`)
- [ ] Database backups configured
- [ ] Log rotation configured
- [ ] Firewall configured (ports 80, 443 open)
- [ ] File permissions set correctly
- [ ] Email tested and working
- [ ] LMS integration tested (if applicable)
- [ ] Performance tested with expected load

### Production PHP Settings

Edit `php.ini`:

```ini
; Security
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; Performance
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
post_max_size = 20M
upload_max_filesize = 10M

; Session Security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
session.cookie_samesite = "Strict"

; OpCache (Performance)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
opcache.validate_timestamps = 0
```

### Setup Automated Backups

```bash
# Create backup script
sudo nano /usr/local/bin/backup-sams.sh
```

```bash
#!/bin/bash
# SAMS Backup Script

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/sams"
DB_NAME="attendance_db"
DB_USER="attendance_user"
DB_PASS="your_password"
APP_DIR="/opt/lampp/htdocs/attendance"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup database
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db_$DATE.sql.gz"

# Backup files
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" -C "$(dirname "$APP_DIR")" "$(basename "$APP_DIR")"

# Keep only last 7 days of backups
find "$BACKUP_DIR" -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-sams.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
```

Add line:

```
0 2 * * * /usr/local/bin/backup-sams.sh >> /var/log/sams-backup.log 2>&1
```

### Monitoring Setup

```bash
# Install monitoring tools
sudo apt-get install -y htop iotop vnstat

# Monitor Apache logs
tail -f /var/log/apache2/attendance-access.log
tail -f /var/log/apache2/attendance-error.log

# Monitor MySQL
mysqladmin -u root -p processlist
mysqladmin -u root -p status
```

---

## 🔧 Troubleshooting

### Database Connection Errors

**Error**: "SQLSTATE[HY000] [2002] No such file or directory"

**Solution**:

```bash
# Check MySQL is running
sudo systemctl status mysql

# If not running, start it
sudo systemctl start mysql

# Verify connection
mysql -u attendance_user -p
```

### LTI Launch Fails

**Error**: "Invalid JWT token"

**Solutions**:

1. Check server time synchronization:

```bash
sudo ntpdate pool.ntp.org
```

2. Verify keys match:

```bash
openssl rsa -in lti_private_key.pem -pubout | diff - lti_public_key.pem
```

3. Enable debug mode in `/includes/config.php`:

```php
define('LTI_DEBUG_MODE', true);
```

### Grade Sync Not Working

**Check**:

1. LTI configuration has AGS enabled
2. `lti_grade_sync_log` table for errors
3. Access token is valid and not expired
4. LMS has grade passback enabled for the tool

### Email Not Sending

**Check**:

1. SMTP credentials are correct
2. Port 587 is not blocked by firewall
3. Enable less secure apps (for Gmail)
4. Use app-specific password (for Gmail with 2FA)

---

## 📞 Support

### Documentation

- [LMS Integration Guide](docs/LMS_INTEGRATION_GUIDE.md)
- [Implementation Guide](docs/IMPLEMENTATION_GUIDE.md)
- [API Reference](docs/API_REFERENCE.md)

### Getting Help

1. Check logs: `/var/log/apache2/attendance-error.log`
2. Check database logs: `SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 50;`
3. Enable debug mode
4. Review this guide's troubleshooting section

---

**Installation Complete! 🎉**

Next steps:

1. Login as admin
2. Configure LMS integration (if applicable)
3. Create classes and users
4. Start tracking attendance!

---

**Last Updated**: November 24, 2025
**Version**: 2.1.0
**Status**: Production Ready ✅
