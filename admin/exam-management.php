<?php

/**
 * VERDANT SMS v3.0 — ADMIN EXAM MANAGEMENT
 * Create exams, manage questions, view results, approve registrations
 * Issue Entrance IDs manually
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_admin('../login.php');

$user = get_user();
$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create_exam':
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $grade = $_POST['grade_level'] ?? '';
            $duration = intval($_POST['duration_minutes'] ?? 60);
            $passMark = intval($_POST['pass_mark'] ?? 50);
            $totalMarks = intval($_POST['total_marks'] ?? 100);

            if (empty($title)) {
                $error = 'Exam title is required.';
            } else {
                db()->insert('entrance_exams', [
                    'title' => $title,
                    'description' => $description,
                    'grade_level' => $grade,
                    'duration_minutes' => $duration,
                    'pass_mark' => $passMark,
                    'total_marks' => $totalMarks,
                    'created_by' => $user['id']
                ]);
                $success = 'Exam created successfully!';
            }
            break;

        case 'add_question':
            $examId = intval($_POST['exam_id'] ?? 0);
            $questionText = trim($_POST['question_text'] ?? '');
            $questionType = $_POST['question_type'] ?? 'mcq';
            $optionA = trim($_POST['option_a'] ?? '');
            $optionB = trim($_POST['option_b'] ?? '');
            $optionC = trim($_POST['option_c'] ?? '');
            $optionD = trim($_POST['option_d'] ?? '');
            $correctAnswer = strtoupper($_POST['correct_answer'] ?? 'A');
            $marks = intval($_POST['marks'] ?? 1);

            if (empty($questionText) || $examId < 1) {
                $error = 'Question text and exam are required.';
            } else {
                db()->insert('exam_questions', [
                    'exam_id' => $examId,
                    'question_text' => $questionText,
                    'question_type' => $questionType,
                    'option_a' => $optionA,
                    'option_b' => $optionB,
                    'option_c' => $optionC,
                    'option_d' => $optionD,
                    'correct_answer' => $correctAnswer,
                    'marks' => $marks
                ]);
                $success = 'Question added successfully!';
            }
            break;

        case 'approve_registration':
            $regId = intval($_POST['registration_id'] ?? 0);
            $examId = intval($_POST['assign_exam_id'] ?? 0);

            if ($regId < 1 || $examId < 1) {
                $error = 'Registration and exam must be selected.';
            } else {
                // Generate access token
                $token = bin2hex(random_bytes(32));

                // Create attempt
                db()->insert('exam_attempts', [
                    'registration_id' => $regId,
                    'exam_id' => $examId,
                    'access_token' => $token,
                    'status' => 'not_started'
                ]);

                // Update registration status
                db()->query("UPDATE exam_registrations SET status = 'exam_sent', exam_link_sent_at = NOW() WHERE id = ?", [$regId]);

                // Get registration details for email
                $reg = db()->fetchOne("SELECT * FROM exam_registrations WHERE id = ?", [$regId]);

                // TODO: Send email with exam link
                // send_email($reg['email'], 'Your Entrance Exam Link', "Click here: " . BASE_URL . "/student/exam.php?token=" . $token);

                $success = 'Registration approved! Exam link: ' . BASE_URL . '/student/exam.php?token=' . $token;
            }
            break;

        case 'decline_registration':
            $regId = intval($_POST['registration_id'] ?? 0);
            if ($regId > 0) {
                db()->query("UPDATE exam_registrations SET status = 'declined' WHERE id = ?", [$regId]);
                $success = 'Registration declined.';
            }
            break;

        case 'manual_entrance_id':
            $regId = intval($_POST['registration_id'] ?? 0);
            $entranceId = trim($_POST['entrance_id'] ?? '');

            if ($regId > 0 && !empty($entranceId)) {
                // Check if attempt exists, if not create one
                $attempt = db()->fetchOne("SELECT id FROM exam_attempts WHERE registration_id = ?", [$regId]);
                if ($attempt) {
                    db()->query("UPDATE exam_attempts SET entrance_id = ?, passed = 1, status = 'graded' WHERE registration_id = ?", [$entranceId, $regId]);
                } else {
                    // Get any active exam
                    $exam = db()->fetchOne("SELECT id FROM entrance_exams WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
                    if ($exam) {
                        db()->insert('exam_attempts', [
                            'registration_id' => $regId,
                            'exam_id' => $exam['id'],
                            'access_token' => bin2hex(random_bytes(32)),
                            'status' => 'graded',
                            'passed' => 1,
                            'entrance_id' => $entranceId,
                            'submitted_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                db()->query("UPDATE exam_registrations SET status = 'exam_completed' WHERE id = ?", [$regId]);
                $success = 'Entrance ID assigned: ' . $entranceId;
            }
            break;

        case 'delete_exam':
            $examId = intval($_POST['exam_id'] ?? 0);
            if ($examId > 0) {
                db()->query("DELETE FROM entrance_exams WHERE id = ?", [$examId]);
                $success = 'Exam deleted.';
            }
            break;

        case 'toggle_exam':
            $examId = intval($_POST['exam_id'] ?? 0);
            if ($examId > 0) {
                db()->query("UPDATE entrance_exams SET is_active = NOT is_active WHERE id = ?", [$examId]);
                $success = 'Exam status toggled.';
            }
            break;
    }
}

// Fetch data
$exams = db()->fetchAll("SELECT e.*, u.full_name as created_by_name,
    (SELECT COUNT(*) FROM exam_questions WHERE exam_id = e.id) as question_count,
    (SELECT COUNT(*) FROM exam_attempts WHERE exam_id = e.id) as attempt_count
    FROM entrance_exams e
    LEFT JOIN users u ON e.created_by = u.id
    ORDER BY e.created_at DESC");

$pendingRegistrations = db()->fetchAll("SELECT * FROM exam_registrations WHERE status = 'pending' ORDER BY created_at DESC");
$allRegistrations = db()->fetchAll("
    SELECT er.*, ea.entrance_id, ea.passed, ea.percentage, ea.status as attempt_status
    FROM exam_registrations er
    LEFT JOIN exam_attempts ea ON er.id = ea.registration_id
    ORDER BY er.created_at DESC
    LIMIT 100
");

// Get questions for selected exam (if viewing)
$viewExamId = intval($_GET['view_exam'] ?? 0);
$examQuestions = [];
if ($viewExamId > 0) {
    $examQuestions = db()->fetchAll("SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY order_num, id", [$viewExamId]);
}

$grades = [
    'Grade 1',
    'Grade 2',
    'Grade 3',
    'Grade 4',
    'Grade 5',
    'Grade 6',
    'Grade 7',
    'Grade 8',
    'Grade 9',
    'Grade 10',
    'Grade 11',
    'Grade 12'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Management — Verdant Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <style>
        .tab-container {
            display: flex;
            gap: 5px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 12px 24px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 10px 10px 0 0;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: var(--card-bg);
            border-color: var(--neon-green);
            color: var(--neon-green);
        }

        .tab-btn .badge {
            background: var(--neon-purple);
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            margin-left: 8px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }

        .stat-card i {
            font-size: 2rem;
            color: var(--neon-green);
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--text-primary);
        }

        .stat-card .label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-card,
        .data-card {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .form-card h3,
        .data-card h3 {
            color: var(--neon-green);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--neon-green);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--neon-green), #00cc6a);
            color: #000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.4);
        }

        .btn-danger {
            background: rgba(255, 71, 87, 0.2);
            border: 1px solid var(--error);
            color: var(--error);
        }

        .btn-danger:hover {
            background: var(--error);
            color: #fff;
        }

        .btn-secondary {
            background: rgba(0, 212, 255, 0.2);
            border: 1px solid var(--neon-blue);
            color: var(--neon-blue);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .data-table th {
            color: var(--neon-green);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .data-table tr:hover {
            background: rgba(0, 255, 136, 0.05);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background: rgba(255, 204, 0, 0.2);
            color: #ffcc00;
        }

        .status-approved {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
        }

        .status-declined {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }

        .status-sent {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }

        .status-completed {
            background: rgba(179, 102, 255, 0.2);
            color: #b366ff;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid rgba(0, 255, 136, 0.4);
            color: var(--neon-green);
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid rgba(255, 71, 87, 0.4);
            color: var(--error);
        }

        .question-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .question-item .q-num {
            color: var(--neon-blue);
            font-weight: bold;
        }

        .question-item .q-text {
            margin: 10px 0;
            font-size: 1.05rem;
        }

        .question-item .options-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }

        .question-item .option {
            padding: 8px 15px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .question-item .option.correct {
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid var(--neon-green);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: var(--neon-green);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .data-table {
                font-size: 0.85rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
            }
        }
    </style>
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="cyber-container">
            <h1 style="color: var(--neon-green); margin-bottom: 10px;">
                <i class="fas fa-clipboard-list"></i> Entrance Exam Management
            </h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Create exams, manage questions, approve registrations, issue Entrance IDs</p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-file-alt"></i>
                    <div class="value"><?= count($exams) ?></div>
                    <div class="label">Total Exams</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-clock"></i>
                    <div class="value"><?= count($pendingRegistrations) ?></div>
                    <div class="label">Pending Registrations</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="value"><?= count($allRegistrations) ?></div>
                    <div class="label">Total Applicants</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-trophy"></i>
                    <div class="value"><?= count(array_filter($allRegistrations, fn($r) => ($r['passed'] ?? 0) == 1)) ?></div>
                    <div class="label">Students Passed</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tab-container">
                <button class="tab-btn active" onclick="showTab('exams')">
                    <i class="fas fa-file-alt"></i> Exams
                </button>
                <button class="tab-btn" onclick="showTab('questions')">
                    <i class="fas fa-question-circle"></i> Add Questions
                </button>
                <button class="tab-btn" onclick="showTab('pending')">
                    <i class="fas fa-user-clock"></i> Pending
                    <?php if (count($pendingRegistrations) > 0): ?>
                        <span class="badge"><?= count($pendingRegistrations) ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab-btn" onclick="showTab('all')">
                    <i class="fas fa-users"></i> All Applicants
                </button>
            </div>

            <!-- Tab: Exams -->
            <div id="tab-exams" class="tab-content active">
                <div class="form-card">
                    <h3><i class="fas fa-plus-circle"></i> Create New Exam</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="create_exam">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Exam Title *</label>
                                <input type="text" name="title" placeholder="e.g., Grade 6 Entrance Exam 2025" required>
                            </div>
                            <div class="form-group">
                                <label>Grade Level</label>
                                <select name="grade_level">
                                    <option value="">All Grades</option>
                                    <?php foreach ($grades as $g): ?>
                                        <option value="<?= $g ?>"><?= $g ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Duration (minutes)</label>
                                <input type="number" name="duration_minutes" value="60" min="10" max="180">
                            </div>
                            <div class="form-group">
                                <label>Pass Mark (%)</label>
                                <input type="number" name="pass_mark" value="50" min="1" max="100">
                            </div>
                            <div class="form-group">
                                <label>Total Marks</label>
                                <input type="number" name="total_marks" value="100" min="1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" placeholder="Exam description..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Exam
                        </button>
                    </form>
                </div>

                <div class="data-card">
                    <h3><i class="fas fa-list"></i> All Exams</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Grade</th>
                                <th>Duration</th>
                                <th>Pass %</th>
                                <th>Questions</th>
                                <th>Attempts</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exam['title']) ?></td>
                                    <td><?= $exam['grade_level'] ?: 'All' ?></td>
                                    <td><?= $exam['duration_minutes'] ?> min</td>
                                    <td><?= $exam['pass_mark'] ?>%</td>
                                    <td><?= $exam['question_count'] ?></td>
                                    <td><?= $exam['attempt_count'] ?></td>
                                    <td>
                                        <span class="status-badge <?= $exam['is_active'] ? 'status-approved' : 'status-declined' ?>">
                                            <?= $exam['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="toggle_exam">
                                            <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <?= $exam['is_active'] ? 'Disable' : 'Enable' ?>
                                            </button>
                                        </form>
                                        <a href="?view_exam=<?= $exam['id'] ?>" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($exams)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; color: var(--text-muted);">No exams created yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($viewExamId > 0 && !empty($examQuestions)): ?>
                    <div class="data-card">
                        <h3><i class="fas fa-question-circle"></i> Questions for Selected Exam</h3>
                        <?php foreach ($examQuestions as $i => $q): ?>
                            <div class="question-item">
                                <span class="q-num">Q<?= $i + 1 ?></span>
                                <span style="float: right; color: var(--text-muted);"><?= $q['marks'] ?> mark(s)</span>
                                <div class="q-text"><?= htmlspecialchars($q['question_text']) ?></div>
                                <div class="options-list">
                                    <div class="option <?= $q['correct_answer'] === 'A' ? 'correct' : '' ?>">A: <?= htmlspecialchars($q['option_a']) ?></div>
                                    <div class="option <?= $q['correct_answer'] === 'B' ? 'correct' : '' ?>">B: <?= htmlspecialchars($q['option_b']) ?></div>
                                    <div class="option <?= $q['correct_answer'] === 'C' ? 'correct' : '' ?>">C: <?= htmlspecialchars($q['option_c']) ?></div>
                                    <div class="option <?= $q['correct_answer'] === 'D' ? 'correct' : '' ?>">D: <?= htmlspecialchars($q['option_d']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab: Add Questions -->
            <div id="tab-questions" class="tab-content">
                <div class="form-card">
                    <h3><i class="fas fa-plus-circle"></i> Add Question to Exam</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_question">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Select Exam *</label>
                                <select name="exam_id" required>
                                    <option value="">Choose Exam</option>
                                    <?php foreach ($exams as $e): ?>
                                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Question Type</label>
                                <select name="question_type">
                                    <option value="mcq">Multiple Choice (MCQ)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Marks</label>
                                <input type="number" name="marks" value="1" min="1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Question Text *</label>
                            <textarea name="question_text" placeholder="Enter the question..." required></textarea>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Option A</label>
                                <input type="text" name="option_a" placeholder="Option A">
                            </div>
                            <div class="form-group">
                                <label>Option B</label>
                                <input type="text" name="option_b" placeholder="Option B">
                            </div>
                            <div class="form-group">
                                <label>Option C</label>
                                <input type="text" name="option_c" placeholder="Option C">
                            </div>
                            <div class="form-group">
                                <label>Option D</label>
                                <input type="text" name="option_d" placeholder="Option D">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <select name="correct_answer">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab: Pending Registrations -->
            <div id="tab-pending" class="tab-content">
                <div class="data-card">
                    <h3><i class="fas fa-user-clock"></i> Pending Exam Registrations</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Grade</th>
                                <th>Parent</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRegistrations as $reg): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reg['full_name']) ?></td>
                                    <td><?= htmlspecialchars($reg['email']) ?></td>
                                    <td><?= htmlspecialchars($reg['phone']) ?></td>
                                    <td><?= $reg['grade_applying_for'] ?: '-' ?></td>
                                    <td><?= htmlspecialchars($reg['parent_name']) ?></td>
                                    <td><?= date('M j, Y', strtotime($reg['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="showApproveModal(<?= $reg['id'] ?>, '<?= htmlspecialchars($reg['full_name']) ?>')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="decline_registration">
                                            <input type="hidden" name="registration_id" value="<?= $reg['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Decline this registration?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pendingRegistrations)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-muted);">No pending registrations</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: All Applicants -->
            <div id="tab-all" class="tab-content">
                <div class="data-card">
                    <h3><i class="fas fa-users"></i> All Exam Applicants</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Entrance ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allRegistrations as $reg): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reg['full_name']) ?></td>
                                    <td><?= htmlspecialchars($reg['email']) ?></td>
                                    <td><?= $reg['grade_applying_for'] ?: '-' ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $reg['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $reg['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= $reg['percentage'] ? $reg['percentage'] . '%' : '-' ?></td>
                                    <td>
                                        <?php if ($reg['entrance_id']): ?>
                                            <code style="color: var(--neon-green);"><?= $reg['entrance_id'] ?></code>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$reg['entrance_id'] && $reg['status'] !== 'pending'): ?>
                                            <button class="btn btn-secondary btn-sm" onclick="showManualIdModal(<?= $reg['id'] ?>, '<?= htmlspecialchars($reg['full_name']) ?>')">
                                                <i class="fas fa-id-card"></i> Issue ID
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Approve Modal -->
    <div class="modal" id="approveModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-check-circle"></i> Approve Registration</h3>
                <button class="modal-close" onclick="closeModal('approveModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="approve_registration">
                <input type="hidden" name="registration_id" id="approveRegId">
                <p style="margin-bottom: 20px;">Approving: <strong id="approveRegName"></strong></p>
                <div class="form-group">
                    <label>Assign Exam *</label>
                    <select name="assign_exam_id" required>
                        <option value="">Select Exam</option>
                        <?php foreach ($exams as $e): ?>
                            <?php if ($e['is_active']): ?>
                                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['title']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p style="color: var(--text-muted); margin-bottom: 20px;">
                    An exam link will be generated and should be sent to the applicant.
                </p>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Approve & Generate Link
                </button>
            </form>
        </div>
    </div>

    <!-- Manual ID Modal -->
    <div class="modal" id="manualIdModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-id-card"></i> Issue Entrance ID</h3>
                <button class="modal-close" onclick="closeModal('manualIdModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="manual_entrance_id">
                <input type="hidden" name="registration_id" id="manualRegId">
                <p style="margin-bottom: 20px;">Issuing ID for: <strong id="manualRegName"></strong></p>
                <div class="form-group">
                    <label>Entrance ID *</label>
                    <input type="text" name="entrance_id" id="manualEntranceId" placeholder="e.g., VERDANT-EXAM-12345" required>
                </div>
                <button type="button" class="btn btn-secondary" onclick="generateRandomId()" style="margin-bottom: 15px;">
                    <i class="fas fa-random"></i> Generate Random
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Issue ID
                </button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');
            event.target.closest('.tab-btn').classList.add('active');
        }

        function showApproveModal(regId, name) {
            document.getElementById('approveRegId').value = regId;
            document.getElementById('approveRegName').textContent = name;
            document.getElementById('approveModal').classList.add('active');
        }

        function showManualIdModal(regId, name) {
            document.getElementById('manualRegId').value = regId;
            document.getElementById('manualRegName').textContent = name;
            generateRandomId();
            document.getElementById('manualIdModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function generateRandomId() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let id = 'VERDANT-EXAM-';
            for (let i = 0; i < 8; i++) {
                id += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('manualEntranceId').value = id;
        }

        // Close modal on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });
    </script>
</body>

</html>