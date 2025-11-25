# 🎯 Student Attendance Management System - Complete Project Overview

## 📋 Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [System Architecture](#system-architecture)
4. [Database Schema](#database-schema)
5. [User Roles & Features](#user-roles--features)
6. [File Structure](#file-structure)
7. [Core Systems](#core-systems)
8. [API Endpoints](#api-endpoints)
9. [UI/UX Design](#uiux-design)
10. [Security Features](#security-features)
11. [Installation & Setup](#installation--setup)
12. [Testing Guide](#testing-guide)

---

## 🌟 System Overview

The **Student Attendance Management System** is a comprehensive web-based platform built with a cyberpunk-themed UI that manages attendance tracking, student records, communication, and administrative tasks for educational institutions.

### Key Highlights
- **Multi-Role System**: Admin, Teacher, Student, and Parent roles with distinct permissions
- **Real-time Attendance**: QR code scanning and manual check-in/check-out
- **Messaging System**: Broadcast and direct messaging with notifications
- **ID Management**: Automated ID assignment with format validation
- **Email Integration**: Verification emails and approval notifications
- **Responsive Design**: Cyberpunk-themed UI with neon effects and animations
- **Security**: Session management, CSRF protection, role-based access control

---

## 💻 Technology Stack

### Backend
- **PHP 8.x**: Server-side logic
- **MySQL 8.x**: Database (via LAMPP)
- **PDO**: Database abstraction layer
- **PHPMailer**: Email sending

### Frontend
- **HTML5**: Structure
- **CSS3**: Styling with custom cyberpunk theme
- **JavaScript (ES6+)**: Interactivity and AJAX
- **Font Awesome**: Icons
- **Google Fonts** (Orbitron): Cyberpunk typography

### Server
- **LAMPP Stack**: Linux, Apache, MySQL, PHP
- **Apache 2.4**: Web server
- **ModRewrite**: URL routing

### Libraries
- **QRCode.js**: QR code generation
- **Chart.js**: Analytics and reporting
- **DataTables**: Enhanced table functionality

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER (Browser)                   │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐           │
│  │ Admin UI   │  │ Teacher UI │  │ Student UI │  ...       │
│  └────────────┘  └────────────┘  └────────────┘           │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTPS
┌───────────────────────────▼─────────────────────────────────┐
│                   PRESENTATION LAYER (PHP)                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Pages        │  │ Includes     │  │ API          │     │
│  │ (Dashboard,  │  │ (Auth, Nav,  │  │ (REST JSON)  │     │
│  │  Forms, etc) │  │  Functions)  │  │              │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└───────────────────────────┬─────────────────────────────────┘
                            │ PDO
┌───────────────────────────▼─────────────────────────────────┐
│                    DATA LAYER (MySQL)                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Users Table  │  │ Students     │  │ Messages     │     │
│  │ Teachers     │  │ Attendance   │  │ Notifications│     │
│  │ Classes      │  │ Parents      │  │ Logs         │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### Request Flow
1. **User Request** → Apache routes to PHP file
2. **Authentication** → Session validation via `auth.php`
3. **Authorization** → Role-based access check
4. **Business Logic** → PHP processes request
5. **Database Query** → PDO executes prepared statement
6. **Response** → HTML rendered or JSON returned
7. **Client Update** → Page displays or JavaScript updates DOM

---

## 🗄️ Database Schema

### Core Tables

#### 1. **users** (Central user management)
```sql
id (PK) | username | password | email | first_name | last_name
role (admin/teacher/student/parent) | status (active/inactive)
approved (0/1) | verification_token | token_expiry | created_at
```

#### 2. **students** (Student-specific data)
```sql
id (PK) | user_id (FK→users) | student_id (STU20250001)
grade | section | parent_id (FK→parents) | status | created_at
```

#### 3. **teachers** (Teacher-specific data)
```sql
id (PK) | user_id (FK→users) | teacher_id (TCH20250001)
department | subject | status | created_at
```

#### 4. **parents** (Parent-specific data)
```sql
id (PK) | user_id (FK→users) | phone | address | created_at
```

#### 5. **classes** (Class management)
```sql
id (PK) | class_name | grade | section | teacher_id (FK→teachers)
subject | schedule | room_number | status | created_at
```

#### 6. **attendance** (Attendance records)
```sql
id (PK) | student_id (FK→students) | class_id (FK→classes)
date | status (present/absent/late/excused) | check_in_time
check_out_time | remarks | recorded_by (FK→users) | created_at
```

#### 7. **messages** (Communication system)
```sql
id (PK) | sender_id (FK→users) | receiver_id (FK→users)
recipient_role (all/student/teacher/parent) | subject | message
is_read | read_at | created_at
```

#### 8. **message_recipients** (Broadcast message tracking)
```sql
id (PK) | message_id (FK→messages) | recipient_id (FK→users)
is_read | read_at | deleted_at | created_at
```

#### 9. **notifications** (User notifications)
```sql
id (PK) | user_id (FK→users) | title | message | type
link | is_read | read_at | created_at
```

### Relationships
```
users (1) ───< (N) students
users (1) ───< (N) teachers
users (1) ───< (N) parents
parents (1) ───< (N) students (via parent_id)
teachers (1) ───< (N) classes
classes (1) ───< (N) attendance
students (1) ───< (N) attendance
messages (1) ───< (N) message_recipients
```

---

## 👥 User Roles & Features

### 🔴 Admin Role
**Dashboard**: `/admin/dashboard.php`

**Capabilities**:
- ✅ **User Management**: Approve/reject registrations, manage user accounts
- ✅ **ID Assignment**: Assign/edit Student IDs (STU20250001) and Teacher IDs (TCH20250001)
- ✅ **Class Management**: Create classes, assign teachers, manage schedules
- ✅ **Broadcast Messaging**: Send messages to all users, specific roles, or individuals
- ✅ **Reports & Analytics**: View system-wide attendance, user statistics
- ✅ **Settings**: Configure system settings, security policies
- ✅ **Audit Logs**: View system activity and changes

**Key Pages**:
```
/admin/dashboard.php       - Overview with statistics
/admin/manage-ids.php      - ID assignment interface (tabbed)
/admin/users.php           - User management
/admin/approve-users.php   - Registration approvals
/admin/classes.php         - Class management
/admin/reports.php         - Attendance reports
/admin/analytics.php       - System analytics
/admin/settings.php        - System configuration
```

---

### 🟢 Teacher Role
**Dashboard**: `/teacher/dashboard.php`

**Capabilities**:
- ✅ **My Classes**: View assigned classes and schedules
- ✅ **Attendance Marking**: Mark students present/absent/late/excused
- ✅ **Student Records**: View student information and attendance history
- ✅ **Messaging**: Send messages to students, parents, other teachers
- ✅ **Reports**: Generate class-specific attendance reports
- ✅ **Profile**: Manage personal information and settings

**Key Pages**:
```
/teacher/dashboard.php         - Class overview
/teacher/my-classes.php        - Assigned classes list
/teacher/mark-attendance.php   - Attendance interface
/teacher/students.php          - Student directory
/teacher/reports.php           - Attendance reports
/teacher/settings.php          - Profile settings
```

---

### 🔵 Student Role
**Dashboard**: `/student/dashboard.php`

**Capabilities**:
- ✅ **Check-in/Check-out**: Self-service attendance via student ID
- ✅ **View Schedule**: See class timetable
- ✅ **Attendance History**: View personal attendance records
- ✅ **Messaging**: Receive announcements, contact teachers
- ✅ **Profile**: Update personal information
- ✅ **Notifications**: Real-time alerts for messages and updates

**Key Pages**:
```
/student/dashboard.php     - Overview with today's schedule
/student/checkin.php       - Self check-in interface
/student/schedule.php      - Class schedule
/student/attendance.php    - Attendance history
/student/profile.php       - Personal profile
/student/settings.php      - Account settings
```

---

### 🟡 Parent Role
**Dashboard**: `/parent/dashboard.php`

**Capabilities**:
- ✅ **Children's Attendance**: View linked children's attendance
- ✅ **Reports**: Access children's attendance reports
- ✅ **Messaging**: Receive updates, contact teachers
- ✅ **Child Linking**: Link multiple children to account
- ✅ **Notifications**: Alerts for absences and announcements

**Key Pages**:
```
/parent/dashboard.php      - Children overview
/parent/children.php       - Linked children list
/parent/attendance.php     - Children's attendance
/parent/reports.php        - Attendance reports
/parent/communication.php  - Messages (redirects to /messages.php)
/parent/settings.php       - Account settings
```

---

## 📁 File Structure

```
/opt/lampp/htdocs/attendance/
│
├── index.php                      # Landing page (redirects to login)
├── login.php                      # Login interface
├── register.php                   # Registration with email verification
├── verify-email.php               # Email verification handler
├── logout.php                     # Session termination
├── messages.php                   # Universal messaging interface
├── PROJECT_OVERVIEW.md            # This document
│
├── includes/                      # Shared components
│   ├── database.php               # PDO database connection
│   ├── functions.php              # Helper functions
│   ├── auth.php                   # Authentication middleware
│   ├── cyber-nav.php              # Role-based navigation sidebar
│   └── send-email.php             # PHPMailer wrapper
│
├── api/                           # REST API endpoints
│   ├── messaging.php              # Message send/receive/read/delete
│   ├── attendance.php             # Attendance CRUD operations
│   └── notifications.php          # Notification management
│
├── admin/                         # Admin-only pages
│   ├── dashboard.php
│   ├── manage-ids.php             # Student/Teacher ID assignment
│   ├── users.php
│   ├── approve-users.php
│   ├── classes.php
│   ├── reports.php
│   ├── analytics.php
│   └── settings.php
│
├── teacher/                       # Teacher pages
│   ├── dashboard.php
│   ├── my-classes.php
│   ├── mark-attendance.php
│   ├── students.php
│   ├── reports.php
│   └── settings.php
│
├── student/                       # Student pages
│   ├── dashboard.php
│   ├── checkin.php                # Self check-in with ID entry
│   ├── schedule.php
│   ├── attendance.php
│   ├── profile.php
│   └── settings.php
│
├── parent/                        # Parent pages
│   ├── dashboard.php
│   ├── children.php
│   ├── attendance.php
│   ├── reports.php
│   ├── communication.php
│   └── settings.php
│
├── assets/                        # Static resources
│   ├── css/
│   │   ├── cyberpunk-ui.css       # Main theme stylesheet
│   │   └── animations.css         # Neon animations
│   ├── js/
│   │   ├── main.js                # Global JavaScript
│   │   └── qrcode.js              # QR code library
│   ├── images/
│   │   ├── logo.png
│   │   └── backgrounds/
│   ├── locales/
│   │   └── en.json                # Internationalization
│   └── templates/
│       └── email/                 # Email templates
│
├── config/                        # Configuration files
│   ├── env-settings.json          # Environment variables
│   ├── integration-keys.ini       # API keys
│   └── security-policies.yaml     # Security settings
│
├── docs/                          # Documentation
│   ├── api-specs.yaml             # API documentation
│   └── requirements.md            # Project requirements
│
├── logs/                          # Application logs
│   ├── error.log
│   ├── access.log
│   └── email.log
│
├── scripts/                       # Utility scripts
│   ├── setup_database.sql         # Initial database setup
│   ├── fix_messaging_schema.sql   # Messaging system fix
│   ├── backup.py                  # Database backup
│   ├── deploy.sh                  # Deployment script
│   └── migrate-data.sql           # Data migration
│
└── tests/                         # Testing
    ├── unit/                      # Unit tests
    ├── integration/               # Integration tests
    └── e2e/                       # End-to-end tests
```

---

## 🔧 Core Systems

### 1. **Authentication System**

**Registration Flow**:
1. User fills registration form (`register.php`)
2. System generates verification token (10-minute expiry)
3. Verification email sent via PHPMailer
4. User clicks link → `verify-email.php?token=xxx`
5. Token validated → Account marked as verified
6. Admin approves → Assigns Student/Teacher ID → Approval email sent
7. User can now login

**Login Flow**:
1. User enters credentials (`login.php`)
2. Password verified via `password_verify()`
3. Session created with `$_SESSION['user_id']`, `$_SESSION['role']`
4. Redirect to role-specific dashboard
5. `auth.php` included on protected pages validates session

**Session Security**:
```php
session_start();
session_regenerate_id(true); // Prevent session fixation
// Timeout after 30 minutes inactivity
if (time() - $_SESSION['last_activity'] > 1800) {
    session_destroy();
}
```

---

### 2. **Messaging System**

**Architecture**:
- **Universal Interface**: `/messages.php` (all roles)
- **API Backend**: `/api/messaging.php` (RESTful)
- **Database**: `messages` + `message_recipients` tables

**Message Types**:
1. **Direct Message**: User to user (`receiver_id` set)
2. **Broadcast - All Users**: Admin to everyone (`recipient_role = 'all'`)
3. **Broadcast - Role**: Admin to specific role (`recipient_role = 'student'`)

**Send Flow (Broadcast)**:
```
Admin composes → Selects "All Students"
  ↓
POST /api/messaging.php {action: 'send', recipient_role: 'student', subject, message}
  ↓
Insert into messages (sender_id, recipient_role, subject, message)
  ↓
Query users WHERE role = 'student' AND status = 'active'
  ↓
For each student:
  - Insert into message_recipients (message_id, recipient_id)
  - Insert into notifications (user_id, title, message)
  ↓
Return success with recipient count
```

**Inbox Query**:
```sql
SELECT DISTINCT m.*, u.first_name, u.last_name,
       COALESCE(mr.is_read, m.is_read, 0) as is_read
FROM messages m
JOIN users u ON m.sender_id = u.id
LEFT JOIN message_recipients mr ON m.id = mr.message_id AND mr.recipient_id = ?
WHERE (mr.recipient_id = ? AND mr.deleted_at IS NULL)
   OR (m.receiver_id = ? AND m.recipient_role IS NULL)
ORDER BY m.created_at DESC
```

**API Endpoints**:
- `POST /api/messaging.php?action=send` - Send message
- `GET /api/messaging.php?action=inbox` - Fetch received messages
- `GET /api/messaging.php?action=sent` - Fetch sent messages
- `POST /api/messaging.php?action=read` - Mark as read
- `POST /api/messaging.php?action=delete` - Soft delete
- `GET /api/messaging.php?action=users` - Get user list for direct messaging
- `GET /api/messaging.php?action=unread_count` - Get unread count

---

### 3. **ID Management System**

**Location**: `/admin/manage-ids.php`

**ID Formats**:
- **Student ID**: `STU20250001` (STU + year + 4-digit sequential)
- **Teacher ID**: `TCH20250001` (TCH + year + 4-digit sequential)

**Features**:
- Tabbed interface (Students / Teachers / Parents)
- Inline editing with auto-formatting
- Duplicate ID validation
- Real-time statistics (total users, assigned IDs)

**JavaScript Auto-Format**:
```javascript
function formatStudentID(input) {
    let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (!value.startsWith('STU')) {
        value = 'STU' + value.replace(/^STU/, '');
    }
    if (value.length > 11) value = value.substring(0, 11);
    input.value = value;
}
```

**AJAX Save**:
```javascript
fetch('manage-ids.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=update_id&user_id=${userId}&new_id=${newId}&type=student`
}).then(res => res.json()).then(data => {
    if (data.success) showSuccess('ID updated');
    else showError(data.error);
});
```

---

### 4. **School Management System**

**Types**:
1. **Teacher Marked**: Teacher selects class → marks students
2. **Self Check-in**: Student enters ID → system records attendance
3. **QR Code**: Student scans QR → auto check-in

**Student Check-in Flow** (`/student/checkin.php`):
```
1. Student enters STU20250001
   ↓
2. JavaScript auto-formats input (prepends STU if missing)
   ↓
3. POST to same page with student_id
   ↓
4. Query: SELECT s.*, u.* FROM students s
          JOIN users u ON s.user_id = u.id
          WHERE s.student_id = ? AND s.status = 'active'
   ↓
5. If found:
   - Check if already checked in today
   - Insert attendance record (status='present', check_in_time=NOW())
   - Display success with student info
   ↓
6. If not found: Show error "Student ID not found"
```

**Attendance Table Structure**:
```sql
INSERT INTO attendance (
    student_id, class_id, date, status,
    check_in_time, recorded_by, created_at
) VALUES (?, ?, CURDATE(), 'present', NOW(), ?, NOW())
```

---

### 5. **Navigation System**

**Location**: `/includes/cyber-nav.php`

**Role-Based Menus**:
```php
<?php
$user_role = $_SESSION['role'] ?? 'guest';
$user_id = $_SESSION['user_id'] ?? 0;

// Get unread message count
$unread_result = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM message_recipients
    WHERE recipient_id = ? AND is_read = 0 AND deleted_at IS NULL
", [$user_id]);
$unread_count = $unread_result['count'] ?? 0;

// Render menu based on role
if ($user_role === 'admin') {
    // Admin menu items
} elseif ($user_role === 'teacher') {
    // Teacher menu items
} elseif ($user_role === 'student') {
    // Student menu items
} elseif ($user_role === 'parent') {
    // Parent menu items
}
?>
```

**Unread Badge**:
```html
<a href="/attendance/messages.php" class="<?= $active_page == 'messages' ? 'active' : '' ?>">
    <i class="fas fa-envelope"></i>
    <span>Messages</span>
    <?php if ($unread_count > 0): ?>
        <span class="badge"><?= $unread_count ?></span>
    <?php endif; ?>
</a>
```

---

## 🔌 API Endpoints

### **Messaging API** (`/api/messaging.php`)

All requests require authentication via session.

#### **1. Send Message**
```http
POST /api/messaging.php
Content-Type: application/x-www-form-urlencoded

action=send
&receiver_id=5                    # For direct message (optional)
&recipient_role=student           # For broadcast (all/student/teacher/parent)
&subject=Important Announcement
&message=Classes cancelled tomorrow
```

**Response**:
```json
{
    "success": true,
    "message_id": 42,
    "recipients_count": 150,
    "message": "Broadcast message sent to 150 users"
}
```

#### **2. Get Inbox**
```http
GET /api/messaging.php?action=inbox
```

**Response**:
```json
{
    "success": true,
    "messages": [
        {
            "id": 42,
            "sender_id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "sender_role": "admin",
            "subject": "Important Announcement",
            "message": "Classes cancelled tomorrow",
            "is_read": 0,
            "read_at": null,
            "created_at": "2025-01-15 10:30:00"
        }
    ]
}
```

#### **3. Mark as Read**
```http
POST /api/messaging.php
Content-Type: application/x-www-form-urlencoded

action=read&message_id=42
```

**Response**:
```json
{"success": true}
```

#### **4. Get Unread Count**
```http
GET /api/messaging.php?action=unread_count
```

**Response**:
```json
{"success": true, "count": 3}
```

---

## 🎨 UI/UX Design

### **Cyberpunk Theme**

**Color Palette**:
```css
:root {
    --cyber-bg: #0a0e27;           /* Dark blue background */
    --cyber-card: #1a1f3a;         /* Card background */
    --cyber-primary: #00f3ff;      /* Neon cyan */
    --cyber-secondary: #ff00ff;    /* Neon magenta */
    --cyber-accent: #ffff00;       /* Neon yellow */
    --cyber-text: #e0e0e0;         /* Light gray text */
    --cyber-border: rgba(0, 243, 255, 0.3); /* Cyan border */
}
```

**Key Visual Elements**:
- **Neon Borders**: `box-shadow: 0 0 20px var(--cyber-primary)`
- **Glitch Effects**: Text animation on hover
- **Grid Background**: Diagonal lines with glow
- **Holographic Cards**: Semi-transparent with backdrop blur
- **Pulsing Animations**: `@keyframes pulse` for buttons

**Typography**:
```css
font-family: 'Orbitron', 'Courier New', monospace;
```

**Button Styles**:
```css
.cyber-btn {
    background: linear-gradient(45deg, var(--cyber-primary), var(--cyber-secondary));
    border: 2px solid var(--cyber-primary);
    box-shadow: 0 0 20px rgba(0, 243, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: all 0.3s ease;
}

.cyber-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 30px rgba(0, 243, 255, 0.8);
}
```

**Responsive Design**:
```css
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .sidebar.active {
        transform: translateX(0);
    }
}
```

---

## 🔒 Security Features

### 1. **Password Security**
```php
// Registration
$hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Login
if (password_verify($entered_password, $stored_hash)) {
    // Success
}
```

### 2. **SQL Injection Prevention**
```php
// Prepared statements with PDO
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

### 3. **XSS Protection**
```php
// Output escaping
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

### 4. **CSRF Protection**
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate on form submit
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}
```

### 5. **Session Security**
```php
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,  // HTTPS only
    'cookie_samesite' => 'Strict'
]);
```

### 6. **Email Verification**
```php
// 10-minute token expiry
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

// Validate
if (time() > strtotime($token_expiry)) {
    die('Token expired');
}
```

### 7. **Role-Based Access Control**
```php
// In auth.php
if (!in_array($_SESSION['role'], $allowed_roles)) {
    http_response_code(403);
    die('Access denied');
}
```

---

## 📦 Installation & Setup

### Prerequisites
- **LAMPP/XAMPP**: Linux/Apache/MySQL/PHP stack
- **PHP 8.0+**: With PDO, mbstring, openssl extensions
- **MySQL 8.0+**: Or MariaDB 10.5+
- **Composer**: For PHPMailer dependencies

### Step 1: Clone/Download Project
```bash
cd /opt/lampp/htdocs/
git clone https://github.com/yourusername/attendance.git
# or extract ZIP
```

### Step 2: Database Setup
```bash
# Start MySQL
sudo /opt/lampp/lampp startmysql

# Create database
mysql -u root -h localhost --socket=/opt/lampp/var/mysql/mysql.sock
```

```sql
CREATE DATABASE attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE attendance_system;
SOURCE /opt/lampp/htdocs/attendance/scripts/setup_database.sql;
SOURCE /opt/lampp/htdocs/attendance/scripts/fix_messaging_schema.sql;
EXIT;
```

### Step 3: Configure Database Connection
Edit `/includes/database.php`:
```php
<?php
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $host = 'localhost';
        $db = 'attendance_system';
        $user = 'root';
        $pass = '';  // LAMPP default
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, $user, $pass, $options);
    }
    return $pdo;
}
```

### Step 4: Configure Email (PHPMailer)
Edit `/includes/send-email.php`:
```php
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';  // Use App Password, not regular password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->setFrom('your-email@gmail.com', 'School Management System');
```

### Step 5: Set Permissions
```bash
chmod 755 /opt/lampp/htdocs/attendance
chmod 777 /opt/lampp/htdocs/attendance/logs  # Log directory writable
```

### Step 6: Create Admin Account
```sql
INSERT INTO users (username, email, password, first_name, last_name, role, status, approved, created_at)
VALUES (
    'admin',
    'admin@school.edu',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY0N1rWW3n2F7ni',  -- password: admin123
    'System',
    'Administrator',
    'admin',
    'active',
    1,
    NOW()
);
```

### Step 7: Test Installation
```bash
# Start LAMPP
sudo /opt/lampp/lampp start

# Open browser
http://localhost/attendance/login.php

# Login with:
# Username: admin
# Password: admin123
```

---

## 🧪 Testing Guide

### **1. Registration & Email Verification**

**Test Case**: New student registration
```
1. Navigate to http://localhost/attendance/register.php
2. Fill form:
   - Username: test_student
   - Email: your-test-email@gmail.com
   - Password: Test@123
   - First Name: Test
   - Last Name: Student
   - Role: Student
3. Submit → Should show "Registration successful, check email"
4. Check inbox for verification email (10-minute expiry)
5. Click verification link
6. Should redirect to login with "Email verified" message
```

**Expected Results**:
- ✅ Email received within 1 minute
- ✅ Token valid for 10 minutes
- ✅ After verification, `approved` field still 0 (needs admin approval)

---

### **2. Admin Approval & ID Assignment**

**Test Case**: Admin approves new student
```
1. Login as admin
2. Go to Admin → Approve Users
3. Find "Test Student" in pending list
4. Click "Approve"
5. System assigns student ID (e.g., STU20250001)
6. Approval email sent to student
7. Student can now login
```

**Verify**:
```sql
SELECT * FROM students WHERE user_id = (SELECT id FROM users WHERE username = 'test_student');
-- Should show student_id = 'STU20250001'
```

---

### **3. Student Check-in**

**Test Case**: Student self check-in
```
1. Login as test_student
2. Go to Student → Check-in
3. Enter student ID: STU20250001 (or just type "20250001", auto-formats)
4. Click "Check In"
5. Should show success message with student name
```

**Verify**:
```sql
SELECT * FROM attendance WHERE student_id = (SELECT id FROM students WHERE student_id = 'STU20250001') AND date = CURDATE();
-- Should show new record with status='present', check_in_time=NOW()
```

---

### **4. Admin Broadcast Messaging**

**Test Case**: Admin sends message to all students
```
1. Login as admin
2. Go to Messages
3. Click "Compose"
4. Select recipient: "All Students"
5. Subject: "Test Announcement"
6. Message: "This is a test broadcast message"
7. Click "Send"
8. Should show "Broadcast message sent to X users"
```

**Verify Database**:
```sql
-- Check message created
SELECT * FROM messages WHERE subject = 'Test Announcement';

-- Check recipients created
SELECT COUNT(*) FROM message_recipients WHERE message_id = (SELECT id FROM messages WHERE subject = 'Test Announcement');
-- Should match number of students

-- Check notifications created
SELECT COUNT(*) FROM notifications WHERE title LIKE '%Test Announcement%';
-- Should match number of students
```

**Verify Recipient Inbox**:
```
1. Login as test_student
2. Go to Messages
3. Should see "Test Announcement" in inbox
4. Unread badge should show "1"
```

---

### **5. ID Management (Inline Edit)**

**Test Case**: Admin edits teacher ID
```
1. Login as admin
2. Go to Admin → Manage IDs
3. Click "Teachers" tab
4. Find teacher, click on ID field
5. Change ID to TCH20250099
6. Press Enter or click outside
7. Should show success message
```

**Verify**:
```sql
SELECT teacher_id FROM teachers WHERE user_id = ?;
-- Should show 'TCH20250099'
```

**Test Duplicate Prevention**:
```
1. Try to assign same ID to different teacher
2. Should show error "ID already in use"
```

---

### **6. Role-Based Navigation**

**Test Case**: Verify each role sees correct menu
```
Admin sees:
- Dashboard, Students, Teachers, Classes, Manage IDs, Messages, Reports, Analytics, Users, Settings

Teacher sees:
- Dashboard, My Classes, Students, Mark Attendance, Messages, Reports, Settings

Student sees:
- Dashboard, Schedule, Attendance, Check-in, Messages, Profile, Settings

Parent sees:
- Dashboard, Children, Attendance, Messages, Contact Teachers, Reports, Settings
```

**Verify Unread Badge**:
```
1. Send message to student
2. Login as student
3. Navigation should show badge with "1" next to Messages
4. Click Messages → open message
5. Badge should disappear
```

---

## 📊 System Statistics

### Database Queries Performance
```sql
-- Optimize attendance lookup
CREATE INDEX idx_student_date ON attendance(student_id, date);

-- Optimize message inbox
CREATE INDEX idx_recipient_read ON message_recipients(recipient_id, is_read, deleted_at);

-- Optimize user lookup by role
CREATE INDEX idx_role_status ON users(role, status, approved);
```

### Expected Load Times
- **Login**: < 500ms
- **Dashboard**: < 1s
- **Message Inbox**: < 800ms (100 messages)
- **Attendance Report**: < 2s (1000 records)

---

## 🚀 Future Enhancements

### Planned Features
1. **Mobile App**: React Native for iOS/Android
2. **QR Code Attendance**: Generate class-specific QR codes
3. **Geolocation**: Verify students are on campus during check-in
4. **Analytics Dashboard**: Chart.js visualizations
5. **Export Reports**: PDF/Excel export
6. **SMS Notifications**: Twilio integration for absences
7. **Parent App**: Dedicated parent mobile interface
8. **Multi-Language**: Full i18n support
9. **Dark Mode Toggle**: User preference for theme
10. **API Documentation**: Swagger/OpenAPI specs

---

## 📞 Support & Contact

### Documentation
- **API Docs**: `/docs/api-specs.yaml`
- **Requirements**: `/docs/requirements.md`
- **This Guide**: `/PROJECT_OVERVIEW.md`

### Logs
- **Error Logs**: `/logs/error.log`
- **Email Logs**: `/logs/email.log`
- **Access Logs**: `/var/log/apache2/access.log`

### Troubleshooting

**Issue**: Can't login after registration
- **Solution**: Check if email verified and admin approved account

**Issue**: Student ID not found during check-in
- **Solution**: Verify admin assigned student ID in Manage IDs panel

**Issue**: Broadcast messages not delivering
- **Solution**: Ensure `fix_messaging_schema.sql` was executed

**Issue**: Emails not sending
- **Solution**: Check SMTP credentials in `/includes/send-email.php`, enable "Less secure apps" in Gmail or use App Password

---

## 📜 License

This project is proprietary software developed for educational institutions.

---

## 🎯 Quick Reference

### Default Credentials
```
Admin:
Username: admin
Password: admin123 (CHANGE IMMEDIATELY)
```

### Important URLs
```
Login:        http://localhost/attendance/login.php
Register:     http://localhost/attendance/register.php
Admin Panel:  http://localhost/attendance/admin/dashboard.php
Messages:     http://localhost/attendance/messages.php
```

### Database Access
```bash
mysql -u root -h localhost --socket=/opt/lampp/var/mysql/mysql.sock attendance_system
```

### Server Control
```bash
sudo /opt/lampp/lampp start
sudo /opt/lampp/lampp stop
sudo /opt/lampp/lampp restart
```

---

**Last Updated**: January 2025
**Version**: 1.0.0
**Status**: Production Ready ✅
