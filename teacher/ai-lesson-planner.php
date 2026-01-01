<?php
/**
 * AI Lesson Planner - Teacher Module
 * Generates NERDC-compliant lesson plans using AI
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';

$teacherName = $_SESSION['full_name'] ?? 'Teacher';
$pageTitle = "AI Lesson Planner";

// Nigerian curriculum subjects
$subjects = [
    'Mathematics', 'English Language', 'Basic Science', 'Basic Technology',
    'Social Studies', 'Civic Education', 'Agricultural Science', 'Home Economics',
    'Physical and Health Education', 'Computer Studies', 'Religious Studies',
    'French', 'Fine Arts', 'Music', 'Yoruba', 'Igbo', 'Hausa'
];

$classes = [
    'Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6',
    'JSS 1', 'JSS 2', 'JSS 3', 'SSS 1', 'SSS 2', 'SSS 3'
];

$generatedPlan = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $class = $_POST['class'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $topic = trim($_POST['topic'] ?? '');
    $duration = intval($_POST['duration'] ?? 40);

    if (empty($topic)) {
        $error = 'Please enter a topic.';
    } else {
        // In production, this would call Grok/AI API
        // For now, generate a structured template
        $generatedPlan = [
            'class' => $class,
            'subject' => $subject,
            'topic' => $topic,
            'duration' => $duration,
            'objectives' => [
                "Students will be able to define and explain " . $topic,
                "Students will identify key concepts related to " . $topic,
                "Students will apply knowledge of " . $topic . " to solve practical problems"
            ],
            'materials' => ['Textbook', 'Whiteboard', 'Marker', 'Charts/Diagrams', 'Projector (optional)'],
            'plan' => [
                ['time' => '5 mins', 'activity' => 'Introduction', 'description' => "Welcome students. Review previous lesson briefly. Introduce today's topic: " . $topic],
                ['time' => '10 mins', 'activity' => 'Explanation', 'description' => "Explain core concepts of " . $topic . " with examples. Use visual aids."],
                ['time' => '15 mins', 'activity' => 'Student Activity', 'description' => "Group work or individual practice. Students solve problems related to " . $topic],
                ['time' => '5 mins', 'activity' => 'Discussion', 'description' => "Class discussion on findings. Address questions and misconceptions."],
                ['time' => '5 mins', 'activity' => 'Summary & Homework', 'description' => "Summarize key points. Assign homework from textbook."]
            ],
            'assessment' => [
                "Oral questions during lesson",
                "Classwork exercises",
                "Take-home assignment"
            ],
            'differentiation' => [
                'gifted' => "Advanced problems for extension",
                'struggling' => "Peer tutoring and simplified examples"
            ]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            padding: 1.5rem;
        }

        .container { max-width: 1000px; margin: 0 auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            color: var(--primary);
        }

        .header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(0,212,255,0.15), rgba(168,85,247,0.15));
            border: 1px solid var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            color: var(--primary);
        }

        .generator-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .generator-card h2 {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .generator-card h2 i {
            color: var(--success);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            background: var(--bg-dark);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            color: var(--text);
            font-size: 0.95rem;
            font-family: inherit;
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-generate {
            background: linear-gradient(135deg, var(--primary), var(--purple));
            color: #fff;
            width: 100%;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        .btn-generate:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background: rgba(255,71,87,0.15);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        /* Generated Plan Styles */
        .plan-output {
            background: var(--bg-card);
            border: 1px solid var(--success);
            border-radius: 20px;
            overflow: hidden;
        }

        .plan-header {
            background: linear-gradient(135deg, rgba(0,255,135,0.1), rgba(0,212,255,0.1));
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .plan-header h2 {
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .plan-header h2 i {
            color: var(--success);
        }

        .plan-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-action {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-pdf {
            background: var(--danger);
            color: #fff;
        }

        .btn-save {
            background: var(--success);
            color: #000;
        }

        .plan-body {
            padding: 2rem;
        }

        .plan-meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .meta-item {
            background: var(--bg-dark);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }

        .meta-item .label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .meta-item .value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--primary);
        }

        .plan-section {
            margin-bottom: 2rem;
        }

        .plan-section:last-child {
            margin-bottom: 0;
        }

        .plan-section h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .plan-section h3 i {
            color: var(--primary);
            font-size: 0.9rem;
        }

        .plan-section ul {
            list-style: none;
        }

        .plan-section ul li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .plan-section ul li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--success);
        }

        .timeline {
            border-left: 2px solid var(--primary);
            padding-left: 1.5rem;
            margin-left: 0.5rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.75rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            background: var(--primary);
            border-radius: 50%;
        }

        .timeline-time {
            display: inline-block;
            background: var(--bg-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .timeline-activity {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .timeline-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .diff-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .diff-item {
            background: var(--bg-dark);
            padding: 1rem;
            border-radius: 10px;
        }

        .diff-item h4 {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            color: var(--warning);
        }

        .diff-item p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .plan-meta { grid-template-columns: repeat(2, 1fr); }
            .diff-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-brain"></i> AI Lesson Planner</h1>
                <p>Generate NERDC-compliant lesson plans in seconds</p>
            </div>
            <div class="ai-badge">
                <i class="fas fa-robot"></i> Powered by AI
            </div>
        </header>

        <!-- Generator Form -->
        <div class="generator-card">
            <h2><i class="fas fa-magic"></i> Create New Lesson Plan</h2>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Class</label>
                        <select name="class" required>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c ?>" <?= (isset($_POST['class']) && $_POST['class'] === $c) ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject" required>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?= $s ?>" <?= (isset($_POST['subject']) && $_POST['subject'] === $s) ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Topic</label>
                        <input type="text" name="topic" placeholder="e.g., Introduction to Fractions" value="<?= htmlspecialchars($_POST['topic'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <select name="duration">
                            <option value="35">35 minutes</option>
                            <option value="40" selected>40 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes (Double Period)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Curriculum Standard</label>
                        <select name="standard">
                            <option value="nerdc">NERDC (Nigeria)</option>
                            <option value="waec">WAEC Aligned</option>
                            <option value="neco">NECO Aligned</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="generate" class="btn btn-generate">
                    <i class="fas fa-magic"></i> Generate Lesson Plan
                </button>
            </form>
        </div>

        <!-- Generated Plan Output -->
        <?php if ($generatedPlan): ?>
        <div class="plan-output">
            <div class="plan-header">
                <h2><i class="fas fa-file-alt"></i> Generated Lesson Plan</h2>
                <div class="plan-actions">
                    <button class="btn-action btn-pdf" onclick="window.print()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="btn-action btn-save">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </div>
            <div class="plan-body">
                <div class="plan-meta">
                    <div class="meta-item">
                        <div class="label">Class</div>
                        <div class="value"><?= htmlspecialchars($generatedPlan['class']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Subject</div>
                        <div class="value"><?= htmlspecialchars($generatedPlan['subject']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Topic</div>
                        <div class="value"><?= htmlspecialchars($generatedPlan['topic']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Duration</div>
                        <div class="value"><?= $generatedPlan['duration'] ?> mins</div>
                    </div>
                </div>

                <div class="plan-section">
                    <h3><i class="fas fa-bullseye"></i> Learning Objectives</h3>
                    <ul>
                        <?php foreach ($generatedPlan['objectives'] as $obj): ?>
                            <li><?= htmlspecialchars($obj) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="plan-section">
                    <h3><i class="fas fa-tools"></i> Materials Needed</h3>
                    <ul>
                        <?php foreach ($generatedPlan['materials'] as $mat): ?>
                            <li><?= htmlspecialchars($mat) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="plan-section">
                    <h3><i class="fas fa-list-ol"></i> Lesson Procedure</h3>
                    <div class="timeline">
                        <?php foreach ($generatedPlan['plan'] as $step): ?>
                            <div class="timeline-item">
                                <div class="timeline-time"><?= htmlspecialchars($step['time']) ?></div>
                                <div class="timeline-activity"><?= htmlspecialchars($step['activity']) ?></div>
                                <div class="timeline-desc"><?= htmlspecialchars($step['description']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="plan-section">
                    <h3><i class="fas fa-clipboard-check"></i> Assessment</h3>
                    <ul>
                        <?php foreach ($generatedPlan['assessment'] as $assess): ?>
                            <li><?= htmlspecialchars($assess) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="plan-section">
                    <h3><i class="fas fa-users"></i> Differentiation</h3>
                    <div class="diff-grid">
                        <div class="diff-item">
                            <h4><i class="fas fa-star"></i> Gifted Students</h4>
                            <p><?= htmlspecialchars($generatedPlan['differentiation']['gifted']) ?></p>
                        </div>
                        <div class="diff-item">
                            <h4><i class="fas fa-hand-holding-heart"></i> Struggling Students</h4>
                            <p><?= htmlspecialchars($generatedPlan['differentiation']['struggling']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
