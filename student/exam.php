<?php

/**
 * VERDANT SMS v3.0 — ONLINE ENTRANCE EXAMINATION
 * Timed MCQ exam with auto-grading
 * Access via unique token sent to approved candidates
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Get access token from URL
$token = $_GET['token'] ?? '';
$error = '';
$exam = null;
$attempt = null;
$questions = [];
$submitted = false;
$result = null;

if (empty($token)) {
    $error = 'Invalid exam access link. Please use the link sent to your email.';
} else {
    // Verify token and get attempt
    $attempt = db()->fetchOne("
        SELECT ea.*, er.full_name, er.email, ee.title as exam_title,
               ee.duration_minutes, ee.pass_mark, ee.total_marks, ee.id as exam_id
        FROM exam_attempts ea
        JOIN exam_registrations er ON ea.registration_id = er.id
        JOIN entrance_exams ee ON ea.exam_id = ee.id
        WHERE ea.access_token = ?
    ", [$token]);

    if (!$attempt) {
        $error = 'Invalid or expired exam link.';
    } elseif ($attempt['status'] === 'submitted' || $attempt['status'] === 'graded') {
        $submitted = true;
        $result = $attempt;
    } else {
        $exam = $attempt;

        // Start exam if not started
        if ($attempt['status'] === 'not_started') {
            db()->query("UPDATE exam_attempts SET status = 'in_progress', started_at = NOW() WHERE id = ?", [$attempt['id']]);
            $exam['started_at'] = date('Y-m-d H:i:s');
        }

        // Get questions
        $questions = db()->fetchAll("
            SELECT * FROM exam_questions
            WHERE exam_id = ?
            ORDER BY order_num, id
        ", [$attempt['exam_id']]);

        // Calculate remaining time
        $startTime = strtotime($exam['started_at'] ?? 'now');
        $duration = $exam['duration_minutes'] * 60;
        $elapsed = time() - $startTime;
        $remaining = max(0, $duration - $elapsed);

        // Auto-submit if time expired
        if ($remaining <= 0 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $submitted = true;
            $result = gradeExam($attempt['id'], $questions);
        }
    }
}

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $exam && !$submitted) {
    $result = gradeExam($attempt['id'], $questions, $_POST);
    $submitted = true;
}

/**
 * Grade the exam and generate Entrance ID if passed
 */
function gradeExam($attemptId, $questions, $answers = [])
{
    $score = 0;
    $totalMarks = 0;

    foreach ($questions as $q) {
        $totalMarks += $q['marks'];
        $selected = $answers['q_' . $q['id']] ?? '';
        $isCorrect = ($q['question_type'] === 'mcq' && strtoupper($selected) === strtoupper($q['correct_answer']));

        if ($isCorrect) {
            $score += $q['marks'];
        }

        // Save answer
        db()->insert('exam_answers', [
            'attempt_id' => $attemptId,
            'question_id' => $q['id'],
            'selected_answer' => $selected,
            'is_correct' => $isCorrect ? 1 : 0,
            'marks_awarded' => $isCorrect ? $q['marks'] : 0
        ]);
    }

    $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;

    // Get pass mark
    $attempt = db()->fetchOne("SELECT ea.*, ee.pass_mark FROM exam_attempts ea JOIN entrance_exams ee ON ea.exam_id = ee.id WHERE ea.id = ?", [$attemptId]);
    $passed = $percentage >= $attempt['pass_mark'];

    // Generate Entrance ID if passed
    $entranceId = null;
    if ($passed) {
        $entranceId = 'VERDANT-EXAM-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    // Update attempt
    db()->query("
        UPDATE exam_attempts
        SET status = 'graded', submitted_at = NOW(), score = ?, total_marks = ?,
            percentage = ?, passed = ?, entrance_id = ?
        WHERE id = ?
    ", [$score, $totalMarks, $percentage, $passed ? 1 : 0, $entranceId, $attemptId]);

    // Update registration status
    db()->query("UPDATE exam_registrations SET status = 'exam_completed' WHERE id = ?", [$attempt['registration_id']]);

    return [
        'score' => $score,
        'total_marks' => $totalMarks,
        'percentage' => $percentage,
        'passed' => $passed,
        'entrance_id' => $entranceId
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $submitted ? 'Exam Results' : 'Entrance Examination' ?> — Verdant School</title>
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
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <style>
        :root {
            --neon-green: #00ff88;
            --neon-blue: #00d4ff;
            --neon-purple: #b366ff;
            --dark-bg: #0a0a0f;
            --card-bg: rgba(15, 25, 35, 0.95);
            --text-primary: #e0e6ed;
            --text-muted: #8892a0;
            --border-glow: rgba(0, 255, 136, 0.3);
            --success: #00ff88;
            --error: #ff4757;
            --warning: #ffcc00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Background */
        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(90deg, rgba(0, 255, 136, 0.02) 1px, transparent 1px),
                linear-gradient(rgba(0, 255, 136, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: -1;
        }

        /* Header */
        .exam-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-glow);
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .exam-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .exam-title h1 {
            font-size: 1.3rem;
            color: var(--neon-green);
        }

        .exam-title .student-name {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Timer */
        .timer {
            background: rgba(255, 71, 87, 0.2);
            border: 1px solid var(--error);
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--error);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: pulse 1s ease-in-out infinite;
        }

        .timer.warning {
            background: rgba(255, 204, 0, 0.2);
            border-color: var(--warning);
            color: var(--warning);
        }

        .timer.safe {
            background: rgba(0, 255, 136, 0.2);
            border-color: var(--success);
            color: var(--success);
            animation: none;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Main Content */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px 50px;
        }

        /* Error Page */
        .error-container {
            text-align: center;
            padding: 60px 20px;
        }

        .error-container i {
            font-size: 4rem;
            color: var(--error);
            margin-bottom: 20px;
        }

        .error-container h2 {
            color: var(--text-primary);
            margin-bottom: 15px;
        }

        .error-container p {
            color: var(--text-muted);
        }

        /* Question Card */
        .question-card {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .question-card:hover {
            border-color: var(--neon-green);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.1);
        }

        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-blue));
            color: #000;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .question-marks {
            float: right;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Options */
        .options {
            display: grid;
            gap: 12px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option:hover {
            border-color: var(--neon-blue);
            background: rgba(0, 212, 255, 0.1);
        }

        .option input {
            display: none;
        }

        .option input:checked+.option-label {
            border-color: var(--neon-green);
            background: rgba(0, 255, 136, 0.15);
        }

        .option-radio {
            width: 22px;
            height: 22px;
            border: 2px solid var(--text-muted);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .option input:checked~.option-radio {
            border-color: var(--neon-green);
            background: var(--neon-green);
        }

        .option input:checked~.option-radio::after {
            content: '✓';
            color: #000;
            font-weight: bold;
        }

        .option-text {
            flex: 1;
        }

        .option-letter {
            font-weight: bold;
            color: var(--neon-blue);
            margin-right: 10px;
        }

        /* Submit Button */
        .submit-section {
            text-align: center;
            margin-top: 40px;
        }

        .btn-submit {
            padding: 18px 50px;
            background: linear-gradient(135deg, var(--neon-green), #00cc6a);
            border: none;
            border-radius: 12px;
            color: #000;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(0, 255, 136, 0.5);
        }

        /* Results */
        .results-card {
            background: var(--card-bg);
            border: 2px solid var(--neon-green);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 0 50px rgba(0, 255, 136, 0.2);
        }

        .results-card.failed {
            border-color: var(--error);
            box-shadow: 0 0 50px rgba(255, 71, 87, 0.2);
        }

        .result-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }

        .result-icon.passed {
            color: var(--success);
        }

        .result-icon.failed {
            color: var(--error);
        }

        .result-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .result-title.passed {
            color: var(--success);
        }

        .result-title.failed {
            color: var(--error);
        }

        .score-display {
            font-size: 3rem;
            font-weight: bold;
            margin: 30px 0;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .entrance-id {
            background: rgba(0, 255, 136, 0.2);
            border: 2px dashed var(--neon-green);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }

        .entrance-id h3 {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .entrance-id .id-code {
            font-size: 2rem;
            font-weight: bold;
            color: var(--neon-green);
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
        }

        .entrance-id p {
            color: var(--text-muted);
            margin-top: 15px;
            font-size: 0.9rem;
        }

        /* Confetti */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: confetti-fall 3s ease-out forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        @media (max-width: 600px) {
            .exam-header {
                flex-direction: column;
                text-align: center;
            }

            .timer {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="cyber-grid"></div>

    <?php if ($error): ?>
        <div class="container">
            <div class="error-container">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Access Denied</h2>
                <p><?= htmlspecialchars($error) ?></p>
                <p style="margin-top: 20px;"><a href="http://localhost/attendance/" style="color: var(--neon-green);">← Back to Home</a></p>
            </div>
        </div>

    <?php elseif ($submitted): ?>
        <!-- Results Page -->
        <div class="exam-header">
            <div class="exam-title">
                <h1><i class="fas fa-graduation-cap"></i> Examination Complete</h1>
            </div>
        </div>

        <div class="container">
            <div class="results-card <?= $result['passed'] ? '' : 'failed' ?>">
                <div class="result-icon <?= $result['passed'] ? 'passed' : 'failed' ?>">
                    <i class="fas <?= $result['passed'] ? 'fa-trophy' : 'fa-times-circle' ?>"></i>
                </div>
                <h1 class="result-title <?= $result['passed'] ? 'passed' : 'failed' ?>">
                    <?= $result['passed'] ? 'Congratulations! You Passed!' : 'Unfortunately, You Did Not Pass' ?>
                </h1>

                <div class="score-display">
                    <?= $result['score'] ?> / <?= $result['total_marks'] ?> (<?= $result['percentage'] ?>%)
                </div>

                <?php if ($result['passed'] && $result['entrance_id']): ?>
                    <div class="entrance-id">
                        <h3>YOUR ENTRANCE ID</h3>
                        <div class="id-code"><?= $result['entrance_id'] ?></div>
                        <p>Use this ID when registering as a student at Verdant School.</p>
                    </div>

                    <a href="../auth/register.php" class="btn-submit" style="display: inline-block; text-decoration: none;">
                        <i class="fas fa-user-plus"></i> Register Now
                    </a>

                    <script>
                        // Confetti celebration
                        for (let i = 0; i < 100; i++) {
                            setTimeout(() => {
                                const confetti = document.createElement('div');
                                confetti.className = 'confetti';
                                confetti.style.left = Math.random() * 100 + '%';
                                confetti.style.background = ['#00ff88', '#00d4ff', '#b366ff', '#ffcc00', '#ff6b9d'][Math.floor(Math.random() * 5)];
                                document.body.appendChild(confetti);
                                setTimeout(() => confetti.remove(), 3000);
                            }, i * 30);
                        }
                    </script>
                <?php else: ?>
                    <p style="color: var(--text-muted); margin-top: 20px;">
                        Please contact the school administration for more information about retaking the examination.
                    </p>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- Exam Page -->
        <form method="POST" id="examForm">
            <div class="exam-header">
                <div class="exam-title">
                    <h1><i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($exam['exam_title']) ?></h1>
                    <span class="student-name"><?= htmlspecialchars($exam['full_name']) ?></span>
                </div>
                <div class="timer safe" id="timer">
                    <i class="fas fa-clock"></i>
                    <span id="timeDisplay">--:--</span>
                </div>
            </div>

            <div class="container">
                <?php foreach ($questions as $i => $q): ?>
                    <div class="question-card">
                        <span class="question-number">Question <?= $i + 1 ?></span>
                        <span class="question-marks"><?= $q['marks'] ?> mark<?= $q['marks'] > 1 ? 's' : '' ?></span>
                        <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>

                        <?php if ($q['question_type'] === 'mcq'): ?>
                            <div class="options">
                                <?php foreach (['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $letter => $text): ?>
                                    <?php if ($text): ?>
                                        <label class="option">
                                            <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $letter ?>">
                                            <span class="option-radio"></span>
                                            <span class="option-text">
                                                <span class="option-letter"><?= $letter ?>.</span>
                                                <?= htmlspecialchars($text) ?>
                                            </span>
                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="submit-section">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Exam
                    </button>
                </div>
            </div>
        </form>

        <script>
            // Timer
            let remaining = <?= $remaining ?? 0 ?>;
            const timerEl = document.getElementById('timer');
            const timeDisplay = document.getElementById('timeDisplay');

            function updateTimer() {
                const mins = Math.floor(remaining / 60);
                const secs = remaining % 60;
                timeDisplay.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

                // Update timer style based on time left
                if (remaining <= 60) {
                    timerEl.className = 'timer';
                } else if (remaining <= 300) {
                    timerEl.className = 'timer warning';
                } else {
                    timerEl.className = 'timer safe';
                }

                if (remaining <= 0) {
                    document.getElementById('examForm').submit();
                }

                remaining--;
            }

            updateTimer();
            setInterval(updateTimer, 1000);

            // Warn before leaving
            window.onbeforeunload = function() {
                return "Your exam is in progress. Are you sure you want to leave?";
            };

            document.getElementById('examForm').onsubmit = function() {
                window.onbeforeunload = null;
            };
        </script>
    <?php endif; ?>
</body>

</html>
