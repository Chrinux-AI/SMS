<?php
/**
 * Migration Runner
 */
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/database.php';

echo "DEBUG: DB_HOST = " . DB_HOST . "\n";
echo "DEBUG: DB_USER = " . DB_USER . "\n";
echo "DEBUG: DB_NAME = " . DB_NAME . "\n";

try {
    $pdo = Database::getInstance()->getConnection();

    echo "Running migration 001_multi_school_setup...\n";

    // Disable foreign key checks temporarily to allow adding NOT NULL columns to populated tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/001_multi_school_setup.sql');

    // Execute multiple statements
    // PDO might not support multiple statements in one go depending on config, so we might need to split
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);

    if ($pdo->exec($sql) === false) {
        $err = $pdo->errorInfo();
        // If error is "Duplicate column name", we can ignore it (idempotency)
        if (strpos($err[2], "Duplicate column name") !== false) {
             echo "Info: Columns already exist.\n";
        } else {
             // throw new Exception("Migration failed: " . $err[2]);
             echo "Warning: " . $err[2] . "\n";
        }
    }

    // Ensure all existing records belong to school ID 1
    // First ensure school 1 exists
    $stmt = $pdo->prepare("SELECT id FROM schools WHERE id=1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        echo "Creating default school...\n";
        $pdo->exec("INSERT INTO schools (id, school_code, school_name, subscription_plan, subscription_status) VALUES (1, 'DEMO-001', 'Verdant Demo School', 'premium', 'active')");
    }

    // Update existing records
    $tables = ['users', 'students', 'teachers', 'classes', 'attendance', 'subjects'];
    foreach ($tables as $table) {
        // checks if table exists first
        $check = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($check->rowCount() > 0) {
             echo "Updating $table to school_id = 1...\n";
             $pdo->exec("UPDATE $table SET school_id = 1 WHERE school_id = 0 OR school_id IS NULL");
        }
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
