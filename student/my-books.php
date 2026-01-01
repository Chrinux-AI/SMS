<?php

/**
 * Student My Books - Student Panel
 * View issued books and history
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';
require_role('student');

$page_title = "My Books";
$current_page = "my-books.php";

$student_id = $_SESSION['user_id'];

// Fetch currently issued books
$issued_books = db()->fetchAll("
    SELECT lir.*, lb.book_title, lb.author, lb.isbn,
           DATEDIFF(lir.due_date, NOW()) as days_remaining
    FROM library_issue_return lir
    JOIN library_books lb ON lir.book_id = lb.id
    JOIN students s ON lir.member_id = s.id
    WHERE s.user_id = ? AND lir.status = 'issued'
    ORDER BY lir.due_date
", [$student_id]);

// Fetch reading history
$history = db()->fetchAll("
    SELECT lir.*, lb.book_title, lb.author
    FROM library_issue_return lir
    JOIN library_books lb ON lir.book_id = lb.id
    JOIN students s ON lir.member_id = s.id
    WHERE s.user_id = ? AND lir.status = 'returned'
    ORDER BY lir.return_date DESC
    LIMIT 10
", [$student_id]);

$total_fines = array_sum(array_column($issued_books, 'fine_amount'));

include '../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
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
    <link rel="stylesheet" href="../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-reader"></i> <?php echo $page_title; ?></h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($issued_books); ?></div>
                    <div class="stat-label">Currently Issued</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-history"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($history); ?></div>
                    <div class="stat-label">Reading History</div>
                </div>
            </div>
            <?php if ($total_fines > 0): ?>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-details">
                        <div class="stat-value">₹<?php echo number_format($total_fines, 2); ?></div>
                        <div class="stat-label">Pending Fines</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-book-open"></i> Currently Issued Books</h3>
            </div>
            <div class="card-body">
                <?php if (empty($issued_books)): ?>
                    <div class="empty-state">
                        <i class="fas fa-book fa-3x"></i>
                        <p>No books currently issued</p>
                        <a href="search-books.php" class="btn btn-primary">Browse Library</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="cyber-table">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($issued_books as $book): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($book['book_title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($book['issue_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($book['due_date'])); ?></td>
                                        <td>
                                            <?php if ($book['days_remaining'] < 0): ?>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Overdue (<?php echo abs($book['days_remaining']); ?> days)
                                                </span>
                                            <?php elseif ($book['days_remaining'] <= 3): ?>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i>
                                                    Due in <?php echo $book['days_remaining']; ?> day(s)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-success">
                                                    On Time
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Reading History</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Issue Date</th>
                                <th>Return Date</th>
                                <th>Fine Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['author']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($item['issue_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($item['return_date'])); ?></td>
                                    <td>
                                        <?php if ($item['fine_amount'] > 0): ?>
                                            <span class="text-danger">₹<?php echo number_format($item['fine_amount'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="text-success">₹0.00</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            color: var(--neon-cyan);
            margin-bottom: 20px;
        }

        .empty-state p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
    </style>
</body>

</html>
