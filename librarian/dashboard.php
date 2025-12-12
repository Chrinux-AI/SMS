<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('librarian', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Library Stats
$total_books = db()->fetchOne("SELECT COUNT(*) as count FROM library_books")['count'] ?? 0;
$available_books = db()->fetchOne("SELECT COUNT(*) as count FROM library_books WHERE status = 'available'")['count'] ?? 0;
$issued_books = db()->fetchOne("SELECT COUNT(*) as count FROM library_issue_return WHERE status = 'issued'")['count'] ?? 0;
$overdue_books = db()->fetchOne("SELECT COUNT(*) as count FROM library_issue_return WHERE status = 'issued' AND due_date < CURDATE()")['count'] ?? 0;

// Today's Activity
$today_issues = db()->fetchOne("SELECT COUNT(*) as count FROM library_issue_return WHERE DATE(issue_date) = CURDATE()")['count'] ?? 0;
$today_returns = db()->fetchOne("SELECT COUNT(*) as count FROM library_issue_return WHERE DATE(return_date) = CURDATE()")['count'] ?? 0;

// Top Borrowed Books
$top_books = db()->fetchAll("
    SELECT lb.book_title, lb.author, COUNT(lir.id) as borrow_count
    FROM library_issue_return lir
    JOIN library_books lb ON lir.book_id = lb.id
    WHERE lir.issue_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY lb.id
    ORDER BY borrow_count DESC
    LIMIT 5
");

// Recent Issues
$recent_issues = db()->fetchAll("
    SELECT lir.*, lb.book_title,
           CONCAT(u.first_name, ' ', u.last_name) as member_name,
           DATEDIFF(lir.due_date, CURDATE()) as days_remaining
    FROM library_issue_return lir
    JOIN library_books lb ON lir.book_id = lb.id
    JOIN students st ON lir.member_id = st.id
    JOIN users u ON st.user_id = u.id
    WHERE lir.status = 'issued'
    ORDER BY lir.issue_date DESC
    LIMIT 10
");

// Overdue Fines Collected
$total_fines = db()->fetchOne("
    SELECT IFNULL(SUM(fine_amount), 0) as total
    FROM library_issue_return
    WHERE fine_paid = 1 AND MONTH(return_date) = MONTH(CURDATE())
")['total'] ?? 0;

$page_title = 'Library Management';
$page_icon = 'book';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb green"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/library/add-book.php'">
                        <i class="fas fa-plus"></i> Add Book
                    </button>
                    <button class="cyber-btn green" onclick="window.location.href='../admin/library/issue-book.php'">
                        <i class="fas fa-hand-holding"></i> Issue Book
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar green" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Librarian</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-book"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_books); ?></div>
                            <div class="stat-label">Total Books</div>
                            <div class="stat-trend"><i class="fas fa-warehouse"></i> Inventory</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($available_books); ?></div>
                            <div class="stat-label">Available</div>
                            <div class="stat-trend up"><i class="fas fa-book-open"></i> Ready</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-hand-holding"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $issued_books; ?></div>
                            <div class="stat-label">Issued</div>
                            <div class="stat-trend"><i class="fas fa-arrow-right"></i> Out</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $overdue_books; ?></div>
                            <div class="stat-label">Overdue</div>
                            <div class="stat-trend down"><i class="fas fa-clock"></i> Late</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-arrow-up"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $today_issues; ?></div>
                            <div class="stat-label">Today Issued</div>
                            <div class="stat-trend"><i class="fas fa-calendar-day"></i> Today</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon golden"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($total_fines); ?></div>
                            <div class="stat-label">Fines Collected</div>
                            <div class="stat-trend up"><i class="fas fa-rupee-sign"></i> This Month</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/library/issue-return.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-exchange-alt"></i></div>
                            <h4>Issue/Return</h4>
                            <p>Process book transactions</p>
                        </a>

                        <a href="../admin/library/books.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-list"></i></div>
                            <h4>Manage Books</h4>
                            <p>Add, edit, remove books</p>
                        </a>

                        <a href="../admin/library/overdue.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-bell"></i></div>
                            <h4>Overdue Reports</h4>
                            <p>Send reminders</p>
                        </a>

                        <a href="../admin/library/analytics.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-chart-line"></i></div>
                            <h4>Analytics</h4>
                            <p>Usage statistics</p>
                        </a>
                    </div>
                </section>

                <!-- Top Borrowed Books -->
                <?php if (!empty($top_books)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-trophy"></i> Most Popular Books (Last 30 Days)</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th>Times Borrowed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_books as $book): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                                            <td><span class="cyber-badge cyan"><?php echo $book['borrow_count']; ?> times</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Currently Issued Books -->
                <?php if (!empty($recent_issues)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-book-reader"></i> Currently Issued Books</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Book Title</th>
                                        <th>Borrower</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_issues as $issue): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($issue['book_title']); ?></td>
                                            <td><?php echo htmlspecialchars($issue['member_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($issue['issue_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($issue['due_date'])); ?></td>
                                            <td>
                                                <?php if ($issue['days_remaining'] < 0): ?>
                                                    <span class="cyber-badge red">Overdue by <?php echo abs($issue['days_remaining']); ?> days</span>
                                                <?php elseif ($issue['days_remaining'] <= 3): ?>
                                                    <span class="cyber-badge orange">Due in <?php echo $issue['days_remaining']; ?> days</span>
                                                <?php else: ?>
                                                    <span class="cyber-badge green"><?php echo $issue['days_remaining']; ?> days left</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
