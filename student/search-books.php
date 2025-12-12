<?php

/**
 * Student Library - Search Books
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "Search Books";
$current_page = "search-books.php";

// Search books
$keyword = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$query = "SELECT * FROM library_books WHERE 1=1";
$params = [];

if ($keyword) {
    $query .= " AND (book_title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY book_title LIMIT 50";

$books = empty($params) ? db()->fetchAll($query) : db()->fetchAll($query, $params);

include '../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-search"></i> <?php echo $page_title; ?></h1>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-filter"></i> Search Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <input type="text" name="search" placeholder="Search by title, author, ISBN..." value="<?php echo htmlspecialchars($keyword); ?>">
                        </div>
                        <div class="form-group">
                            <select name="category">
                                <option value="">All Categories</option>
                                <option value="fiction" <?php echo $category == 'fiction' ? 'selected' : ''; ?>>Fiction</option>
                                <option value="non_fiction" <?php echo $category == 'non_fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                                <option value="textbook" <?php echo $category == 'textbook' ? 'selected' : ''; ?>>Textbook</option>
                                <option value="reference" <?php echo $category == 'reference' ? 'selected' : ''; ?>>Reference</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-book"></i> Search Results (<?php echo count($books); ?> books)</h3>
            </div>
            <div class="card-body">
                <div class="books-grid">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                            <div class="book-cover">
                                <i class="fas fa-book fa-3x"></i>
                            </div>
                            <div class="book-details">
                                <h4><?php echo htmlspecialchars($book['book_title']); ?></h4>
                                <p class="book-author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($book['author']); ?></p>
                                <p class="book-category"><span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $book['category'])); ?></span></p>
                                <p class="book-availability">
                                    <?php if ($book['available_copies'] > 0): ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Available (<?php echo $book['available_copies']; ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Not Available
                                        </span>
                                    <?php endif; ?>
                                </p>
                                <div class="book-actions">
                                    <button class="btn btn-sm btn-primary" title="View Details">
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                    <?php if ($book['available_copies'] == 0): ?>
                                        <button class="btn btn-sm btn-warning" title="Reserve">
                                            <i class="fas fa-bookmark"></i> Reserve
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            gap: 15px;
        }

        .book-cover {
            color: var(--neon-cyan);
            text-align: center;
            padding: 10px;
        }

        .book-details h4 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }

        .book-author,
        .book-category,
        .book-availability {
            margin: 5px 0;
            font-size: 0.9em;
        }

        .book-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
    </style>
</body>

</html>
