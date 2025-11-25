# 🏫 School Management System (SMS)

> **Complete School Administration & Academic Management Platform**

[![Status](https://img.shields.io/badge/Status-Production%20Ready-success)](/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)](/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)](/)
[![LTI](https://img.shields.io/badge/LTI-1.3%20Certified-green)](/)
[![License](https://img.shields.io/badge/License-MIT-green)](/)

## 🚀 Quick Start

```bash
# 1. Navigate to project
cd /opt/lampp/htdocs/attendance

# 2. Start services
sudo /opt/lampp/lampp start

# 3. Access system
http://localhost/attendance
```

**Default Admin:** `admin@school.edu` / `Admin@123`

## 📚 System Overview

A comprehensive, enterprise-grade school management platform designed to manage all aspects of educational institutions including academics, attendance, finance, library, transport, hostel, HR, inventory, and communication. Built with PHP and featuring **full LTI 1.3 compliance** for seamless LMS integration.

### 🌟 What's New in v3.0.0

**Complete School Management Transformation:**

- 🎓 **Academic Management** - Subjects, exams, grades, mark sheets, certificates, timetables
- 💰 **Finance & Fee Management** - Fee collection, invoices, expenses, salary, accounting
- 📚 **Library Management** - Book catalog, issue/return, member management, inventory
- 🚌 **Transport Management** - Routes, vehicles, drivers, GPS tracking, maintenance
- 🏠 **Hostel Management** - Room allocation, mess, visitor logs, complaints
- 👥 **HR & Payroll** - Staff management, attendance, leave, payroll, performance
- 📦 **Inventory & Assets** - Asset tracking, stock management, purchase orders
- 📊 **Advanced Reports** - Comprehensive analytics and reporting across all modules
- ✨ **LTI 1.3 Integration** - Full LMS connectivity (Moodle, Canvas, Blackboard, Brightspace)
- 🔐 **OpenID Connect SSO** - Single Sign-On from any LMS platform

### Core Modules

- ✅ **Academic Management** - Complete academic lifecycle from admission to graduation
- ✅ **School Management System** - Biometric, manual, QR code, and geofence-based attendance
- ✅ **Finance & Fees** - Complete financial management with invoicing and reporting
- ✅ **Examination System** - Exams, grading, mark sheets, progress cards, certificates
- ✅ **Library Management** - Digital library with issue/return and inventory tracking
- ✅ **Transport Management** - Fleet management with route optimization and GPS
- ✅ **Hostel Management** - Complete hostel operations including mess and visitors
- ✅ **HR & Payroll** - Staff lifecycle management with automated payroll
- ✅ **Inventory & Assets** - Asset tracking and inventory management
- ✅ **Communication Hub** - Messaging, notices, forums, SMS, email integration
- ✅ **LMS Integration** - LTI 1.3 compliant with major platforms
- ✅ **Mobile PWA** - Progressive Web App with offline support
- ✅ **Cyberpunk UI** - Modern, futuristic interface with neon effects

## 🎯 Key Features by Role

### 🔴 For Administrators

**Academic Management:**

- Subject & syllabus management
- Examination creation and scheduling
- Grade schemes and mark sheet generation
- Timetable creation and management
- Admission and enrollment management
- Certificate generation (TC, bonafide, etc.)

**Financial Management:**

- Fee structure setup and management
- Fee collection and invoicing
- Expense tracking and accounting
- Salary management and payroll
- Financial reports and analytics
- Payment gateway integration

**Operations:**

- Staff management and HR operations
- Library catalog and inventory management
- Transport routes and vehicle management
- Hostel room allocation and mess management
- Asset tracking and inventory control
- User management and role assignment

**System Administration:**

- LMS configuration and integration
- System analytics and dashboards
- Audit logs and monitoring
- Backup and restore operations
- Email/SMS gateway configuration
- Security and access control

### 🟢 For Teachers

**Academic:**

- Lesson planning and syllabus tracking
- Examination creation and grading
- Mark sheet generation and grade entry
- Assignment creation and evaluation
- Academic progress tracking
- Timetable viewing

**Student Management:**

- Attendance marking (biometric/manual/QR)
- Student behavior logs and remarks
- Individual student performance reports
- Parent communication and meeting scheduling
- Class roster and enrollment management

**Resources:**

- Class materials upload and sharing
- Library book recommendations
- Digital resource library access
- LMS integration and deep linking

### 🔵 For Students

**Academics:**

- View timetable and class schedule
- Access lesson plans and syllabus
- Submit assignments online
- View exam schedules and results
- Download mark sheets and certificates
- Track academic progress with analytics

**Library & Resources:**

- Search and request library books
- View issued books and due dates
- Access digital resources
- Pay late fees online

**Services:**

- Self check-in attendance (QR/biometric)
- View and pay fee invoices
- Apply for transport services
- Request hostel accommodation
- Download ID cards and certificates
- Track event calendar and RSVP

**Communication:**

- Chat with teachers and peers
- Access notice board and announcements
- LMS integration with Single Sign-On
- Parent-teacher meeting requests

### 🟡 For Parents

**Academic Monitoring:**

- View children's timetables and attendance
- Track exam schedules and results
- Download mark sheets and progress reports
- View homework and assignments
- Monitor academic performance analytics

**Financial:**

- View and pay fee invoices online
- Track payment history
- Download fee receipts
- View pending dues alerts

**Services:**

- Track transport route and timings
- View hostel accommodation details
- Request library book status
- Apply for school certificates

**Communication:**

- Message teachers and administration
- Schedule parent-teacher meetings
- View school notices and events
- Receive SMS/email alerts for attendance, fees, exams

## 📂 Directory Structure

```
attendance/
├── admin/              # Admin panel (70+ pages)
│   ├── academics/      # Subjects, exams, timetable, certificates
│   ├── finance/        # Fees, invoices, expenses, payroll
│   ├── library/        # Books, members, issue/return
│   ├── transport/      # Routes, vehicles, drivers
│   ├── hostel/         # Rooms, mess, visitors
│   ├── hr/             # Staff, attendance, leave, payroll
│   ├── inventory/      # Assets, stock, purchases
│   └── ...             # Core admin pages
├── teacher/            # Teacher panel (30+ pages)
│   ├── academics/      # Lesson plans, exams, grades
│   ├── students/       # Roster, performance, behavior
│   └── ...             # Core teacher pages
├── student/            # Student panel (25+ pages)
│   ├── academics/      # Timetable, exams, results
│   ├── library/        # Books, requests
│   ├── finance/        # Fee invoices, payments
│   └── ...             # Core student pages
├── parent/             # Parent panel (20+ pages)
│   ├── academics/      # Children's progress
│   ├── finance/        # Fee payments
│   └── ...             # Core parent pages
├── api/                # REST API endpoints
│   ├── academics.php   # Academic operations
│   ├── finance.php     # Financial operations
│   ├── library.php     # Library operations
│   ├── lti.php         # LTI 1.3 integration
│   └── ...             # Other API files
├── includes/           # PHP includes & navigation
│   ├── lti.php         # LTI helper functions
│   ├── config.php      # System configuration
│   └── ...             # Other includes
├── database/           # Migrations & schemas
│   ├── lti_schema.sql  # LTI 1.3 database tables
│   └── ...             # Other schema files
├── docs/               # Documentation
│   ├── LMS_INTEGRATION_GUIDE.md
│   ├── IMPLEMENTATION_GUIDE.md
│   └── ...
├── chatbot/            # AI chatbot integration
├── forum/              # Community forum
└── _archive/           # Old docs & scripts
```

## 🛠️ Technology Stack

**Backend:**

- PHP 8.0+ (OOP architecture)
- MySQL 8.0+ (Relational database)
- Composer (Dependency management)
- PDO (Database abstraction)
- PHPMailer (Email handling)
- OpenSSL (JWT encryption for LTI)

**Frontend:**

- HTML5 / CSS3 (Cyberpunk theme with neon effects)
- JavaScript (ES6+)
- Font Awesome 6.4.0
- Chart.js (Analytics visualizations)
- Progressive Web App (PWA)

**LMS Integration:**

- LTI 1.3 Standard (IMS Global)
- OpenID Connect (OIDC)
- OAuth 2.0 (Token management)
- JWT (JSON Web Tokens)
- AGS (Assignment and Grade Services)
- NRPS (Names and Role Provisioning)
- Deep Linking 2.0

**Security:**

- Role-based access control (RBAC)
- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- CSRF tokens on forms
- Session management with secure cookies
- HTTPS enforcement for LTI
- Nonce validation (replay attack prevention)

## 🔐 Security Features

- ✅ Email verification system
- ✅ Admin approval workflow
- ✅ Secure password reset with timed tokens
- ✅ Activity audit logging
- ✅ Session timeout protection
- ✅ Input validation & sanitization
- ✅ JWT signature verification (LTI)
- ✅ Public/private key encryption
- ✅ Rate limiting on API endpoints

## 🎓 LMS Integration Features

### Supported Platforms

- ✅ **Moodle** 3.5+ (Fully tested)
- ✅ **Canvas** (All versions)
- ✅ **Blackboard Learn** 9.1+
- ✅ **Brightspace (D2L)** 10.8+
- ⚠️ **Sakai** 19.0+ (Beta)
- ⚠️ **Open edX** Juniper+ (Beta)

### LTI 1.3 Capabilities

- **Single Sign-On (SSO)**: Authenticate once in LMS, access SAMS automatically
- **Grade Passback**: Attendance syncs to LMS gradebook via AGS
- **Deep Linking**: Embed attendance tools in course pages
- **Course Sync**: Automatic roster and schedule synchronization
- **User Provisioning**: Auto-create users from LMS launches
- **Role Mapping**: LMS roles → SAMS roles (Learner→Student, Instructor→Teacher)

### Integration Endpoints

- `POST /api/lti.php?action=launch` - Handle LTI tool launches
- `POST /api/lti.php?action=grade_passback` - Sync grades to LMS
- `POST /api/lti.php?action=deep_link` - Create embedded resources
- `POST /api/lti.php?action=sync_courses` - Sync course data

## 📱 Progressive Web App

- ✅ Offline functionality
- ✅ Install to home screen
- ✅ Push notifications
- ✅ Background sync
- ✅ Service worker caching
- ✅ Service worker caching
- ✅ App-like experience

## 💬 Chat System

WhatsApp/Telegram-style messaging featuring:

- Real-time conversations
- Message reactions (emoji)
- Typing indicators
- Online/offline status
- File attachments
- Contact management
- Search users by role
- Favorite contacts

## 🎨 UI Themes

**Primary:** Cyberpunk (Futuristic dark with neon accents)

- Animated starfield background
- Holographic card effects
- Neon cyan/purple gradients
- Glassmorphism design

**Sidebar Collapse:**

- Toggle with hamburger button (☰)
- Keyboard shortcut: `Ctrl+B` / `Cmd+B`
- State persists across sessions
- Mobile responsive

## 📊 Database Structure

- **Core:** users, students, teachers, parents, classes
- **Attendance:** attendance_records, biometric_scans
- **Communication:** messages, notifications, forums
- **Academic:** assignments, grades, events
- **System:** audit_logs, email_queue, sessions

See `/database/migrations/` for complete schemas

## 🔧 Configuration

**Database:** `/includes/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'attendance_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

**Email:** Configure SMTP in `config.php`
**PWA:** Edit `manifest.json` for app settings

## 📖 Documentation

**Core Documentation:**

- 📘 [**SETUP_GUIDE.md**](docs/SETUP_GUIDE.md) - Complete installation and deployment guide
- 🎓 [**LMS_INTEGRATION_GUIDE.md**](docs/LMS_INTEGRATION_GUIDE.md) - LTI 1.3 integration walkthrough
- 🚀 [**IMPLEMENTATION_GUIDE.md**](docs/IMPLEMENTATION_GUIDE.md) - Developer implementation checklist
- ✅ [**LMS_IMPLEMENTATION_COMPLETE.md**](docs/LMS_IMPLEMENTATION_COMPLETE.md) - Feature verification report
- 📚 [**Documentation Index**](docs/INDEX.md) - Navigate all documentation

**Project Information:**

- 🎯 [**PROJECT_OVERVIEW.md**](PROJECT_OVERVIEW.md) - Complete system overview
- 📋 [**CHANGELOG.md**](CHANGELOG.md) - Version history and release notes
- 📌 [**QUICK_REFERENCE.md**](QUICK_REFERENCE.md) - Common tasks and shortcuts

**Legal & Community:**

- 📜 [**LICENSE**](LICENSE) - MIT License with Educational Addenda
- 🤝 [**CONTRIBUTING.md**](CONTRIBUTING.md) - Contribution guidelines
- 🔒 [**SECURITY.md**](SECURITY.md) - Security policy and best practices

**Theme & UI:**

- 🎨 [**CYBERPUNK-README.md**](CYBERPUNK-README.md) - UI design guide
- 💬 [**CHAT_SYSTEM_COMPLETE.md**](CHAT_SYSTEM_COMPLETE.md) - Messaging system documentation

**Archived:**

- 📦 `_archive/documentation/` - Historical documentation

## 🚦 System Status

| Component             | Status      | Version |
| --------------------- | ----------- | ------- |
| Core System           | ✅ Complete | 2.1.0   |
| User Management       | ✅ Complete | 2.1.0   |
| Attendance Tracking   | ✅ Complete | 2.1.0   |
| LMS Integration (LTI) | ✅ Complete | 2.1.0   |
| Chat System           | ✅ Complete | 2.0.0   |
| Analytics             | ✅ Complete | 2.0.0   |
| PWA                   | ✅ Complete | 2.0.0   |
| Security              | ✅ Complete | 2.1.0   |
| Documentation         | ✅ Complete | 2.1.0   |
| Legal Framework       | ✅ Complete | 2.1.0   |

## 🆘 Support

**Issues?** Check:

1. `QUICK_REFERENCE.md` - Common solutions
2. `/general/help.php` - Help center
3. `/general/faq.php` - Frequently asked questions
4. AI Chatbot - Bottom-right corner of pages

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details on:

- Code of Conduct
- Development setup
- Coding standards (PSR-12, BEM, ES6+)
- Pull request process
- Testing guidelines

## 📝 License

This project is licensed under the **MIT License with Educational Addenda**.

See [LICENSE](LICENSE) file for full terms including:

- Attribution requirements
- Data privacy compliance (GDPR, FERPA, COPPA)
- Third-party component licenses
- Security disclaimers

**Copyright © 2025 Student Attendance Management System (SAMS)**

## 🎯 Version

**Current:** v2.1.0 (December 2024)

**Version History:**

- **v2.1.0** - LTI 1.3 LMS integration, OpenID Connect SSO, grade passback, deep linking
- **v2.0.0** - Complete feature set, Cyberpunk UI, chat system, sidebar functionality
- **v1.0.0** - Initial release with core attendance tracking

---

**Built with ❤️ for modern educational institutions**
