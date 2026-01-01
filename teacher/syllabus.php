<?php

/**
 * Syllabus Management - Teacher Module
 * View and manage subject syllabus
 * Verdant SMS v3.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check authentication
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'teacher', 'subject-coordinator'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$page_title = 'Syllabus Management';

// Get subjects
$subjects = db()->fetchAll("SELECT * FROM subjects ORDER BY section, name");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="cyber-container">
            <div class="page-header">
                <h1><i class="fas fa-book"></i> <?php echo $page_title; ?></h1>
                <p class="text-muted">Manage curriculum and syllabus for subjects</p>
            </div>

            <!-- Section Tabs -->
            <div class="section-tabs" style="margin-bottom: 2rem;">
                <button class="tab-btn active" data-section="primary">Primary (P1-P6)</button>
                <button class="tab-btn" data-section="junior_secondary">JSS (1-3)</button>
                <button class="tab-btn" data-section="senior_secondary">SSS (1-3)</button>
            </div>

            <!-- Subjects Grid -->
            <div class="subjects-grid">
                <?php
                $current_section = '';
                foreach ($subjects as $subject):
                    $section_class = $subject['section'] ?? 'all';
                ?>
                    <div class="subject-card" data-section="<?php echo $section_class; ?>">
                        <div class="subject-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($subject['name']); ?></h3>
                        <p class="subject-code"><?php echo htmlspecialchars($subject['code'] ?? ''); ?></p>
                        <p class="subject-meta">
                            <span class="badge <?php echo $subject['category'] ?? 'core'; ?>">
                                <?php echo ucfirst($subject['category'] ?? 'Core'); ?>
                            </span>
                        </p>
                        <div class="subject-actions">
                            <button class="cyber-btn btn-sm" onclick="viewSyllabus(<?php echo $subject['id']; ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <?php if (in_array($user_role, ['admin', 'subject-coordinator'])): ?>
                                <button class="cyber-btn btn-sm btn-secondary" onclick="editSyllabus(<?php echo $subject['id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($subjects)): ?>
                <div class="cyber-card">
                    <div class="card-body text-center" style="padding: 3rem;">
                        <i class="fas fa-book-open fa-3x" style="color: var(--cyber-cyan);"></i>
                        <h3 style="margin-top: 1rem;">No Subjects Found</h3>
                        <p class="text-muted">Subjects need to be configured in the admin panel.</p>
                        <?php if ($user_role === 'admin'): ?>
                            <a href="../admin/academics/subjects.php" class="cyber-btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-plus"></i> Add Subjects
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <style>
        .section-tabs {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--cyber-cyan);
            color: var(--cyber-cyan);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: var(--cyber-cyan);
            color: #000;
        }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .subject-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .subject-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.2);
        }

        .subject-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-pink));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: #000;
        }

        .subject-card h3 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .subject-code {
            color: var(--cyber-cyan);
            font-family: monospace;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .badge.core {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
        }

        .badge.elective {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .subject-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
    </style>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const section = this.dataset.section;
                document.querySelectorAll('.subject-card').forEach(card => {
                    const cardSection = card.dataset.section;
                    card.style.display = (cardSection === section || cardSection === 'all') ? 'block' : 'none';
                });
            });
        });

        function viewSyllabus(id) {
            alert('Syllabus viewer coming soon!');
        }

        function editSyllabus(id) {
            alert('Syllabus editor coming soon!');
        }
    </script>
</body>

</html>