<?php

/**
 * Transport API Endpoint
 * Handles all transport management operations
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
        case 'get_routes':
            $query = "SELECT * FROM transport_routes WHERE is_active = 1 ORDER BY route_number";
            $routes = db()->fetchAll($query);
            $response = ['success' => true, 'data' => $routes];
            break;

        case 'get_student_transport':
            $student_id = intval($_GET['student_id']);
            $query = "SELECT st.*, tr.route_name, tr.route_number, tv.vehicle_number, td.driver_name, td.contact_number
                      FROM student_transport st
                      JOIN transport_routes tr ON st.route_id = tr.id
                      LEFT JOIN transport_assignments ta ON tr.id = ta.route_id
                      LEFT JOIN transport_vehicles tv ON ta.vehicle_id = tv.id
                      LEFT JOIN transport_drivers td ON ta.driver_id = td.id
                      WHERE st.student_id = ? AND st.is_active = 1";
            $transport = db()->fetch($query, [$student_id]);
            $response = ['success' => true, 'data' => $transport];
            break;

        case 'track_vehicle':
            $vehicle_id = intval($_GET['vehicle_id']);
            // In real implementation, this would fetch GPS coordinates from tracking device
            $query = "SELECT tv.*, ta.route_id, tr.route_name
                      FROM transport_vehicles tv
                      LEFT JOIN transport_assignments ta ON tv.id = ta.vehicle_id
                      LEFT JOIN transport_routes tr ON ta.route_id = tr.id
                      WHERE tv.id = ?";
            $vehicle = db()->fetch($query, [$vehicle_id]);

            // Mock GPS data
            $vehicle['current_location'] = [
                'latitude' => 28.6139 + (rand(-100, 100) / 10000),
                'longitude' => 77.2090 + (rand(-100, 100) / 10000),
                'speed' => rand(0, 60),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $response = ['success' => true, 'data' => $vehicle];
            break;

        case 'assign_route':
            require_admin();
            $student_id = intval($_POST['student_id']);
            $route_id = intval($_POST['route_id']);
            $pickup_point = sanitize_input($_POST['pickup_point']);
            $academic_year = sanitize_input($_POST['academic_year']);

            // Get route fare
            $route = db()->fetch("SELECT fare_amount FROM transport_routes WHERE id = ?", [$route_id]);

            $stmt = db()->prepare("INSERT INTO student_transport (student_id, route_id, pickup_point, academic_year, monthly_fee, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$student_id, $route_id, $pickup_point, $academic_year, $route['fare_amount']]);

            $response = ['success' => true, 'message' => 'Route assigned successfully'];
            break;

        default:
            throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
