# 🎓 LMS Integration Guide - Student Attendance Management System

## 📋 Table of Contents

1. [Overview](#overview)
2. [Supported LMS Platforms](#supported-lms-platforms)
3. [LTI 1.3 Standard Compliance](#lti-13-standard-compliance)
4. [Prerequisites](#prerequisites)
5. [Installation Steps](#installation-steps)
6. [Configuration Guide](#configuration-guide)
7. [Feature Overview](#feature-overview)
8. [API Reference](#api-reference)
9. [Troubleshooting](#troubleshooting)
10. [Security Best Practices](#security-best-practices)

---

## 🌟 Overview

The Student Attendance Management System (SAMS) integrates seamlessly with Learning Management Systems (LMS) using the **LTI 1.3 (Learning Tools Interoperability)** standard. This integration enables:

- **Single Sign-On (SSO)**: Users authenticate once in the LMS and access SAMS automatically
- **Grade Passback**: Attendance data syncs to LMS gradebooks automatically
- **Deep Linking**: Embed SAMS features directly within LMS courses
- **Course Synchronization**: Automatic roster and course data syncing
- **Unified Experience**: Seamless workflow between LMS and attendance system

---

## 🎯 Supported LMS Platforms

SAMS supports the following LMS platforms via LTI 1.3:

| LMS Platform          | Version      | Status             | Notes                        |
| --------------------- | ------------ | ------------------ | ---------------------------- |
| **Moodle**            | 3.5+         | ✅ Fully Supported | Requires LTI 1.3 plugin      |
| **Canvas**            | All versions | ✅ Fully Supported | Native LTI 1.3 support       |
| **Blackboard Learn**  | 9.1+         | ✅ Supported       | Ultra experience recommended |
| **Brightspace (D2L)** | 10.8+        | ✅ Supported       | LTI Advantage required       |
| **Sakai**             | 19.0+        | ⚠️ Beta            | Limited testing              |
| **Open edX**          | Juniper+     | ⚠️ Beta            | Custom configuration needed  |
| **Google Classroom**  | N/A          | ❌ Not Supported   | No LTI 1.3 support           |

---

## 📚 LTI 1.3 Standard Compliance

### What is LTI 1.3?

LTI (Learning Tools Interoperability) 1.3 is a standard developed by IMS Global (1EdTech) that enables secure integration between learning platforms and external tools.

### Key Features Implemented

✅ **OpenID Connect (OIDC) Authentication**

- Secure JWT-based authentication
- Public/private key encryption
- Token validation and expiration

✅ **OAuth 2.0 Authorization**

- Client credentials grant flow
- Scope-based permissions
- Access token management

✅ **LTI Advantage Services**

- **Assignment and Grade Services (AGS)**: Grade passback to LMS
- **Names and Role Provisioning Services (NRPS)**: Course roster sync
- **Deep Linking (DL)**: Embed resources in courses

✅ **Security Framework**

- Nonce validation (replay attack prevention)
- HTTPS enforcement
- JWT signature verification
- State parameter validation

---

## ✅ Prerequisites

### System Requirements

1. **PHP 7.4 or higher** with extensions:

   - `openssl` (for JWT encryption)
   - `curl` (for LMS API calls)
   - `json` (for data exchange)
   - `pdo_mysql` (for database)

2. **MySQL 5.7 or higher**

3. **HTTPS/SSL Certificate**

   - **Required** for LTI 1.3 compliance
   - Self-signed certificates acceptable for testing only

4. **LAMPP/XAMPP Stack** (if using local development)

### LMS Administrator Access

You will need:

- Administrator or instructor access to your LMS
- Ability to register external tools
- Access to LMS developer/integration settings

---

## 🚀 Installation Steps

### Step 1: Apply Database Schema

Run the LTI schema SQL file to create necessary tables:

```bash
cd /opt/lampp/htdocs/attendance
mysql -u root -p your_database_name < database/lti_schema.sql
```

This creates the following tables:

- `lti_configurations` - Stores LMS connection settings
- `lti_sessions` - Tracks active LTI launches
- `lti_resource_links` - Maps embedded resources
- `lti_context_mappings` - Links LMS courses to SAMS classes
- `lti_user_mappings` - Maps LMS users to SAMS users
- `lti_grade_sync_log` - Audit log for grade syncing
- `lti_nonce_store` - Prevents replay attacks

### Step 2: Generate RSA Key Pair

SAMS needs a public/private key pair for JWT signing:

```bash
# Generate private key (2048-bit RSA)
openssl genrsa -out lti_private_key.pem 2048

# Extract public key
openssl rsa -in lti_private_key.pem -pubout -out lti_public_key.pem

# Display public key (for LMS configuration)
cat lti_public_key.pem

# Display private key (for SAMS database)
cat lti_private_key.pem
```

⚠️ **Security Note**: Store private keys securely and never commit to version control!

### Step 3: Configure HTTPS

LTI 1.3 requires HTTPS. For production:

```bash
# Install Let's Encrypt certificate
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

For development (self-signed):

```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/selfsigned.key \
  -out /etc/ssl/certs/selfsigned.crt
```

### Step 4: Update Apache Configuration

Enable SSL and mod_rewrite:

```bash
sudo a2enmod ssl
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## ⚙️ Configuration Guide

### Configure SAMS (Tool Side)

1. **Login as Admin** to SAMS
2. Navigate to **Admin Panel** → **LMS Integration Settings**
3. Click **"Add New LMS Configuration"**
4. Fill in the form:

#### Basic Information

- **LMS Platform**: Select (Moodle/Canvas/Blackboard/etc.)
- **Platform Name**: Friendly name (e.g., "University Moodle")
- **Is Active**: ✅ Check to enable

#### LTI 1.3 Settings (provided by your LMS)

- **Client ID**: Unique identifier from LMS
- **Issuer**: LMS URL (e.g., `https://moodle.university.edu`)
- **Deployment ID**: Deployment identifier from LMS
- **Auth Login URL**: OIDC auth endpoint
- **Auth Token URL**: OAuth token endpoint
- **JWKS URL**: Public key set URL

#### Tool Keys (generated in Step 2)

- **Public Key**: Paste contents of `lti_public_key.pem`
- **Private Key**: Paste contents of `lti_private_key.pem`

#### Advanced Options

- **Auto-Sync Enabled**: ✅ Enable automatic grade syncing
- **Sync Frequency**: 3600 seconds (1 hour)
- **Grade Passback Enabled**: ✅ Enable
- **Deep Linking Enabled**: ✅ Enable

5. Click **"Save Configuration"**

---

### Configure Moodle (Platform Side)

#### Step 1: Enable LTI 1.3

1. Login as Moodle Administrator
2. Go to **Site Administration** → **Plugins** → **Activity modules** → **External tool**
3. Click **"Manage tools"**
4. Click **"Configure a tool manually"**

#### Step 2: Tool Settings

Fill in the registration form:

| Field                  | Value                                              |
| ---------------------- | -------------------------------------------------- |
| **Tool name**          | Student School Management System                          |
| **Tool URL**           | `https://yourdomain.com/api/lti.php?action=launch` |
| **LTI version**        | LTI 1.3                                            |
| **Public key type**    | RSA key                                            |
| **Public key**         | Paste SAMS public key from Step 2                  |
| **Initiate login URL** | `https://yourdomain.com/lti-login.php`             |
| **Redirection URI(s)** | `https://yourdomain.com/lti-redirect.php`          |

#### Step 3: Tool Configuration

| Setting                         | Value      |
| ------------------------------- | ---------- |
| **Default launch container**    | New window |
| **Support Deep Linking**        | ✅ Yes     |
| **Content-Item Message**        | ✅ Enabled |
| **Tool supports grades**        | ✅ Yes     |
| **Accept grades from the tool** | ✅ Yes     |

#### Step 4: Services

Enable the following LTI Advantage services:

- ✅ **IMS LTI Assignment and Grade Services**

  - Use this service for grade sync from external tools

- ✅ **IMS LTI Names and Role Provisioning**

  - Allows tool to retrieve course members

- ✅ **Tool Settings**
  - Allow tool to store settings

#### Step 5: Privacy Settings

Configure data sharing:

- ✅ Share launcher's name with tool
- ✅ Share launcher's email with tool
- ⚠️ Accept grades from the tool (required for grade passback)

#### Step 6: Save and Copy Configuration

1. Click **"Save changes"**
2. **Important**: Copy the following values (you'll need these for SAMS):

   - **Client ID** (e.g., `AbCdEfGh123456`)
   - **Platform ID / Issuer** (e.g., `https://moodle.university.edu`)
   - **Public keyset URL** (e.g., `https://moodle.university.edu/mod/lti/certs.php`)
   - **Access token URL** (e.g., `https://moodle.university.edu/mod/lti/token.php`)
   - **Authentication request URL** (e.g., `https://moodle.university.edu/mod/lti/auth.php`)
   - **Deployment ID** (usually auto-generated)

3. Go back to SAMS Admin → LMS Settings and enter these values

---

### Configure Canvas (Platform Side)

#### Step 1: Developer Keys

1. Login as Canvas Administrator
2. Go to **Admin** → **Developer Keys**
3. Click **"+ Developer Key"** → **"+ LTI Key"**

#### Step 2: Key Settings

Configure the LTI key:

| Field                             | Value                                              |
| --------------------------------- | -------------------------------------------------- |
| **Key Name**                      | Student School Management System                          |
| **Redirect URIs**                 | `https://yourdomain.com/lti-redirect.php`          |
| **Method**                        | Manual Entry                                       |
| **Title**                         | Student School Management System                          |
| **Description**                   | Attendance tracking and management                 |
| **Target Link URI**               | `https://yourdomain.com/api/lti.php?action=launch` |
| **OpenID Connect Initiation Url** | `https://yourdomain.com/lti-login.php`             |
| **JWK Method**                    | Public JWK URL                                     |
| **Public JWK URL**                | `https://yourdomain.com/lti-jwks.php`              |

#### Step 3: Configure Scopes

Enable the following LTI scopes:

- ✅ `https://purl.imsglobal.org/spec/lti-ags/scope/lineitem`
- ✅ `https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly`
- ✅ `https://purl.imsglobal.org/spec/lti-ags/scope/score`
- ✅ `https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly`

#### Step 4: Placements

Configure tool placements:

- ✅ **Course Navigation**: Adds link in course menu
- ✅ **Assignment Selection**: For deep linking
- ⚠️ **Global Navigation**: Optional (adds to all courses)

#### Step 5: Save and Enable

1. Click **"Save"**
2. Toggle the key to **"ON"** state
3. Copy the **Client ID** (displayed after saving)
4. Note Canvas URLs:

   - **Issuer**: `https://canvas.instructure.com`
   - **JWKS URL**: `https://canvas.instructure.com/api/lti/security/jwks`
   - **Token URL**: `https://canvas.instructure.com/login/oauth2/token`
   - **Auth URL**: `https://canvas.instructure.com/api/lti/authorize_redirect`

5. Enter these in SAMS LMS Settings

---

## 🎨 Feature Overview

### 1. Single Sign-On (SSO)

Users click an LTI link in their LMS and are automatically authenticated in SAMS.

**User Flow**:

1. Student clicks "Attendance" link in Moodle course
2. LMS sends LTI launch request to SAMS
3. SAMS validates JWT token
4. User is mapped/created in SAMS
5. Student is redirected to their dashboard

**Benefits**:

- No separate login required
- Seamless user experience
- Automatic user provisioning
- Role mapping (Student/Teacher/Admin)

### 2. Grade Passback

Attendance percentages automatically sync to LMS gradebook.

**How it works**:

1. Student marks attendance in SAMS
2. Attendance percentage is calculated (e.g., 85%)
3. SAMS sends grade to LMS via AGS API
4. Grade appears in LMS gradebook

**Configuration**:

- **Auto-Sync**: Grades sync hourly (configurable)
- **Manual Sync**: Teachers can trigger via "Sync to LMS" button
- **Grading Scale**: 0-100 (configurable per class)

**Admin View**:

```
Admin → LMS Settings → Grade Sync Log
- View all sync attempts
- See success/failure status
- Retry failed syncs
- Export sync reports
```

### 3. Deep Linking

Embed specific SAMS features directly in LMS courses.

**Available Resources**:

- Attendance Dashboard
- Class Reports
- Individual Student Reports
- Attendance Marking Interface

**How to use** (Moodle example):

1. In Moodle course, turn editing on
2. Click **"Add an activity or resource"**
3. Select **"External tool"**
4. Choose **"Student School Management System"**
5. Select resource type (e.g., "Class Attendance")
6. Save

Students will see the embedded interface directly in Moodle!

### 4. Course Synchronization

Automatically sync course rosters and schedules.

**What syncs**:

- ✅ Course name and description
- ✅ Enrolled students
- ✅ Instructors/TAs
- ✅ Course start/end dates
- ⚠️ Assignments (if enabled)

**Sync Options**:

- **Automatic**: Runs every hour
- **Manual**: Admin/Teacher triggers
- **On-Demand**: During LTI launch

**Admin Control**:

```
Admin → LMS Settings → Course Sync
- Enable/disable per course
- Set sync frequency
- View sync logs
- Resolve conflicts
```

---

## 🔌 API Reference

### LTI Launch Endpoint

**Endpoint**: `POST /api/lti.php?action=launch`

**Request Parameters**:

```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9...",
  "state": "random_state_string",
  "lti_config_id": 1
}
```

**Response** (Success):

```json
{
  "success": true,
  "message": "LTI launch successful",
  "redirect": "/student/dashboard.php",
  "new_user": false
}
```

**Response** (Error):

```json
{
  "success": false,
  "error": "Invalid LTI token"
}
```

---

### Grade Passback Endpoint

**Endpoint**: `POST /api/lti.php?action=grade_passback`

**Request Parameters**:

```json
{
  "user_id": 123,
  "lms_context_id": "course_12345",
  "grade_value": 85.5,
  "lti_config_id": 1,
  "sync_type": "manual"
}
```

**Response**:

```json
{
  "success": true,
  "message": "Grade synced to LMS successfully"
}
```

---

### Deep Link Endpoint

**Endpoint**: `POST /api/lti.php?action=deep_link`

**Request Parameters**:

```json
{
  "resource_type": "attendance",
  "resource_id": 5,
  "title": "CS101 Attendance",
  "lms_context_id": "course_12345",
  "lti_config_id": 1
}
```

**Response**:

```json
{
  "success": true,
  "resource_url": "https://yourdomain.com/lti-resource.php?type=attendance&id=5",
  "resource_link_id": "sams_attendance_5_abc123"
}
```

---

### Course Sync Endpoint

**Endpoint**: `POST /api/lti.php?action=sync_courses`

**Request Parameters**:

```json
{
  "lti_config_id": 1,
  "lms_context_id": "course_12345",
  "sync_users": true,
  "sync_schedule": true
}
```

**Response**:

```json
{
  "success": true,
  "users_synced": 45,
  "courses_synced": 1,
  "message": "Sync completed successfully"
}
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. "Invalid LTI token" Error

**Cause**: JWT signature verification failed

**Solutions**:

1. Verify public key is correctly copied to LMS
2. Check that private key is saved in SAMS
3. Ensure HTTPS is enabled (required for LTI 1.3)
4. Check system time synchronization (NTP)

```bash
# Verify keys match
openssl rsa -in lti_private_key.pem -pubout | diff - lti_public_key.pem
```

#### 2. "Token expired" Error

**Cause**: JWT expiration time exceeded

**Solutions**:

1. Check server time synchronization:

```bash
sudo ntpdate pool.ntp.org
```

2. Verify timezone settings in `php.ini`:

```ini
date.timezone = "UTC"
```

3. Reduce latency between LMS and SAMS

#### 3. Grades Not Syncing

**Cause**: AGS service not properly configured

**Solutions**:

1. Verify AGS scope is enabled in LMS
2. Check grade passback is enabled in SAMS config
3. Review sync logs:

```sql
SELECT * FROM lti_grade_sync_log ORDER BY created_at DESC LIMIT 20;
```

4. Manually trigger sync from teacher panel

#### 4. Users Not Auto-Creating

**Cause**: User provisioning settings

**Solutions**:

1. Check SAMS allows auto-user creation
2. Verify email is included in LTI launch claims
3. Check user role mapping in `lti_determine_role()` function
4. Review user creation logs

#### 5. Deep Links Not Working

**Cause**: Deep linking service not enabled

**Solutions**:

1. Enable Deep Linking in LMS tool settings
2. Verify deep_linking_enabled = 1 in SAMS config
3. Check redirect URIs match exactly
4. Test with simple resource first

---

### Debug Mode

Enable LTI debug logging:

1. Edit `/includes/config.php`:

```php
define('LTI_DEBUG_MODE', true);
define('LTI_LOG_FILE', '/var/log/sams_lti_debug.log');
```

2. View logs:

```bash
tail -f /var/log/sams_lti_debug.log
```

3. Check database logs:

```sql
SELECT * FROM system_logs WHERE log_type = 'lti_error' ORDER BY created_at DESC;
```

---

## 🔒 Security Best Practices

### 1. Key Management

✅ **DO**:

- Generate strong 2048-bit RSA keys minimum
- Store private keys outside web root
- Rotate keys annually
- Use environment variables for secrets

❌ **DON'T**:

- Commit keys to version control
- Share private keys via email/chat
- Use same keys across environments
- Use default/sample keys

### 2. HTTPS Enforcement

**Always use HTTPS for**:

- All LTI endpoints
- Redirect URIs
- JWKS URLs
- Token endpoints

**Apache Configuration**:

```apache
# Force HTTPS redirect
<VirtualHost *:80>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>
```

### 3. Nonce Validation

Prevent replay attacks:

```php
// Nonce cleanup (run via cron)
CALL cleanup_lti_nonces();
```

Add to crontab:

```bash
# Clean up old nonces daily
0 0 * * * mysql -u root -p'password' attendance_db -e "CALL cleanup_lti_nonces();"
```

### 4. Access Control

Implement rate limiting on LTI endpoints:

```php
// /includes/rate_limit.php
function check_lti_rate_limit($ip) {
    // Allow 10 launches per minute per IP
    return rate_limit($ip, 'lti_launch', 10, 60);
}
```

### 5. Audit Logging

Monitor all LTI activities:

```sql
-- View recent LTI launches
SELECT
    ls.created_at,
    u.email,
    u.role,
    ls.lms_context_id,
    ls.ip_address
FROM lti_sessions ls
JOIN users u ON ls.user_id = u.id
ORDER BY ls.created_at DESC
LIMIT 50;

-- Failed launch attempts
SELECT * FROM system_logs
WHERE log_type = 'lti_error'
AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY);
```

---

## 📊 Performance Optimization

### Database Indexing

Ensure indexes exist for optimal performance:

```sql
-- Check index usage
SHOW INDEX FROM lti_sessions;
SHOW INDEX FROM lti_user_mappings;

-- Add custom indexes if needed
CREATE INDEX idx_lti_sessions_context ON lti_sessions(lms_context_id, created_at);
CREATE INDEX idx_grade_sync_status ON lti_grade_sync_log(sync_status, created_at);
```

### Caching

Implement caching for frequently accessed data:

```php
// Cache LTI configuration
$cache_key = "lti_config_" . $config_id;
$config = cache_get($cache_key);

if (!$config) {
    $config = fetch_lti_config($config_id);
    cache_set($cache_key, $config, 3600); // 1 hour
}
```

---

## 📞 Support

### Getting Help

1. **Documentation**: Review this guide thoroughly
2. **System Logs**: Check `/var/log/apache2/error.log` and SAMS logs
3. **Database Logs**: Query `system_logs` table
4. **LMS Documentation**: Consult your LMS's LTI documentation
5. **IMS Global**: Reference official LTI 1.3 specs at [imsglobal.org](https://www.imsglobal.org/spec/lti/v1p3)

### Reporting Issues

When reporting LTI issues, include:

1. **LMS Platform**: Name and version
2. **Error Message**: Full text of error
3. **Server Logs**: Relevant log entries
4. **JWT Token** (sanitized): Remove sensitive claims
5. **Configuration**: LTI settings (sanitize keys)
6. **Steps to Reproduce**: Detailed user actions

---

## 📄 License

This LMS integration is part of the Student Attendance Management System and is licensed under the same terms as the main application.

---

**Last Updated**: November 24, 2025
**Version**: 2.1.0
**LTI Specification**: 1.3.0
**Status**: Production Ready ✅
