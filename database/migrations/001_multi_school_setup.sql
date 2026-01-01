-- Migration: 001_multi_school_setup (Fixed)
-- Date: 2025-12-31

-- 1. Create schools table
CREATE TABLE IF NOT EXISTS `schools` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `school_code` VARCHAR(20) UNIQUE NOT NULL,
    `school_name` VARCHAR(255) NOT NULL,
    `logo_path` VARCHAR(255),
    `address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `phone` VARCHAR(20),
    `email` VARCHAR(255),
    `subscription_plan` ENUM('basic', 'standard', 'premium') DEFAULT 'standard',
    `subscription_status` ENUM('trial', 'active', 'suspended', 'expired') DEFAULT 'trial',
    `subscription_expiry` DATE,
    `max_students` INT DEFAULT 500,
    `max_staff` INT DEFAULT 50,
    `admin_user_id` INT,
    `registered_by` INT,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_code` (`school_code`),
    INDEX `idx_status` (`subscription_status`)
);

-- 2. Create School 1 (Default/Demo School) IMMEDIATELY
INSERT INTO schools (id, school_code, school_name, subscription_plan, subscription_status)
SELECT 1, 'DEMO-001', 'Verdant Demo School', 'premium', 'active'
WHERE NOT EXISTS (SELECT 1 FROM schools WHERE id = 1);

-- 3. Add school_id to users with DEFAULT 1
SET @dbname = DATABASE();
SET @tablename = "users";
SET @columnname = "school_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE users ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id), ADD CONSTRAINT fk_users_school FOREIGN KEY (school_id) REFERENCES schools(id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4. Add school_id to students
SET @tablename = "students";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE students ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id), ADD CONSTRAINT fk_students_school FOREIGN KEY (school_id) REFERENCES schools(id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 5. Add school_id to teachers
SET @tablename = "teachers";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE teachers ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id), ADD CONSTRAINT fk_teachers_school FOREIGN KEY (school_id) REFERENCES schools(id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 6. Add school_id to classes
SET @tablename = "classes";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE classes ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id), ADD CONSTRAINT fk_classes_school FOREIGN KEY (school_id) REFERENCES schools(id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 7. Add school_id to attendance
SET @tablename = "attendance";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE attendance ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 8. Add school_id to subjects
SET @tablename = "subjects";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE subjects ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 9. Add school_id to parents (if exists)
SET @tablename = "parents";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
    WHERE (table_name = @tablename) AND (table_schema = @dbname)
  ) > 0 AND (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)
  ) = 0,
  "ALTER TABLE parents ADD COLUMN school_id INT NOT NULL DEFAULT 1 AFTER id, ADD INDEX idx_school (school_id)",
  "SELECT 1"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
