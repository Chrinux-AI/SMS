<?php
/**
 * Verdant SMS - Create All Role Accounts
 * Updated credentials per VERDANT-LOGIN-CREDENTIALS.md
 * @version 3.0-evergreen
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "  VERDANT SMS - ROLE ACCOUNTS SETUP\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$accounts = [
    // ADMIN (Supreme - Only One)
    ['admin@verdant.edu', 'Verdant2025!', 'System', 'Admin', 'admin'],
    
    // Leadership
    ['principal@verdant.edu', 'Verdant2025!', 'John', 'Principal', 'principal'],
    ['viceprincipal@verdant.edu', 'Verdant2025!', 'Jane', 'Vice-Principal', 'vice-principal'],
    
    // Academic Staff
    ['teacher@verdant.edu', 'teacher123', 'John', 'Teacher', 'teacher'],
    ['classteacher@verdant.edu', 'teacher123', 'Class', 'Teacher', 'class-teacher'],
    ['subjectcoord@verdant.edu', 'Verdant2025!', 'Subject', 'Coordinator', 'subject-coordinator'],
    
    // Finance & Admin
    ['accountant@verdant.edu', 'Verdant2025!', 'Finance', 'Manager', 'accountant'],
    ['adminofficer@verdant.edu', 'Verdant2025!', 'Admin', 'Officer', 'admin-officer'],
    
    // Support Services
    ['librarian@verdant.edu', 'Verdant2025!', 'Library', 'Manager', 'librarian'],
    ['counselor@verdant.edu', 'Verdant2025!', 'School', 'Counselor', 'counselor'],
    ['nurse@verdant.edu', 'Verdant2025!', 'School', 'Nurse', 'nurse'],
    
    // Facilities
    ['transport@verdant.edu', 'Verdant2025!', 'Transport', 'Manager', 'transport'],
    ['hostel@verdant.edu', 'Verdant2025!', 'Hostel', 'Warden', 'hostel'],
    ['canteen@verdant.edu', 'Verdant2025!', 'Canteen', 'Manager', 'canteen'],
    ['general@verdant.edu', 'Verdant2025!', 'General', 'Staff', 'general'],
    
    // Users
    ['student@verdant.edu', 'student123', 'Demo', 'Student', 'student'],
    ['parent@verdant.edu', 'parent123', 'Demo', 'Parent', 'parent'],
    ['alumni@verdant.edu', 'alumni123', 'Demo', 'Alumni', 'alumni'],
    
    // Additional Staff
    ['itspecialist@verdant.edu', 'Verdant2025!', 'IT', 'Specialist', 'general'],
    ['curriculum@verdant.edu', 'Verdant2025!', 'Curriculum', 'Coordinator', 'general'],
    ['eventcoord@verdant.edu', 'Verdant2025!', 'Event', 'Coordinator', 'general'],
    ['security@verdant.edu', 'Verdant2025!', 'Security', 'Staff', 'general'],
    ['vendor@verdant.edu', 'vendor123', 'Demo', 'Vendor', 'general'],
];

$created = 0;
$updated = 0;

foreach ($accounts as $account) {
    list($email, $password, $first_name, $last_name, $role) = $account;
    
    try {
        $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($existing) {
            db()->update('users', [
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
            echo "✓ UPDATED: {$email} ({$role})\n";
            $updated++;
        } else {
            db()->insert('users', [
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
            echo "✓ CREATED: {$email} ({$role})\n";
            $created++;
        }
    } catch (Exception $e) {
        echo "✗ ERROR: {$email} - " . $e->getMessage() . "\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "  Created: {$created} | Updated: {$updated} | Total: " . ($created + $updated) . "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n🔐 Admin: admin@verdant.edu / Verdant2025!\n";
echo "✅ Login: http://localhost/attendance/login.php\n\n";
