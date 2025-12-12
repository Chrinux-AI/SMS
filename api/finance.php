<?php

/**
 * Finance API Endpoint
 * Handles all finance and fee management operations
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
        case 'get_fee_structures':
            $grade = $_GET['grade'] ?? '';
            $query = "SELECT * FROM fee_structures WHERE is_active = 1";
            if ($grade) {
                $query .= " AND grade_level = ?";
                $structures = db()->fetchAll($query, [$grade]);
            } else {
                $structures = db()->fetchAll($query);
            }
            $response = ['success' => true, 'data' => $structures];
            break;

        case 'get_student_invoices':
            $student_id = intval($_GET['student_id']);
            $query = "SELECT fi.*,
                      (SELECT SUM(amount) FROM fee_invoice_items WHERE invoice_id = fi.id) as total_amount
                      FROM fee_invoices fi
                      WHERE fi.student_id = ?
                      ORDER BY fi.due_date DESC";
            $invoices = db()->fetchAll($query, [$student_id]);
            $response = ['success' => true, 'data' => $invoices];
            break;

        case 'generate_invoice':
            require_admin();
            $student_id = intval($_POST['student_id']);
            $academic_year = sanitize_input($_POST['academic_year']);
            $due_date = sanitize_input($_POST['due_date']);

            // Insert invoice
            $stmt = db()->prepare("INSERT INTO fee_invoices (student_id, invoice_number, academic_year, due_date, status) VALUES (?, ?, ?, ?, 'pending')");
            $invoice_number = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
            $stmt->execute([$student_id, $invoice_number, $academic_year, $due_date]);

            $invoice_id = db()->lastInsertId();

            // Add invoice items
            $fee_structures = json_decode($_POST['fee_structures'], true);
            foreach ($fee_structures as $fee) {
                $stmt = db()->prepare("INSERT INTO fee_invoice_items (invoice_id, fee_structure_id, description, amount) VALUES (?, ?, ?, ?)");
                $stmt->execute([$invoice_id, $fee['id'], $fee['description'], $fee['amount']]);
            }

            $response = ['success' => true, 'message' => 'Invoice generated successfully', 'invoice_id' => $invoice_id];
            break;

        case 'record_payment':
            $invoice_id = intval($_POST['invoice_id']);
            $amount = floatval($_POST['amount']);
            $payment_method = sanitize_input($_POST['payment_method']);
            $transaction_id = sanitize_input($_POST['transaction_id'] ?? '');

            $stmt = db()->prepare("INSERT INTO fee_payments (invoice_id, amount_paid, payment_method, transaction_id, payment_date) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$invoice_id, $amount, $payment_method, $transaction_id]);

            // Update invoice status
            $query = "SELECT (SELECT SUM(amount) FROM fee_invoice_items WHERE invoice_id = ?) as total,
                      (SELECT IFNULL(SUM(amount_paid), 0) FROM fee_payments WHERE invoice_id = ?) as paid";
            $result = db()->fetch($query, [$invoice_id, $invoice_id]);

            $status = ($result['paid'] >= $result['total']) ? 'paid' : 'partial';
            db()->query("UPDATE fee_invoices SET status = ? WHERE id = ?", [$status, $invoice_id]);

            $response = ['success' => true, 'message' => 'Payment recorded successfully'];
            break;

        case 'get_payment_history':
            $student_id = intval($_GET['student_id']);
            $query = "SELECT fp.*, fi.invoice_number
                      FROM fee_payments fp
                      JOIN fee_invoices fi ON fp.invoice_id = fi.id
                      WHERE fi.student_id = ?
                      ORDER BY fp.payment_date DESC";
            $payments = db()->fetchAll($query, [$student_id]);
            $response = ['success' => true, 'data' => $payments];
            break;

        default:
            throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
