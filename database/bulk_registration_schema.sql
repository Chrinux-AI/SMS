-- Bulk Registration System Schema
-- Verdant SMS v3.0 — 12 December 2025

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- BULK REGISTRATION CONFIG TABLE
-- ============================================
DROP TABLE IF EXISTS bulk_registration_config;
CREATE TABLE bulk_registration_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    google_sheet_url VARCHAR(500) DEFAULT NULL,
    csv_file_path VARCHAR(255) DEFAULT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    target_roles JSON NOT NULL, -- ['parent', 'teacher', 'librarian', etc.]
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    total_records INT DEFAULT 0,
    processed_records INT DEFAULT 0,
    failed_records INT DEFAULT 0,
    error_log TEXT DEFAULT NULL,
    created_by INT NOT NULL, -- Admin who created this config
    processed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
);

-- ============================================
-- BULK REGISTRATION RECORDS TABLE
-- ============================================
DROP TABLE IF EXISTS bulk_registration_records;
CREATE TABLE bulk_registration_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role VARCHAR(50) NOT NULL,
    department VARCHAR(100) DEFAULT NULL,
    qualifications TEXT DEFAULT NULL,
    -- Parent-specific fields
    child1_name VARCHAR(150) DEFAULT NULL,
    child1_class VARCHAR(50) DEFAULT NULL,
    child2_name VARCHAR(150) DEFAULT NULL,
    child2_class VARCHAR(50) DEFAULT NULL,
    child3_name VARCHAR(150) DEFAULT NULL,
    child3_class VARCHAR(50) DEFAULT NULL,
    relationship ENUM('Mother', 'Father', 'Guardian') DEFAULT NULL,
    -- Processing status
    status ENUM('pending', 'processed', 'failed', 'duplicate') DEFAULT 'pending',
    user_id INT DEFAULT NULL, -- Created user ID after processing
    error_message TEXT DEFAULT NULL,
    processed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (config_id) REFERENCES bulk_registration_config(id) ON DELETE CASCADE,
    INDEX idx_config (config_id),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

SET FOREIGN_KEY_CHECKS = 1;

-- Verify tables
SHOW TABLES LIKE 'bulk_registration%';
