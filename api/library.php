<?php

/**
 * Library API Endpoint
 * Handles all library management operations
 */
header('Content-Type: application/json');
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// CSRF protection for state-changing requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'search_books':
            $keyword = sanitize_input($_GET['keyword'] ?? '');
            $category = $_GET['category'] ?? '';

            $query = "SELECT * FROM library_books WHERE (book_title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
            $params = ["%$keyword%", "%$keyword%", "%$keyword%"];

            if ($category) {
                $query .= " AND category = ?";
                $params[] = $category;
            }

            $books = db()->fetchAll($query, $params);
            $response = ['success' => true, 'data' => $books];
            break;

        case 'issue_book':
            require_admin();
            $book_id = intval($_POST['book_id']);
            $member_id = intval($_POST['member_id']);
            $issue_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+14 days'));

            // Check availability
            $book = db()->fetch("SELECT available_copies FROM library_books WHERE id = ?", [$book_id]);
            if ($book['available_copies'] <= 0) {
                throw new Exception('Book not available');
            }

            // Issue book
            $stmt = db()->prepare("INSERT INTO library_issue_return (book_id, member_id, member_type, issue_date, due_date, status) VALUES (?, ?, 'student', ?, ?, 'issued')");
            $stmt->execute([$book_id, $member_id, $issue_date, $due_date]);

            // Update available copies
            db()->query("UPDATE library_books SET available_copies = available_copies - 1 WHERE id = ?", [$book_id]);

            $response = ['success' => true, 'message' => 'Book issued successfully'];
            break;

        case 'return_book':
            require_admin();
            $issue_id = intval($_POST['issue_id']);
            $return_date = date('Y-m-d');

            // Get issue details
            $issue = db()->fetch("SELECT * FROM library_issue_return WHERE id = ?", [$issue_id]);

            // Calculate fine if overdue
            $fine = 0;
            if (strtotime($return_date) > strtotime($issue['due_date'])) {
                $days_late = (strtotime($return_date) - strtotime($issue['due_date'])) / 86400;
                $fine = $days_late * 5; // ₹5 per day
            }

            // Update return
            db()->query("UPDATE library_issue_return SET return_date = ?, status = 'returned', fine_amount = ? WHERE id = ?", [$return_date, $fine, $issue_id]);

            // Update available copies
            db()->query("UPDATE library_books SET available_copies = available_copies + 1 WHERE id = ?", [$issue['book_id']]);

            $response = ['success' => true, 'message' => 'Book returned successfully', 'fine' => $fine];
            break;

        case 'get_issued_books':
            $member_id = intval($_GET['member_id']);
            $query = "SELECT lir.*, lb.book_title, lb.author, lb.isbn
                      FROM library_issue_return lir
                      JOIN library_books lb ON lir.book_id = lb.id
                      WHERE lir.member_id = ? AND lir.status = 'issued'
                      ORDER BY lir.due_date";
            $books = db()->fetchAll($query, [$member_id]);
            $response = ['success' => true, 'data' => $books];
            break;

        case 'reserve_book':
            $book_id = intval($_POST['book_id']);
            $member_id = intval($_POST['member_id']);

            $stmt = db()->prepare("INSERT INTO library_book_requests (book_id, member_id, member_type, request_date, status) VALUES (?, ?, 'student', NOW(), 'pending')");
            $stmt->execute([$book_id, $member_id]);

            $response = ['success' => true, 'message' => 'Book reserved successfully'];
            break;

        default:
            throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
