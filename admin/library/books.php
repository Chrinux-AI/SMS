<?php

/**
 * Library Books Management - Admin Panel
 * Manage library book catalog
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Library Books Management";
$current_page = "library/books.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $stmt = db()->prepare("INSERT INTO library_books (isbn, book_title, author, publisher, publication_year, edition, category, subject, language, total_copies, available_copies, rack_number, price, description, added_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitize_input($_POST['isbn']),
            sanitize_input($_POST['book_title']),
            sanitize_input($_POST['author']),
            sanitize_input($_POST['publisher']),
            intval($_POST['publication_year']),
            sanitize_input($_POST['edition']),
            $_POST['category'],
            sanitize_input($_POST['subject']),
            sanitize_input($_POST['language']),
            intval($_POST['total_copies']),
            intval($_POST['total_copies']), // available_copies initially same as total
            sanitize_input($_POST['rack_number']),
            floatval($_POST['price']),
            sanitize_input($_POST['description']),
            date('Y-m-d')
        ]);

        $_SESSION['success_message'] = "Book added successfully!";
        header("Location: books.php");
        exit;
    }
}

// Fetch books
$books = db()->fetchAll("SELECT * FROM library_books ORDER BY book_title");

$total_books = count($books);
$total_copies = array_sum(array_column($books, 'total_copies'));
$available_copies = array_sum(array_column($books, 'available_copies'));

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Library</span>
                <span>/</span>
                <span>Books</span>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_books; ?></div>
                    <div class="stat-label">Total Books</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_copies; ?></div>
                    <div class="stat-label">Total Copies</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $available_copies; ?></div>
                    <div class="stat-label">Available</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_copies - $available_copies; ?></div>
                    <div class="stat-label">Issued</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Book
            </button>
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Import Books
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Book Catalog</h3>
                <div class="card-actions">
                    <input type="text" id="searchInput" placeholder="Search books..." class="search-input">
                    <select id="filterCategory" class="filter-select">
                        <option value="">All Categories</option>
                        <option value="fiction">Fiction</option>
                        <option value="non_fiction">Non-Fiction</option>
                        <option value="textbook">Textbook</option>
                        <option value="reference">Reference</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>ISBN</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Total Copies</th>
                                <th>Available</th>
                                <th>Rack</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($book['isbn']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($book['book_title']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $book['category'])); ?></span></td>
                                    <td><?php echo $book['total_copies']; ?></td>
                                    <td>
                                        <?php if ($book['available_copies'] > 0): ?>
                                            <span class="badge badge-success"><?php echo $book['available_copies']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['rack_number']); ?></td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
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

    <!-- Add Book Modal -->
    <div id="bookModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3><i class="fas fa-book"></i> Add New Book</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">

                <div class="form-grid">
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" placeholder="978-3-16-148410-0">
                    </div>
                    <div class="form-group">
                        <label>Book Title <span class="required">*</span></label>
                        <input type="text" name="book_title" required>
                    </div>
                    <div class="form-group">
                        <label>Author <span class="required">*</span></label>
                        <input type="text" name="author" required>
                    </div>
                    <div class="form-group">
                        <label>Publisher</label>
                        <input type="text" name="publisher">
                    </div>
                    <div class="form-group">
                        <label>Publication Year</label>
                        <input type="number" name="publication_year" min="1900" max="2025">
                    </div>
                    <div class="form-group">
                        <label>Edition</label>
                        <input type="text" name="edition" placeholder="1st Edition">
                    </div>
                    <div class="form-group">
                        <label>Category <span class="required">*</span></label>
                        <select name="category" required>
                            <option value="fiction">Fiction</option>
                            <option value="non_fiction">Non-Fiction</option>
                            <option value="reference">Reference</option>
                            <option value="textbook">Textbook</option>
                            <option value="magazine">Magazine</option>
                            <option value="journal">Journal</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" placeholder="e.g., Science, Mathematics">
                    </div>
                    <div class="form-group">
                        <label>Language</label>
                        <input type="text" name="language" value="English">
                    </div>
                    <div class="form-group">
                        <label>Total Copies <span class="required">*</span></label>
                        <input type="number" name="total_copies" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Rack Number</label>
                        <input type="text" name="rack_number" placeholder="A-12">
                    </div>
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" name="price" step="0.01" min="0">
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('bookModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('bookModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>
