# Google Form Integration Guide

## Verdant SMS v3.0 — Bulk Registration System

This guide explains how to create Google Forms for bulk registration and integrate them with Verdant SMS.

---

## 📋 Form Types Required

### 1. Parent Registration Form

Create a Google Form with these fields:

| Field Name          | Type         | Required | Description              |
| ------------------- | ------------ | -------- | ------------------------ |
| Full Name           | Short answer | Yes      | Parent's full name       |
| Email Address       | Short answer | Yes      | Valid email for login    |
| Phone Number        | Short answer | Yes      | Format: +234XXXXXXXXXX   |
| Relationship        | Dropdown     | Yes      | Mother, Father, Guardian |
| Child 1 - Full Name | Short answer | Yes      | First child's name       |
| Child 1 - Class     | Dropdown     | Yes      | P1-P6, JSS1-3, SSS1-3    |
| Child 2 - Full Name | Short answer | No       | Second child (optional)  |
| Child 2 - Class     | Dropdown     | No       | Second child's class     |
| Child 3 - Full Name | Short answer | No       | Third child (optional)   |
| Child 3 - Class     | Dropdown     | No       | Third child's class      |

**Class Dropdown Options:**

```
Primary 1 (P1)
Primary 2 (P2)
Primary 3 (P3)
Primary 4 (P4)
Primary 5 (P5)
Primary 6 (P6)
JSS 1
JSS 2
JSS 3
SSS 1
SSS 2
SSS 3
```

---

### 2. Staff Registration Form

Create a separate Google Form for staff with these fields:

| Field Name     | Type         | Required | Description                 |
| -------------- | ------------ | -------- | --------------------------- |
| Full Name      | Short answer | Yes      | Staff member's full name    |
| Email Address  | Short answer | Yes      | Valid email for login       |
| Phone Number   | Short answer | Yes      | Format: +234XXXXXXXXXX      |
| Role           | Dropdown     | Yes      | Select from available roles |
| Department     | Short answer | No       | e.g., Mathematics, Science  |
| Qualifications | Paragraph    | No       | Degrees, certifications     |

**Role Dropdown Options:**

```
Teacher
Librarian
Transport Officer
Hostel Warden
Canteen Manager
School Nurse
Counselor
Accountant
Admin Officer
```

---

## 📥 Exporting Form Responses

### Method 1: Google Sheets (Recommended)

1. Open your Google Form
2. Click "Responses" tab
3. Click the Google Sheets icon (Create Spreadsheet)
4. Responses will automatically populate the sheet
5. Copy the spreadsheet URL for the Admin panel

### Method 2: CSV Export

1. Open your Google Form
2. Click "Responses" tab
3. Click the three-dot menu (⋮)
4. Select "Download responses (.csv)"
5. Upload the CSV file in Admin > Bulk Registration

---

## 📊 CSV File Format

### Staff CSV Format

```csv
Full Name,Email,Phone,Role,Department,Qualifications
John Doe,john.doe@email.com,+2348012345678,teacher,Mathematics,B.Ed Mathematics
Jane Smith,jane.smith@email.com,+2348087654321,librarian,,B.Sc Library Science
```

### Parent CSV Format

```csv
Full Name,Email,Phone,Role,Department,Qualifications,Child1 Name,Child1 Class,Child2 Name,Child2 Class,Relationship
Mary Johnson,mary.j@email.com,+2348011111111,parent,,,,David Johnson,JSS 1,Sarah Johnson,P5,Mother
Peter Williams,peter.w@email.com,+2348022222222,parent,,,,Michael Williams,SSS 2,,,Father
```

---

## ⚙️ Admin Panel Setup

### Creating a Registration Window

1. Go to **Admin > Bulk Registration**
2. Click **"New Registration Window"**
3. Fill in:
   - **Name**: e.g., "Parent Registration December 2025"
   - **Google Sheet URL**: (optional) Paste your Google Sheet link
   - **Start Date**: When registration opens
   - **End Date**: When registration closes
   - **Target Roles**: Select which roles this window accepts

### Uploading CSV Data

1. Click **"Upload CSV"** on your registration window
2. Select your CSV file
3. Click **"Upload & Import"**
4. Records will be imported with "pending" status

### Processing Registrations

**Manual Processing:**

1. Click **"Process"** button on the registration window
2. System creates accounts and sends welcome emails

**Automatic Processing (Cron Job):**

- Runs daily at midnight
- Processes all expired registration windows automatically
- Admin receives email notification when complete

---

## 📧 Email Notifications

When accounts are created, users receive:

1. **Welcome Email** containing:

   - Login credentials (email + temporary password)
   - 6-digit OTP for email verification
   - Link to login page
   - Instructions to change password

2. **Email Subject**: "Welcome to Verdant SMS - Your Account Details"

---

## 🔒 Security Notes

- Passwords are randomly generated (12 characters, mixed case, numbers, symbols)
- Users must verify email with OTP before full access
- Users are prompted to change password on first login
- Admin credentials are NEVER bulk-created

---

## 🚫 Roles NOT Available for Bulk Registration

These roles must be created manually by Admin:

- ❌ Admin
- ❌ Principal
- ❌ Vice-Principal
- ❌ Class Teacher
- ❌ Subject Coordinator

---

## 📱 Cron Job Setup

Add to your server's crontab:

```bash
# Run bulk registration processor daily at midnight
0 0 * * * /opt/lampp/bin/php /opt/lampp/htdocs/attendance/cron/process-bulk-registrations.php >> /opt/lampp/htdocs/attendance/logs/cron.log 2>&1
```

To set up:

```bash
crontab -e
```

---

## 🆘 Troubleshooting

### Common Issues

1. **Duplicate Email Error**

   - Email already exists in system
   - Check if user was previously registered

2. **Invalid Email Format**

   - Ensure email follows standard format
   - No spaces or special characters

3. **CSV Upload Fails**

   - Check CSV encoding (UTF-8)
   - Ensure column order matches template
   - Remove any empty rows

4. **Emails Not Sending**
   - Verify SMTP settings in `.env`
   - Check Gmail App Password is correct
   - Review error logs

---

## 📞 Support

- **Email**: christolabiyi35@gmail.com
- **Phone**: +234 816 771 4860
- **WhatsApp**: [Click to Chat](https://wa.me/2348167714860)

---

**Verdant SMS v3.0 — AI-Powered Bulk Registration**
**© 2025 Chrinux-AI**
