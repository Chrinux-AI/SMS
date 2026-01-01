<?php

/**
 * Book Issue/Return Management - Admin Panel
 * Manage library book circulation
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Issue/Return Books";
$current_page = "library/issue-return.php";

// Fetch issued books
$issued_books = db()->fetchAll("
    SELECT lir.*, lb.book_title, lb.isbn, lb.author,
           CONCAT(u.first_name, ' ', u.last_name) as member_name,
           DATEDIFF(NOW(), lir.due_date) as days_overdue
    FROM library_issue_return lir
    JOIN library_books lb ON lir.book_id = lb.id
    JOIN students s ON lir.member_id = s.id
    JOIN users u ON s.user_id = u.id
    WHERE lir.status = 'issued'
    ORDER BY lir.due_date
");

$overdue_count = count(array_filter($issued_books, fn($b) => $b['days_overdue'] > 0));
$total_issued = count($issued_books);

include '../../includes/cyber-nav.php';
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
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-exchange-alt"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Library</span>
                <span>/</span>
                <span>Issue/Return</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book-reader"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_issued; ?></div>
                    <div class="stat-label">Currently Issued</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $overdue_count; ?></div>
                    <div class="stat-label">Overdue Books</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-book"></i> Issue Book
            </button>
            <button class="btn btn-success">
                <i class="fas fa-undo"></i> Return Book
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Currently Issued Books</h3>
                <div class="card-actions">
                    <input type="text" id="searchInput" placeholder="Search by book or member..." class="search-input">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>ISBN</th>
                                <th>Member Name</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Fine</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($issued_books as $book):
                                $fine = $book['days_overdue'] > 0 ? $book['days_overdue'] * 5 : 0;
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($book['book_title']); ?></strong>
                                        <div style="font-size: 0.85em; color: var(--text-secondary);">
                                            <?php echo htmlspecialchars($book['author']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                    <td><?php echo htmlspecialchars($book['member_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($book['issue_date'])); ?></td>
                                    <td>
                                        <?php if ($book['days_overdue'] > 0): ?>
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <?php echo date('M d, Y', strtotime($book['due_date'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <?php echo date('M d, Y', strtotime($book['due_date'])); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($book['days_overdue'] > 0): ?>
                                            <span class="badge badge-danger"><?php echo $book['days_overdue']; ?> days</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">On Time</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($fine > 0): ?>
                                            <strong class="text-danger">₹<?php echo number_format($fine, 2); ?></strong>
                                        <?php else: ?>
                                            <span class="text-success">₹0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-undo"></i> Return
                                        </button>
                                        <button class="btn-icon btn-info" title="Extend Due Date">
                                            <i class="fas fa-calendar-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
