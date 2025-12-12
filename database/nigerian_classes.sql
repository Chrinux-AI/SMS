-- Nigerian Class System Migration
-- Verdant SMS v3.0 — 12 December 2025

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing classes table if needed and recreate with Nigerian structure
DROP TABLE IF EXISTS classes_backup;
CREATE TABLE IF NOT EXISTS classes_backup AS SELECT * FROM classes;

-- Update classes table structure
ALTER TABLE classes
    ADD COLUMN IF NOT EXISTS short_code VARCHAR(10) DEFAULT NULL AFTER name,
    ADD COLUMN IF NOT EXISTS section ENUM('primary', 'junior_secondary', 'senior_secondary') DEFAULT NULL AFTER short_code,
    ADD COLUMN IF NOT EXISTS level INT DEFAULT NULL AFTER section,
    ADD COLUMN IF NOT EXISTS arm VARCHAR(10) DEFAULT NULL AFTER level;

-- Clear existing data and insert Nigerian classes
TRUNCATE TABLE classes;

-- PRIMARY SECTION (P1 - P6)
INSERT INTO classes (name, short_code, section, level, arm) VALUES
('Primary 1', 'P1', 'primary', 1, 'A'),
('Primary 2', 'P2', 'primary', 2, 'A'),
('Primary 3', 'P3', 'primary', 3, 'A'),
('Primary 4', 'P4', 'primary', 4, 'A'),
('Primary 5', 'P5', 'primary', 5, 'A'),
('Primary 6', 'P6', 'primary', 6, 'A');

-- JUNIOR SECONDARY SECTION (JSS 1-3)
INSERT INTO classes (name, short_code, section, level, arm) VALUES
('JSS 1', 'JSS1', 'junior_secondary', 1, 'A'),
('JSS 2', 'JSS2', 'junior_secondary', 2, 'A'),
('JSS 3', 'JSS3', 'junior_secondary', 3, 'A');

-- SENIOR SECONDARY SECTION (SSS 1-3)
INSERT INTO classes (name, short_code, section, level, arm) VALUES
('SSS 1', 'SSS1', 'senior_secondary', 1, 'A'),
('SSS 2', 'SSS2', 'senior_secondary', 2, 'A'),
('SSS 3', 'SSS3', 'senior_secondary', 3, 'A');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify classes
SELECT id, name, short_code, section, level, arm FROM classes ORDER BY section, level, arm;
