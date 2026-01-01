<?php

/**
 * Chat Media Upload API
 * Handle file uploads for chat (images, videos, documents)
 */

session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$conversation_id = intval($_POST['conversation_id'] ?? 0);

// Validate file
$allowed_types = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'video/mp4', 'video/webm',
    'application/pdf', 'application/msword', 
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

$max_size = 10 * 1024 * 1024; // 10MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'error' => 'File too large. Maximum size: 10MB']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'File type not allowed']);
    exit;
}

// Determine file type category
$file_category = 'document';
if (strpos($mime_type, 'image/') === 0) {
    $file_category = 'image';
} elseif (strpos($mime_type, 'video/') === 0) {
    $file_category = 'video';
}

// Create upload directory
$upload_dir = BASE_PATH . '/uploads/chat/' . date('Y/m/');
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('chat_') . '_' . time() . '.' . $extension;
$file_path = $upload_dir . $filename;
$relative_path = '/uploads/chat/' . date('Y/m/') . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $file_path)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    exit;
}

// Generate thumbnail for images/videos
$thumbnail_path = null;
if ($file_category === 'image') {
    $thumbnail_path = generateImageThumbnail($file_path, $upload_dir, $filename);
} elseif ($file_category === 'video') {
    $thumbnail_path = generateVideoThumbnail($file_path, $upload_dir, $filename);
}

echo json_encode([
    'success' => true,
    'file' => [
        'url' => APP_URL . $relative_path,
        'thumbnail' => $thumbnail_path ? (APP_URL . $thumbnail_path) : null,
        'name' => $file['name'],
        'size' => $file['size'],
        'type' => $mime_type,
        'category' => $file_category
    ]
]);

function generateImageThumbnail($source_path, $upload_dir, $filename) {
    try {
        $thumbnail_path = $upload_dir . 'thumb_' . $filename;
        
        // Use GD library
        if (function_exists('imagecreatefromjpeg')) {
            $source = null;
            $extension = strtolower(pathinfo($source_path, PATHINFO_EXTENSION));
            
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $source = imagecreatefromjpeg($source_path);
                    break;
                case 'png':
                    $source = imagecreatefrompng($source_path);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($source_path);
                    break;
                case 'webp':
                    $source = imagecreatefromwebp($source_path);
                    break;
            }
            
            if ($source) {
                $width = imagesx($source);
                $height = imagesy($source);
                $thumb_width = 200;
                $thumb_height = ($height / $width) * $thumb_width;
                
                $thumbnail = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
                
                imagejpeg($thumbnail, $thumbnail_path, 85);
                imagedestroy($source);
                imagedestroy($thumbnail);
                
                return '/uploads/chat/' . date('Y/m/') . 'thumb_' . $filename;
            }
        }
    } catch (Exception $e) {
        // Thumbnail generation failed, continue without it
    }
    
    return null;
}

function generateVideoThumbnail($source_path, $upload_dir, $filename) {
    // Video thumbnail generation requires ffmpeg
    // For now, return null - can be implemented later
    return null;
}


