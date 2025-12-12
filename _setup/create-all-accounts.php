<?php

/**
 * Verdant SMS - Create All 25 Role Accounts
 * Run once to set up demo accounts for all roles
 * @version 3.0-evergreen
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "  VERDANT SMS - 25 ROLE ACCOUNTS SETUP\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// All 25 roles with their accounts
$accounts = [
    // Leadership (Password: Verdant2025!)
    ['superadmin@verdant.edu', 'Verdant2025!', 'Super', 'Admin', 'superadmin'],
    ['owner@verdant.edu', 'Verdant2025!', 'School', 'Owner', 'owner'],
    ['principal@verdant.edu', 'Verdant2025!', 'John', 'Principal', 'principal'],
    ['vice-principal@verdant.edu', 'Verdant2025!', 'Jane', 'Vice-Principal', 'vice-principal'],

    // Administration
    ['admin@verdant.edu', 'Verdant2025!', 'System', 'Admin', 'admin'],
    ['admin-officer@verdant.edu', 'officer123', 'Admin', 'Officer', 'admin-officer'],
    ['accountant@verdant.edu', 'accountant123', 'Finance', 'Manager', 'accountant'],

    // Academic
    ['teacher@verdant.edu', 'teacher123', 'John', 'Teacher', 'teacher'],
    ['class-teacher@verdant.edu', 'classteacher123', 'Class', 'Teacher', 'class-teacher'],
    ['subject-coordinator@verdant.edu', 'coordinator123', 'Subject', 'Coordinator', 'subject-coordinator'],

    // Support Services
    ['librarian@verdant.edu', 'librarian123', 'Library', 'Manager', 'librarian'],
    ['counselor@verdant.edu', 'counselor123', 'School', 'Counselor', 'counselor'],
    ['nurse@verdant.edu', 'nurse123', 'School', 'Nurse', 'nurse'],

    // Facilities
    ['transport@verdant.edu', 'transport123', 'Transport', 'Manager', 'transport'],
    ['hostel@verdant.edu', 'hostel123', 'Hostel', 'Warden', 'hostel'],
    ['canteen@verdant.edu', 'canteen123', 'Canteen', 'Manager', 'canteen'],

    // Users
    ['student@verdant.edu', 'student123', 'Demo', 'Student', 'student'],
    ['parent@verdant.edu', 'parent123', 'Demo', 'Parent', 'parent'],
    ['alumni@verdant.edu', 'alumni123', 'Demo', 'Alumni', 'alumni'],
    ['general@verdant.edu', 'general123', 'General', 'User', 'general'],

    // Additional demo accounts
    ['demo.teacher@verdant.edu', 'teacher123', 'Demo', 'Teacher', 'teacher'],
    ['demo.student@verdant.edu', 'student123', 'Test', 'Student', 'student'],
    ['demo.parent@verdant.edu', 'parent123', 'Test', 'Parent', 'parent'],
    ['test.admin@verdant.edu', 'Verdant2025!', 'Test', 'Admin', 'admin'],
    ['support@verdant.edu', 'support123', 'Support', 'Staff', 'admin-officer'],
];

$created = 0;
$updated = 0;
$errors = 0;

foreach ($accounts as $account) {
    list($email, $password, $first_name, $last_name, $role) = $account;

    try {
        // Check if user exists
        $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        if ($existing) {
            // Update existing user
            $result = db()->update('users', [
                'password_hash' => $password_hash,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'full_name' => $first_name . ' ' . $last_name,
                'role' => $role,
                'status' => 'active',
                'email_verified' => 1,
                'approved' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$existing['id']]);

            if ($result) {
                echo "✓ UPDATED: {$email} ({$role})\n";
                $updated++;
            }
        } else {
            // Create new user
            $user_id = db()->insert('users', [
                'email' => $email,
                'password_hash' => $password_hash,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'full_name' => $first_name . ' ' . $last_name,
                'role' => $role,
                'status' => 'active',
                'email_verified' => 1,
                'approved' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if ($user_id) {
                echo "✓ CREATED: {$email} ({$role}) [ID: {$user_id}]\n";
                $created++;

                // Create role-specific records
                if ($role === 'student') {
                    db()->insert('students', [
                        'user_id' => $user_id,
                        'student_id' => 'STU-' . str_pad($user_id, 5, '0', STR_PAD_LEFT),
                        'grade_level' => '10',
                        'enrollment_date' => date('Y-m-d'),
                        'status' => 'active'
                    ]);
                } elseif ($role === 'teacher') {
                    db()->insert('teachers', [
                        'user_id' => $user_id,
                        'employee_id' => 'TCH-' . str_pad($user_id, 5, '0', STR_PAD_LEFT),
                        'department' => 'General',
                        'join_date' => date('Y-m-d'),
                        'status' => 'active'
                    ]);
                }
            }
        }
    } catch (Exception $e) {
        echo "✗ ERROR: {$email} - " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "  SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  Created: {$created}\n";
echo "  Updated: {$updated}\n";
echo "  Errors:  {$errors}\n";
echo "  Total:   " . ($created + $updated) . "/25 accounts ready\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "🔑 KEY ACCOUNTS:\n";
echo "───────────────────────────────────────────────────────────────\n";
echo "  superadmin@verdant.edu    → Verdant2025!   [Full Access]\n";
echo "  admin@verdant.edu         → Verdant2025!   [Admin Access]\n";
echo "  teacher@verdant.edu       → teacher123     [Teacher Access]\n";
echo "  student@verdant.edu       → student123     [Student Access]\n";
echo "  parent@verdant.edu        → parent123      [Parent Access]\n";
echo "───────────────────────────────────────────────────────────────\n";
echo "\n✅ Login at: http://localhost/attendance/login.php\n\n";
