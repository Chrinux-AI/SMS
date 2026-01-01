<?php

/**
 * Question Bank - Teacher Module
 * Manage question banks for assessments
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
$page_title = 'Question Bank';

// Get subjects for this teacher
$subjects = db()->fetchAll("
    SELECT DISTINCT s.id, s.name, s.code
    FROM subjects s
    ORDER BY s.name
");
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
                <h1><i class="fas fa-database"></i> <?php echo $page_title; ?></h1>
                <p class="text-muted">Create and manage questions for assessments</p>
            </div>

            <div class="cyber-card">
                <div class="card-header">
                    <h2><i class="fas fa-plus-circle"></i> Create Question</h2>
                </div>
                <div class="card-body">
                    <form method="POST" class="cyber-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Subject</label>
                                <select name="subject_id" class="cyber-input" required>
                                    <option value="">Select Subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?php echo $subject['id']; ?>">
                                            <?php echo htmlspecialchars($subject['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Question Type</label>
                                <select name="type" class="cyber-input" required>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="short_answer">Short Answer</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Difficulty</label>
                                <select name="difficulty" class="cyber-input">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Points</label>
                                <input type="number" name="points" class="cyber-input" value="1" min="1" max="100">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Question Text</label>
                            <textarea name="question" class="cyber-input" rows="4" required placeholder="Enter your question here..."></textarea>
                        </div>

                        <div class="form-group" id="options-container">
                            <label>Options (for Multiple Choice)</label>
                            <div class="option-inputs">
                                <input type="text" name="options[]" class="cyber-input" placeholder="Option A">
                                <input type="text" name="options[]" class="cyber-input" placeholder="Option B">
                                <input type="text" name="options[]" class="cyber-input" placeholder="Option C">
                                <input type="text" name="options[]" class="cyber-input" placeholder="Option D">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Correct Answer</label>
                            <input type="text" name="correct_answer" class="cyber-input" placeholder="Enter correct answer">
                        </div>

                        <button type="submit" name="add_question" class="cyber-btn btn-primary">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </form>
                </div>
            </div>

            <div class="cyber-card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Question Library</h2>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted" style="padding: 2rem;">
                        <i class="fas fa-info-circle fa-2x"></i><br><br>
                        Question bank is being set up. Start adding questions above.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <style>
        .option-inputs {
            display: grid;
            gap: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
    </style>
</body>

</html>