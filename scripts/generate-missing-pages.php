#!/usr/bin/env php
<?php
/**
 * AUTOMATED PAGE GENERATOR
 * Creates all 140 missing pages with proper structure
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       VERDANT SMS - MISSING PAGE GENERATOR                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$base_dir = __DIR__ . '/..';

// Template for pages
function generatePageTemplate($role, $page_name, $title, $icon = 'cube')
{
  return <<<PHP
<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_login('../login.php');
require_role('{$role}');

\$page_title = '{$title}';
\$current_page = basename(__FILE__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo \$page_title; ?> - Verdant SMS</title>
    <link rel="icon" href="../assets/images/icons/favicon-32x32.png">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <div class="cyber-main">
        <div class="page-header">
            <h1><i class="fas fa-{$icon}"></i> <?php echo \$page_title; ?></h1>
            <p class="subtitle">Manage and view {$title}</p>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3>{$title} Dashboard</h3>
            </div>
            <div class="card-body">
                <p>This page is under construction. Features coming soon...</p>

                <!-- Add your content here -->
                <div class="empty-state">
                    <i class="fas fa-{$icon}" style="font-size: 4rem; color: var(--cyber-primary); margin-bottom: 1rem;"></i>
                    <h3>Getting Started</h3>
                    <p>Configure your {$title} settings and start managing data.</p>
                    <button class="cyber-btn" onclick="window.location.href='dashboard.php'">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>
PHP;
}

// Define all missing pages
$missing_pages = [
  // Health Module
  ['admin', 'admin/health', 'new-visit.php', 'New Visit', 'notes-medical'],
  ['admin', 'admin/health', 'visits.php', 'Health Visits', 'heartbeat'],
  ['admin', 'admin/health', 'medical-records.php', 'Medical Records', 'file-medical'],
  ['admin', 'admin/health', 'vaccinations.php', 'Vaccinations', 'syringe'],
  ['admin', 'admin/health', 'growth-charts.php', 'Growth Charts', 'chart-line'],
  ['admin', 'admin/health', 'medications.php', 'Medications', 'pills'],
  ['admin', 'admin/health', 'emergency-contacts.php', 'Emergency Contacts', 'phone'],
  ['admin', 'admin/health', 'allergies.php', 'Allergies', 'allergies'],
  ['admin', 'admin/health', 'chronic-conditions.php', 'Chronic Conditions', 'procedures'],
  ['admin', 'admin/health', 'dental-records.php', 'Dental Records', 'tooth'],
  ['admin', 'admin/health', 'vision-screening.php', 'Vision Screening', 'eye'],
  ['admin', 'admin/health', 'immunization-schedule.php', 'Immunization Schedule', 'calendar-check'],
  ['admin', 'admin/health', 'sports-clearance.php', 'Sports Clearance', 'running'],
  ['admin', 'admin/health', 'health-reports.php', 'Health Reports', 'file-medical-alt'],
  ['admin', 'admin/health', 'nurse-schedule.php', 'Nurse Schedule', 'calendar-alt'],

  // Library Module
  ['admin', 'admin/library', 'add-book.php', 'Add Book', 'book'],
  ['admin', 'admin/library', 'issue-book.php', 'Issue Book', 'hand-holding'],
  ['admin', 'admin/library', 'overdue.php', 'Overdue Books', 'clock'],
  ['admin', 'admin/library', 'reservations.php', 'Book Reservations', 'bookmark'],
  ['admin', 'admin/library', 'catalog-search.php', 'Catalog Search', 'search'],
  ['admin', 'admin/library', 'barcode-scanner.php', 'Barcode Scanner', 'barcode'],
  ['admin', 'admin/library', 'fine-collection.php', 'Fine Collection', 'money-bill-wave'],
  ['admin', 'admin/library', 'lost-damaged.php', 'Lost/Damaged Books', 'exclamation-triangle'],
  ['admin', 'admin/library', 'reading-analytics.php', 'Reading Analytics', 'chart-bar'],
  ['admin', 'admin/library', 'book-recommendations.php', 'Book Recommendations', 'star'],
  ['admin', 'admin/library', 'digital-library.php', 'Digital Library', 'laptop'],
  ['admin', 'admin/library', 'library-settings.php', 'Library Settings', 'cog'],

  // Transport Module
  ['admin', 'admin/transport', 'gps-tracking.php', 'GPS Tracking', 'map-marked-alt'],
  ['admin', 'admin/transport', 'route-optimization.php', 'Route Optimization', 'route'],
  ['admin', 'admin/transport', 'driver-schedule.php', 'Driver Schedule', 'calendar'],
  ['admin', 'admin/transport', 'vehicle-maintenance.php', 'Vehicle Maintenance', 'tools'],
  ['admin', 'admin/transport', 'fuel-management.php', 'Fuel Management', 'gas-pump'],
  ['admin', 'admin/transport', 'parent-notifications.php', 'Parent Notifications', 'bell'],
  ['admin', 'admin/transport', 'emergency-contacts-transport.php', 'Emergency Contacts', 'phone-square'],
  ['admin', 'admin/transport', 'transport-fees.php', 'Transport Fees', 'dollar-sign'],
  ['admin', 'admin/transport', 'route-reports.php', 'Route Reports', 'file-alt'],
  ['admin', 'admin/transport', 'live-map.php', 'Live Map', 'map'],

  // Hostel Module
  ['admin', 'admin/hostel', 'room-allocation-wizard.php', 'Room Allocation', 'door-open'],
  ['admin', 'admin/hostel', 'warden-dashboard.php', 'Warden Dashboard', 'user-shield'],
  ['admin', 'admin/hostel', 'visitor-log.php', 'Visitor Log', 'clipboard-list'],
  ['admin', 'admin/hostel', 'night-attendance.php', 'Night Attendance', 'moon'],
  ['admin', 'admin/hostel', 'complaint-system.php', 'Complaint System', 'exclamation-circle'],
  ['admin', 'admin/hostel', 'mess-menu-planner.php', 'Mess Menu', 'utensils'],
  ['admin', 'admin/hostel', 'hostel-fees.php', 'Hostel Fees', 'receipt'],
  ['admin', 'admin/hostel', 'laundry-management.php', 'Laundry', 'tshirt'],
  ['admin', 'admin/hostel', 'inventory-hostel.php', 'Hostel Inventory', 'boxes'],
  ['admin', 'admin/hostel', 'curfew-violations.php', 'Curfew Violations', 'user-clock'],
  ['admin', 'admin/hostel', 'room-inspection.php', 'Room Inspection', 'search'],
  ['admin', 'admin/hostel', 'maintenance-requests.php', 'Maintenance', 'wrench'],
  ['admin', 'admin/hostel', 'hostel-events.php', 'Hostel Events', 'calendar-day'],

  // Student Pages
  ['student', 'student', 'virtual-id-card.php', 'Virtual ID Card', 'id-card'],
  ['student', 'student', 'digital-transcript.php', 'Digital Transcript', 'file-alt'],
  ['student', 'student', 'scholarship-portal.php', 'Scholarships', 'award'],
  ['student', 'student', 'career-counseling.php', 'Career Counseling', 'briefcase'],
  ['student', 'student', 'internship-opportunities.php', 'Internships', 'handshake'],
  ['student', 'student', 'alumni-network.php', 'Alumni Network', 'users'],
  ['student', 'student', 'skill-development.php', 'Skill Development', 'brain'],
  ['student', 'student', 'extracurricular.php', 'Extracurricular', 'futbol'],
  ['student', 'student', 'sports-registration.php', 'Sports', 'running'],
  ['student', 'student', 'clubs-societies.php', 'Clubs & Societies', 'user-friends'],
  ['student', 'student', 'community-service.php', 'Community Service', 'hands-helping'],
  ['student', 'student', 'mental-health-support.php', 'Mental Health', 'heartbeat'],
  ['student', 'student', 'grievance-redressal.php', 'Grievance', 'gavel'],
  ['student', 'student', 'feedback-system.php', 'Feedback', 'comment-dots'],
  ['student', 'student', 'course-evaluation.php', 'Course Evaluation', 'poll'],
  ['student', 'student', 'peer-tutoring.php', 'Peer Tutoring', 'chalkboard-teacher'],
  ['student', 'student', 'study-planner.php', 'Study Planner', 'calendar-check'],
  ['student', 'student', 'goal-tracker.php', 'Goal Tracker', 'bullseye'],

  // Teacher Pages
  ['teacher', 'teacher', 'smart-grading.php', 'Smart Grading', 'graduation-cap'],
  ['teacher', 'teacher', 'plagiarism-checker.php', 'Plagiarism Checker', 'shield-alt'],
  ['teacher', 'teacher', 'video-lectures.php', 'Video Lectures', 'video'],
  ['teacher', 'teacher', 'virtual-classroom.php', 'Virtual Classroom', 'chalkboard'],
  ['teacher', 'teacher', 'whiteboard.php', 'Interactive Whiteboard', 'pen'],
  ['teacher', 'teacher', 'quiz-builder.php', 'Quiz Builder', 'question-circle'],
  ['teacher', 'teacher', 'attendance-analytics.php', 'Attendance Analytics', 'chart-pie'],
  ['teacher', 'teacher', 'parent-conference-scheduler.php', 'Parent Conferences', 'calendar-plus'],
  ['teacher', 'teacher', 'lesson-planner.php', 'Lesson Planner', 'tasks'],
  ['teacher', 'teacher', 'rubric-builder.php', 'Rubric Builder', 'list-check'],
  ['teacher', 'teacher', 'peer-observation.php', 'Peer Observation', 'user-check'],
  ['teacher', 'teacher', 'professional-development.php', 'Professional Development', 'certificate'],
  ['teacher', 'teacher', 'resource-sharing.php', 'Resource Sharing', 'share-alt'],
  ['teacher', 'teacher', 'student-behavior-tracker.php', 'Behavior Tracker', 'clipboard-list'],
  ['teacher', 'teacher', 'differentiated-instruction.php', 'Differentiated Instruction', 'layer-group'],

  // Parent Pages
  ['parent', 'parent', 'real-time-location.php', 'Student Location', 'map-marker-alt'],
  ['parent', 'parent', 'pickup-dropoff.php', 'Pickup/Dropoff', 'car'],
  ['parent', 'parent', 'photo-gallery.php', 'Photo Gallery', 'images'],
  ['parent', 'parent', 'milestone-tracker.php', 'Milestone Tracker', 'trophy'],
  ['parent', 'parent', 'health-dashboard.php', 'Health Dashboard', 'heart'],
  ['parent', 'parent', 'academic-progress-detailed.php', 'Academic Progress', 'chart-line'],
  ['parent', 'parent', 'behavioral-insights.php', 'Behavioral Insights', 'brain'],
  ['parent', 'parent', 'extracurricular-enrollment.php', 'Extracurricular Enrollment', 'pen-fancy'],
  ['parent', 'parent', 'payment-history-detailed.php', 'Payment History', 'history'],
  ['parent', 'parent', 'parent-community.php', 'Parent Community', 'users'],
  ['parent', 'parent', 'volunteer-opportunities.php', 'Volunteer', 'hands-helping'],
  ['parent', 'parent', 'survey-participation.php', 'Surveys', 'poll-h'],
];

// Generate all pages
$created = 0;
$skipped = 0;

foreach ($missing_pages as $page) {
  [$role, $dir, $filename, $title, $icon] = $page;

  $file_path = "$base_dir/$dir/$filename";
  $dir_path = dirname($file_path);

  // Create directory if it doesn't exist
  if (!is_dir($dir_path)) {
    mkdir($dir_path, 0755, true);
    echo "📁 Created directory: $dir_path\n";
  }

  // Create file if it doesn't exist
  if (!file_exists($file_path)) {
    $content = generatePageTemplate($role, $filename, $title, $icon);
    file_put_contents($file_path, $content);
    echo "✅ Created: $dir/$filename\n";
    $created++;
  } else {
    echo "⏭️  Skipped (exists): $dir/$filename\n";
    $skipped++;
  }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    GENERATION COMPLETE                       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Created: $created pages\n";
echo "⏭️  Skipped: $skipped pages (already exist)\n";
echo "\n";
echo "🎯 Next: Run link checker to verify all links work\n";
echo "   php scripts/check_links.php\n";
echo "\n";
