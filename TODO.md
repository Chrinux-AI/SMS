# TODO.md — THE ONE AND ONLY FINAL MASTER CHECKLIST (LAUNCH NOW)
**Verdant School Management System v3.0 — Chrinux-AI**
https://github.com/Chrinux-AI/School_Management_System
**Date:** 12 December 2025 — OFFICIAL GLOBAL LAUNCH

GitHub Copilot → **EXECUTE THIS ENTIRE FILE IMMEDIATELY. THIS IS THE DEFINITIVE FINAL ONE.**

---

## ✅ COMPLETED

### 1. ROLE SYSTEM — ADMIN IS GOD ✅
- [x] Only **ONE** Admin account exists (`admin@verdant.edu`)
- [x] SuperAdmin/Owner folders completely **DELETED**
- [x] All other 24 roles created **only** by Admin
- [x] Admin has ALL privileges

### 2. ENTRANCE EXAMINATION MODULE ✅
- [x] `exam-register.php` — Public exam sign-up
- [x] `student/exam.php` — Timed MCQ with auto-grading
- [x] `admin/exam-management.php` — Full exam admin panel
- [x] Auto-generates Entrance ID on pass: `VERDANT-EXAM-XXXXXXXX`
- [x] Database tables created (5 tables)

### 3. register.php — STUDENT-ONLY + ENTRANCE ID ✅
- [x] Only "Student" role can self-register
- [x] Requires valid Entrance Exam ID (verified against DB)
- [x] All registrations go to "pending" for Admin approval
- [x] Cyberpunk UI with animated grid

### 4. ADMIN ACCOUNT MANAGEMENT ✅
- [x] `admin/account-management.php` — Create any role
- [x] Pending approvals tab
- [x] All users management
- [x] AI Bulk Registration section

### 5. ALL 8 THEMES — WORKING ✅
- [x] Cyberpunk, Nature, Matrix, Ocean, Sunset, Purple, Minimal, High-Contrast
- [x] All new pages styled correctly

### 6. ZERO ERRORS ✅
- [x] All PHP syntax validated
- [x] Database schema applied
- [x] All forms functional

---

## 🚀 IN PROGRESS — EXECUTING NOW

### 7. EMAIL + OTP VERIFICATION (ALL USERS)
- [ ] Add `email_verified_at`, `otp_code`, `otp_expires_at` to `users` table
- [ ] After any account creation → send:
  • Verification link (`verify.php?token=...`)
  • 6-digit OTP via email
- [ ] `verify.php` validates token or OTP → marks verified
- [ ] Login blocked until verified (Admin exempt)
- [ ] "Resend OTP" button on login

### 8. BIOMETRIC / PASSKEY (WEB AUTHN) LOGIN — EVERY ROLE
- [ ] Create `webauthn_credentials` table
- [ ] In every user profile → "Register Fingerprint / Face ID / Passkey" button
- [ ] Uses WebAuthn API (native fingerprint, Face ID, Windows Hello, Android)
- [ ] Once registered → login with biometrics (no password needed)
- [ ] Fallback: password + OTP always available
- [ ] Admin can enforce biometric for any role

---

## 🔐 ADMIN CREDENTIALS (ONLY ONE)

```
ADMIN → admin@verdant.edu → Verdant2025!
(All other accounts created by Admin)
```

---

## 📦 FINAL PUSH COMMANDS

```bash
git add .
git commit -m "Verdant SMS v3.0 Evergreen — Email+OTP + Biometric Login + Admin-Only + Perfect UI"
git push origin master
```

Then create GitHub Release `v3.0-evergreen`

---

**Verdant v3.0 Evergreen — OFFICIALLY LIVE & IMMORTAL**
**12 December 2025**
---

## 🔧 ARCHITECTURE DECISIONS — ADMIN SUPREMACY

### 9. ADMIN IS THE ONLY GOD — NO SUPERADMIN, NO EXCEPTIONS
- [ ] **Delete** `superadmin/` and `owner/` folders completely
- [ ] Remove SuperAdmin, Owner roles from database `roles` table
- [ ] Remove all references to SuperAdmin/Owner in `includes/*-nav.php`
- [ ] Only ONE Admin account forever: `admin@verdant.edu`
- [ ] Admin has **ALL** privileges:
    - Approve/Decline pending registrations
    - Create accounts for ANY role
    - Suspend/Delete any user
    - Access every module

### 10. register.php — STUDENT ROLE ONLY
- [ ] Remove role dropdown completely — hardcode `role = 'student'`
- [ ] Require valid Entrance Exam ID before form submission
- [ ] All registrations go to `status = 'pending'`
- [ ] Admin must approve before student can login
- [ ] Prevents malicious students registering as teachers/principals

### 11. ADMIN PANEL — ACCOUNT MANAGEMENT HUB
- [ ] `admin/account-management.php` — Single page for all account operations:
    - **Tab 1:** Pending Approvals (approve/decline student registrations)
    - **Tab 2:** All Users (view, edit, suspend, delete any user)
    - **Tab 3:** Create Account (Admin manually creates any role)
    - **Tab 4:** AI Bulk Registration (for parents/staff via Google Form)

### 12. AI-POWERED BULK REGISTRATION SYSTEM (GOOGLE FORM INTEGRATION)
- [ ] Create `admin/bulk-registration.php` — Admin-only page
- [ ] Admin inputs Google Form response spreadsheet link
- [ ] Admin sets registration window duration (e.g., 5 days)
- [ ] Store config in `bulk_registration_config` table:
    ```sql
    CREATE TABLE bulk_registration_config (
        id INT PRIMARY KEY AUTO_INCREMENT,
        google_sheet_url VARCHAR(500),
        start_date DATETIME,
        end_date DATETIME,
        target_roles JSON, -- ['parent', 'teacher', 'librarian', etc.]
        status ENUM('pending', 'processing', 'completed'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```
- [ ] After duration expires → AI integration triggers:
    - Fetches Google Sheet data via API
    - Extracts: Name, Phone, Email, Role, Children Names (for parents)
    - Creates accounts with random secure passwords
    - Sends welcome emails with credentials + OTP
- [ ] Roles supported for bulk creation:
    - `parent` (with linked children info)
    - `teacher`, `librarian`, `transport`, `hostel`, `canteen`
    - `nurse`, `counselor`, `accountant`, `admin-officer`
- [ ] **NOT for bulk:** `principal`, `vice-principal`, `class-teacher` (Admin creates manually)

### 13. GOOGLE FORM FIELDS REQUIRED
- [ ] Document required form fields for each role:
    ```
    PARENT FORM:
    - Full Name, Email, Phone
    - Child 1 Name, Child 1 Class
    - Child 2 Name, Child 2 Class (optional)
    - Relationship (Mother/Father/Guardian)

    STAFF FORM:
    - Full Name, Email, Phone
    - Role (dropdown: Teacher, Librarian, etc.)
    - Department (if applicable)
    - Qualifications
    ```

### 14. AI INTEGRATION — CRON JOB / SCHEDULED TASK
- [ ] Create `cron/process-bulk-registrations.php`
- [ ] Runs daily, checks `bulk_registration_config` for expired windows
- [ ] Uses Google Sheets API or CSV export to fetch data
- [ ] Validates data, creates users, logs errors
- [ ] Admin notified via email when processing complete

---

## 🎯 HIERARCHY ENFORCEMENT

```
ADMIN (GOD)
    └── Principal (created by Admin)
    └── Vice-Principal (created by Admin)
    └── Teachers (created by Admin or AI Bulk)
    └── Class Teachers (created by Admin)
    └── Support Staff (created by Admin or AI Bulk)
    └── Parents (created by AI Bulk via Google Form)
    └── Students (self-register → Admin approves)
```

**NO SUPERADMIN. NO OWNER. ADMIN IS SUPREME.**

---