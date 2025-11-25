-- ============================================================================
-- School Management System (SMS) - Complete Database Schema
-- Version: 3.0.0
-- Description: Comprehensive school management database for academics, finance,
--              library, transport, hostel, HR, and inventory management
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================================
-- 1. ACADEMIC MANAGEMENT MODULE
-- ============================================================================

-- Table: subjects
-- Stores all subjects taught in the school
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subject_code` VARCHAR(20) NOT NULL UNIQUE,
  `subject_name` VARCHAR(100) NOT NULL,
  `subject_type` ENUM('core', 'elective', 'optional', 'extra_curricular') DEFAULT 'core',
  `grade_level` VARCHAR(20) NULL COMMENT 'e.g., Grade 1, Grade 2, High School',
  `credit_hours` DECIMAL(4,2) DEFAULT 0.00,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_subject_code` (`subject_code`),
  INDEX `idx_grade_level` (`grade_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='School subjects catalog';

-- Table: syllabus
-- Stores syllabus for each subject
CREATE TABLE IF NOT EXISTS `syllabus` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subject_id` INT(11) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `grade_level` VARCHAR(20) NOT NULL,
  `syllabus_title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `topics` TEXT COMMENT 'JSON array of topics',
  `learning_objectives` TEXT,
  `textbook_reference` VARCHAR(255),
  `uploaded_file` VARCHAR(255) NULL,
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
  INDEX `idx_academic_year` (`academic_year`),
  INDEX `idx_grade` (`grade_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lesson_plans
-- Teacher lesson planning
CREATE TABLE IF NOT EXISTS `lesson_plans` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` INT(11) NOT NULL,
  `subject_id` INT(11) NOT NULL,
  `class_id` INT(11) NOT NULL,
  `lesson_title` VARCHAR(200) NOT NULL,
  `lesson_date` DATE NOT NULL,
  `duration_minutes` INT(11) DEFAULT 45,
  `learning_objectives` TEXT,
  `teaching_methods` TEXT,
  `resources_required` TEXT,
  `assessment_methods` TEXT,
  `homework_assigned` TEXT,
  `notes` TEXT,
  `status` ENUM('planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'planned',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
  INDEX `idx_lesson_date` (`lesson_date`),
  INDEX `idx_teacher` (`teacher_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: examinations
-- Examination management
CREATE TABLE IF NOT EXISTS `examinations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `exam_name` VARCHAR(100) NOT NULL,
  `exam_type` ENUM('midterm', 'final', 'quarterly', 'monthly', 'weekly', 'surprise') DEFAULT 'midterm',
  `academic_year` VARCHAR(20) NOT NULL,
  `grade_level` VARCHAR(20) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `description` TEXT,
  `instructions` TEXT,
  `created_by` INT(11) NOT NULL,
  `is_published` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_academic_year` (`academic_year`),
  INDEX `idx_grade` (`grade_level`),
  INDEX `idx_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: exam_schedule
-- Individual exam paper schedules
CREATE TABLE IF NOT EXISTS `exam_schedule` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `examination_id` INT(11) NOT NULL,
  `subject_id` INT(11) NOT NULL,
  `exam_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `duration_minutes` INT(11) NOT NULL,
  `total_marks` DECIMAL(6,2) DEFAULT 100.00,
  `passing_marks` DECIMAL(6,2) DEFAULT 40.00,
  `room_number` VARCHAR(50),
  `instructions` TEXT,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`examination_id`) REFERENCES `examinations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
  INDEX `idx_exam_date` (`exam_date`),
  INDEX `idx_examination` (`examination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: exam_results
-- Student exam results
CREATE TABLE IF NOT EXISTS `exam_results` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `exam_schedule_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `marks_obtained` DECIMAL(6,2),
  `total_marks` DECIMAL(6,2) NOT NULL,
  `percentage` DECIMAL(5,2),
  `grade` VARCHAR(5),
  `remarks` TEXT,
  `is_absent` TINYINT(1) DEFAULT 0,
  `entered_by` INT(11) NOT NULL COMMENT 'Teacher ID',
  `entered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`exam_schedule_id`) REFERENCES `exam_schedule`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_student_exam` (`exam_schedule_id`, `student_id`),
  INDEX `idx_student` (`student_id`),
  INDEX `idx_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: grading_schemes
-- Grading system configuration
CREATE TABLE IF NOT EXISTS `grading_schemes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `scheme_name` VARCHAR(100) NOT NULL,
  `min_percentage` DECIMAL(5,2) NOT NULL,
  `max_percentage` DECIMAL(5,2) NOT NULL,
  `grade` VARCHAR(5) NOT NULL,
  `grade_point` DECIMAL(3,2),
  `description` VARCHAR(100),
  `is_passing` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `idx_percentage` (`min_percentage`, `max_percentage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mark_sheets
-- Generated mark sheets/report cards
CREATE TABLE IF NOT EXISTS `mark_sheets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` INT(11) NOT NULL,
  `examination_id` INT(11) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `total_marks` DECIMAL(8,2),
  `marks_obtained` DECIMAL(8,2),
  `percentage` DECIMAL(5,2),
  `overall_grade` VARCHAR(5),
  `gpa` DECIMAL(3,2),
  `rank` INT(11) COMMENT 'Class rank',
  `attendance_percentage` DECIMAL(5,2),
  `remarks` TEXT,
  `generated_by` INT(11) NOT NULL,
  `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`examination_id`) REFERENCES `examinations`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_student_exam_marksheet` (`student_id`, `examination_id`),
  INDEX `idx_academic_year` (`academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: certificates
-- Student certificates (TC, bonafide, etc.)
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` INT(11) NOT NULL,
  `certificate_type` ENUM('transfer_certificate', 'bonafide', 'character', 'course_completion', 'achievement', 'participation', 'other') NOT NULL,
  `certificate_number` VARCHAR(50) UNIQUE NOT NULL,
  `issue_date` DATE NOT NULL,
  `valid_until` DATE NULL,
  `purpose` VARCHAR(255),
  `content` TEXT COMMENT 'Certificate content/body',
  `issued_by` INT(11) NOT NULL,
  `signed_by` VARCHAR(100) COMMENT 'Principal/Authority name',
  `pdf_path` VARCHAR(255),
  `status` ENUM('draft', 'issued', 'revoked') DEFAULT 'issued',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  INDEX `idx_certificate_type` (`certificate_type`),
  INDEX `idx_issue_date` (`issue_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: timetable
-- School timetable management
CREATE TABLE IF NOT EXISTS `timetable` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `class_id` INT(11) NOT NULL,
  `subject_id` INT(11) NOT NULL,
  `teacher_id` INT(11) NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
  `period_number` INT(2) NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `room_number` VARCHAR(50),
  `academic_year` VARCHAR(20) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_class_day_period` (`class_id`, `day_of_week`, `period_number`, `academic_year`),
  INDEX `idx_day` (`day_of_week`),
  INDEX `idx_teacher` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. FINANCE & FEE MANAGEMENT MODULE
-- ============================================================================

-- Table: fee_structures
-- Fee structure templates
CREATE TABLE IF NOT EXISTS `fee_structures` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fee_name` VARCHAR(100) NOT NULL,
  `fee_type` ENUM('tuition', 'admission', 'annual', 'exam', 'transport', 'hostel', 'library', 'sports', 'lab', 'misc') NOT NULL,
  `grade_level` VARCHAR(20),
  `amount` DECIMAL(10,2) NOT NULL,
  `frequency` ENUM('one_time', 'monthly', 'quarterly', 'half_yearly', 'yearly') DEFAULT 'one_time',
  `due_day` INT(2) COMMENT 'Day of month for recurring fees',
  `description` TEXT,
  `is_mandatory` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  `academic_year` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_fee_type` (`fee_type`),
  INDEX `idx_grade` (`grade_level`),
  INDEX `idx_academic_year` (`academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: fee_invoices
-- Student fee invoices
CREATE TABLE IF NOT EXISTS `fee_invoices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
  `student_id` INT(11) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `invoice_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) DEFAULT 0.00,
  `net_amount` DECIMAL(10,2) NOT NULL,
  `amount_paid` DECIMAL(10,2) DEFAULT 0.00,
  `amount_due` DECIMAL(10,2) NOT NULL,
  `status` ENUM('draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
  `remarks` TEXT,
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  INDEX `idx_invoice_number` (`invoice_number`),
  INDEX `idx_student` (`student_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_dates` (`invoice_date`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: fee_invoice_items
-- Line items for fee invoices
CREATE TABLE IF NOT EXISTS `fee_invoice_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `fee_structure_id` INT(11) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantity` INT(11) DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `fee_invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: fee_payments
-- Fee payment records
CREATE TABLE IF NOT EXISTS `fee_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `payment_number` VARCHAR(50) UNIQUE NOT NULL,
  `invoice_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `payment_date` DATE NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('cash', 'cheque', 'card', 'online', 'bank_transfer', 'upi') NOT NULL,
  `transaction_id` VARCHAR(100),
  `reference_number` VARCHAR(100),
  `bank_name` VARCHAR(100),
  `cheque_number` VARCHAR(50),
  `remarks` TEXT,
  `received_by` INT(11) NOT NULL,
  `receipt_generated` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `fee_invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  INDEX `idx_payment_number` (`payment_number`),
  INDEX `idx_payment_date` (`payment_date`),
  INDEX `idx_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: expenses
-- School expense tracking
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `expense_number` VARCHAR(50) UNIQUE NOT NULL,
  `expense_date` DATE NOT NULL,
  `expense_category` ENUM('salary', 'utilities', 'maintenance', 'supplies', 'transport', 'food', 'infrastructure', 'equipment', 'marketing', 'other') NOT NULL,
  `description` TEXT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('cash', 'cheque', 'card', 'online', 'bank_transfer') NOT NULL,
  `vendor_name` VARCHAR(100),
  `bill_number` VARCHAR(50),
  `bill_file` VARCHAR(255),
  `approved_by` INT(11),
  `paid_by` INT(11) NOT NULL,
  `status` ENUM('pending', 'approved', 'paid', 'rejected') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_expense_date` (`expense_date`),
  INDEX `idx_category` (`expense_category`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: salary_structure
-- Staff salary structure
CREATE TABLE IF NOT EXISTS `salary_structure` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `staff_id` INT(11) NOT NULL COMMENT 'References teachers or other staff table',
  `basic_salary` DECIMAL(10,2) NOT NULL,
  `hra` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'House Rent Allowance',
  `da` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Dearness Allowance',
  `ta` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Transport Allowance',
  `medical_allowance` DECIMAL(10,2) DEFAULT 0.00,
  `other_allowances` DECIMAL(10,2) DEFAULT 0.00,
  `pf_deduction` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Provident Fund',
  `tax_deduction` DECIMAL(10,2) DEFAULT 0.00,
  `other_deductions` DECIMAL(10,2) DEFAULT 0.00,
  `gross_salary` DECIMAL(10,2) NOT NULL,
  `net_salary` DECIMAL(10,2) NOT NULL,
  `effective_from` DATE NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_staff` (`staff_id`),
  INDEX `idx_effective_from` (`effective_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: salary_payments
-- Salary payment records
CREATE TABLE IF NOT EXISTS `salary_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `staff_id` INT(11) NOT NULL,
  `salary_structure_id` INT(11) NOT NULL,
  `payment_month` DATE NOT NULL COMMENT 'First day of payment month',
  `payment_date` DATE NOT NULL,
  `gross_salary` DECIMAL(10,2) NOT NULL,
  `total_deductions` DECIMAL(10,2) DEFAULT 0.00,
  `net_salary` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('cash', 'cheque', 'bank_transfer') NOT NULL,
  `transaction_id` VARCHAR(100),
  `remarks` TEXT,
  `paid_by` INT(11) NOT NULL,
  `slip_generated` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`salary_structure_id`) REFERENCES `salary_structure`(`id`) ON DELETE RESTRICT,
  UNIQUE KEY `unique_staff_month` (`staff_id`, `payment_month`),
  INDEX `idx_payment_month` (`payment_month`),
  INDEX `idx_payment_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. LIBRARY MANAGEMENT MODULE
-- ============================================================================

-- Table: library_books
-- Library book catalog
CREATE TABLE IF NOT EXISTS `library_books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `isbn` VARCHAR(20),
  `book_title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `publisher` VARCHAR(100),
  `publication_year` YEAR,
  `edition` VARCHAR(50),
  `category` ENUM('fiction', 'non_fiction', 'reference', 'textbook', 'magazine', 'journal', 'other') DEFAULT 'non_fiction',
  `subject` VARCHAR(100),
  `language` VARCHAR(50) DEFAULT 'English',
  `total_copies` INT(11) DEFAULT 1,
  `available_copies` INT(11) DEFAULT 1,
  `rack_number` VARCHAR(20),
  `price` DECIMAL(8,2),
  `description` TEXT,
  `cover_image` VARCHAR(255),
  `added_date` DATE NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_isbn` (`isbn`),
  INDEX `idx_title` (`book_title`),
  INDEX `idx_author` (`author`),
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: library_members
-- Library membership records
CREATE TABLE IF NOT EXISTS `library_members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `member_type` ENUM('student', 'teacher', 'staff', 'parent') NOT NULL,
  `member_id` INT(11) NOT NULL COMMENT 'ID from respective table',
  `membership_number` VARCHAR(50) UNIQUE NOT NULL,
  `membership_date` DATE NOT NULL,
  `expiry_date` DATE NOT NULL,
  `max_books_allowed` INT(2) DEFAULT 3,
  `max_days_allowed` INT(3) DEFAULT 14,
  `deposit_amount` DECIMAL(8,2) DEFAULT 0.00,
  `fine_amount` DECIMAL(8,2) DEFAULT 0.00,
  `status` ENUM('active', 'inactive', 'suspended', 'expired') DEFAULT 'active',
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_membership_number` (`membership_number`),
  INDEX `idx_member_type_id` (`member_type`, `member_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: library_issue_return
-- Book issue and return records
CREATE TABLE IF NOT EXISTS `library_issue_return` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `book_id` INT(11) NOT NULL,
  `member_id` INT(11) NOT NULL,
  `issue_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `return_date` DATE NULL,
  `actual_return_date` DATE NULL,
  `days_overdue` INT(11) DEFAULT 0,
  `fine_amount` DECIMAL(8,2) DEFAULT 0.00,
  `fine_paid` TINYINT(1) DEFAULT 0,
  `issued_by` INT(11) NOT NULL,
  `returned_to` INT(11) NULL,
  `status` ENUM('issued', 'returned', 'lost', 'damaged') DEFAULT 'issued',
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`book_id`) REFERENCES `library_books`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `library_members`(`id`) ON DELETE CASCADE,
  INDEX `idx_issue_date` (`issue_date`),
  INDEX `idx_status` (`status`),
  INDEX `idx_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: library_book_requests
-- Book request/reservation system
CREATE TABLE IF NOT EXISTS `library_book_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `book_id` INT(11) NOT NULL,
  `member_id` INT(11) NOT NULL,
  `request_date` DATE NOT NULL,
  `request_type` ENUM('reserve', 'recommend_purchase') DEFAULT 'reserve',
  `status` ENUM('pending', 'approved', 'issued', 'rejected', 'cancelled') DEFAULT 'pending',
  `processed_by` INT(11) NULL,
  `processed_date` DATE NULL,
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`book_id`) REFERENCES `library_books`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `library_members`(`id`) ON DELETE CASCADE,
  INDEX `idx_request_date` (`request_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. TRANSPORT MANAGEMENT MODULE
-- ============================================================================

-- Table: transport_routes
-- Transport route management
CREATE TABLE IF NOT EXISTS `transport_routes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `route_number` VARCHAR(20) UNIQUE NOT NULL,
  `route_name` VARCHAR(100) NOT NULL,
  `starting_point` VARCHAR(255) NOT NULL,
  `ending_point` VARCHAR(255) NOT NULL,
  `total_distance_km` DECIMAL(6,2),
  `estimated_time_minutes` INT(11),
  `fare_amount` DECIMAL(8,2),
  `stops` TEXT COMMENT 'JSON array of stops with coordinates',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_route_number` (`route_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: transport_vehicles
-- Vehicle fleet management
CREATE TABLE IF NOT EXISTS `transport_vehicles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `vehicle_number` VARCHAR(20) UNIQUE NOT NULL,
  `vehicle_type` ENUM('bus', 'van', 'mini_bus', 'car') DEFAULT 'bus',
  `vehicle_model` VARCHAR(100),
  `manufacturing_year` YEAR,
  `seating_capacity` INT(3) NOT NULL,
  `registration_number` VARCHAR(50) UNIQUE NOT NULL,
  `insurance_number` VARCHAR(50),
  `insurance_expiry` DATE,
  `fitness_certificate_expiry` DATE,
  `permit_expiry` DATE,
  `last_service_date` DATE,
  `next_service_date` DATE,
  `gps_device_id` VARCHAR(50),
  `status` ENUM('active', 'maintenance', 'inactive', 'retired') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_vehicle_number` (`vehicle_number`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: transport_drivers
-- Driver management
CREATE TABLE IF NOT EXISTS `transport_drivers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `driver_name` VARCHAR(100) NOT NULL,
  `employee_id` VARCHAR(50) UNIQUE NOT NULL,
  `contact_number` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100),
  `address` TEXT,
  `license_number` VARCHAR(50) UNIQUE NOT NULL,
  `license_type` VARCHAR(20),
  `license_expiry` DATE NOT NULL,
  `date_of_birth` DATE,
  `date_of_joining` DATE NOT NULL,
  `blood_group` VARCHAR(5),
  `emergency_contact` VARCHAR(20),
  `photo` VARCHAR(255),
  `status` ENUM('active', 'on_leave', 'suspended', 'resigned') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_employee_id` (`employee_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: transport_assignments
-- Route-Vehicle-Driver assignments
CREATE TABLE IF NOT EXISTS `transport_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `route_id` INT(11) NOT NULL,
  `vehicle_id` INT(11) NOT NULL,
  `driver_id` INT(11) NOT NULL,
  `conductor_id` INT(11) NULL,
  `shift` ENUM('morning', 'afternoon', 'both') DEFAULT 'both',
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`route_id`) REFERENCES `transport_routes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`vehicle_id`) REFERENCES `transport_vehicles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `transport_drivers`(`id`) ON DELETE CASCADE,
  INDEX `idx_route` (`route_id`),
  INDEX `idx_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: student_transport
-- Student transport enrollment
CREATE TABLE IF NOT EXISTS `student_transport` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` INT(11) NOT NULL,
  `route_id` INT(11) NOT NULL,
  `pickup_point` VARCHAR(255) NOT NULL,
  `drop_point` VARCHAR(255) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `monthly_fee` DECIMAL(8,2) NOT NULL,
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`route_id`) REFERENCES `transport_routes`(`id`) ON DELETE CASCADE,
  INDEX `idx_student` (`student_id`),
  INDEX `idx_route` (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: vehicle_maintenance
-- Vehicle maintenance records
CREATE TABLE IF NOT EXISTS `vehicle_maintenance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT(11) NOT NULL,
  `maintenance_date` DATE NOT NULL,
  `maintenance_type` ENUM('routine_service', 'repair', 'breakdown', 'inspection', 'oil_change', 'tire_change', 'other') NOT NULL,
  `description` TEXT NOT NULL,
  `cost` DECIMAL(10,2),
  `vendor_name` VARCHAR(100),
  `next_service_date` DATE,
  `odometer_reading` INT(11),
  `performed_by` VARCHAR(100),
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`vehicle_id`) REFERENCES `transport_vehicles`(`id`) ON DELETE CASCADE,
  INDEX `idx_vehicle` (`vehicle_id`),
  INDEX `idx_maintenance_date` (`maintenance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. HOSTEL MANAGEMENT MODULE
-- ============================================================================

-- Table: hostels
-- Hostel buildings
CREATE TABLE IF NOT EXISTS `hostels` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hostel_name` VARCHAR(100) NOT NULL,
  `hostel_type` ENUM('boys', 'girls', 'mixed', 'staff') DEFAULT 'boys',
  `total_floors` INT(2),
  `total_rooms` INT(4),
  `total_capacity` INT(4),
  `warden_name` VARCHAR(100),
  `warden_contact` VARCHAR(20),
  `address` TEXT,
  `facilities` TEXT COMMENT 'JSON array of facilities',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hostel_rooms
-- Hostel room details
CREATE TABLE IF NOT EXISTS `hostel_rooms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hostel_id` INT(11) NOT NULL,
  `room_number` VARCHAR(20) NOT NULL,
  `floor_number` INT(2),
  `room_type` ENUM('single', 'double', 'triple', 'quad', 'dormitory') DEFAULT 'double',
  `capacity` INT(2) NOT NULL,
  `current_occupancy` INT(2) DEFAULT 0,
  `facilities` TEXT COMMENT 'JSON array of facilities',
  `rent_amount` DECIMAL(8,2),
  `status` ENUM('available', 'occupied', 'maintenance', 'reserved') DEFAULT 'available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`hostel_id`) REFERENCES `hostels`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_hostel_room` (`hostel_id`, `room_number`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hostel_allocations
-- Room allocation to students
CREATE TABLE IF NOT EXISTS `hostel_allocations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` INT(11) NOT NULL,
  `room_id` INT(11) NOT NULL,
  `bed_number` VARCHAR(10),
  `allocation_date` DATE NOT NULL,
  `checkout_date` DATE NULL,
  `monthly_rent` DECIMAL(8,2),
  `deposit_amount` DECIMAL(8,2),
  `status` ENUM('allocated', 'checked_out', 'transferred') DEFAULT 'allocated',
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`room_id`) REFERENCES `hostel_rooms`(`id`) ON DELETE CASCADE,
  INDEX `idx_student` (`student_id`),
  INDEX `idx_room` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hostel_mess
-- Mess management
CREATE TABLE IF NOT EXISTS `hostel_mess` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hostel_id` INT(11) NOT NULL,
  `menu_date` DATE NOT NULL,
  `meal_type` ENUM('breakfast', 'lunch', 'snacks', 'dinner') NOT NULL,
  `menu_items` TEXT NOT NULL,
  `created_by` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`hostel_id`) REFERENCES `hostels`(`id`) ON DELETE CASCADE,
  INDEX `idx_menu_date` (`menu_date`),
  INDEX `idx_hostel` (`hostel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hostel_visitors
-- Visitor log
CREATE TABLE IF NOT EXISTS `hostel_visitors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hostel_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `visitor_name` VARCHAR(100) NOT NULL,
  `visitor_contact` VARCHAR(20),
  `relationship` VARCHAR(50),
  `id_proof_type` VARCHAR(50),
  `id_proof_number` VARCHAR(50),
  `visit_date` DATE NOT NULL,
  `in_time` TIME NOT NULL,
  `out_time` TIME NULL,
  `purpose` TEXT,
  `approved_by` INT(11),
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`hostel_id`) REFERENCES `hostels`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  INDEX `idx_visit_date` (`visit_date`),
  INDEX `idx_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hostel_complaints
-- Hostel complaints and issues
CREATE TABLE IF NOT EXISTS `hostel_complaints` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `hostel_id` INT(11) NOT NULL,
  `student_id` INT(11) NOT NULL,
  `complaint_date` DATE NOT NULL,
  `complaint_type` ENUM('maintenance', 'cleanliness', 'food', 'security', 'noise', 'other') NOT NULL,
  `complaint_text` TEXT NOT NULL,
  `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
  `status` ENUM('pending', 'in_progress', 'resolved', 'closed', 'rejected') DEFAULT 'pending',
  `resolved_date` DATE NULL,
  `resolution_text` TEXT,
  `resolved_by` INT(11) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`hostel_id`) REFERENCES `hostels`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  INDEX `idx_status` (`status`),
  INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. HR & STAFF MANAGEMENT MODULE
-- ============================================================================

-- Table: departments
-- School departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `department_name` VARCHAR(100) NOT NULL,
  `department_code` VARCHAR(20) UNIQUE NOT NULL,
  `head_of_department` INT(11) NULL,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_department_code` (`department_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: staff
-- Non-teaching staff management
CREATE TABLE IF NOT EXISTS `staff` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `employee_id` VARCHAR(50) UNIQUE NOT NULL,
  `department_id` INT(11) NOT NULL,
  `designation` VARCHAR(100) NOT NULL,
  `employment_type` ENUM('permanent', 'temporary', 'contract', 'part_time') DEFAULT 'permanent',
  `date_of_joining` DATE NOT NULL,
  `date_of_leaving` DATE NULL,
  `qualification` VARCHAR(255),
  `experience_years` INT(3),
  `contact_number` VARCHAR(20),
  `emergency_contact` VARCHAR(20),
  `address` TEXT,
  `blood_group` VARCHAR(5),
  `photo` VARCHAR(255),
  `documents` TEXT COMMENT 'JSON array of document paths',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE RESTRICT,
  INDEX `idx_employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: staff_attendance
-- Staff attendance tracking
CREATE TABLE IF NOT EXISTS `staff_attendance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `staff_id` INT(11) NOT NULL COMMENT 'Can reference teachers or staff table',
  `staff_type` ENUM('teacher', 'staff') NOT NULL,
  `attendance_date` DATE NOT NULL,
  `check_in_time` TIME,
  `check_out_time` TIME,
  `status` ENUM('present', 'absent', 'half_day', 'late', 'on_leave') DEFAULT 'present',
  `working_hours` DECIMAL(4,2),
  `remarks` TEXT,
  `marked_by` INT(11),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_staff_date` (`staff_id`, `staff_type`, `attendance_date`),
  INDEX `idx_attendance_date` (`attendance_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: leave_types
-- Types of leaves available
CREATE TABLE IF NOT EXISTS `leave_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `leave_name` VARCHAR(50) NOT NULL,
  `leave_code` VARCHAR(10) UNIQUE NOT NULL,
  `total_days` INT(3) NOT NULL COMMENT 'Annual quota',
  `applicable_to` ENUM('all', 'teaching', 'non_teaching') DEFAULT 'all',
  `is_paid` TINYINT(1) DEFAULT 1,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: leave_applications
-- Leave application management
CREATE TABLE IF NOT EXISTS `leave_applications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `staff_id` INT(11) NOT NULL,
  `staff_type` ENUM('teacher', 'staff') NOT NULL,
  `leave_type_id` INT(11) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `total_days` INT(3) NOT NULL,
  `reason` TEXT NOT NULL,
  `substitute_arrangement` TEXT,
  `contact_during_leave` VARCHAR(20),
  `status` ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
  `approved_by` INT(11) NULL,
  `approval_date` DATE NULL,
  `approval_remarks` TEXT,
  `applied_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types`(`id`) ON DELETE RESTRICT,
  INDEX `idx_staff` (`staff_id`, `staff_type`),
  INDEX `idx_dates` (`start_date`, `end_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: performance_reviews
-- Staff performance evaluation
CREATE TABLE IF NOT EXISTS `performance_reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `staff_id` INT(11) NOT NULL,
  `staff_type` ENUM('teacher', 'staff') NOT NULL,
  `review_period_start` DATE NOT NULL,
  `review_period_end` DATE NOT NULL,
  `reviewer_id` INT(11) NOT NULL,
  `overall_rating` DECIMAL(3,2) COMMENT '1-5 scale',
  `punctuality_rating` DECIMAL(3,2),
  `work_quality_rating` DECIMAL(3,2),
  `communication_rating` DECIMAL(3,2),
  `teamwork_rating` DECIMAL(3,2),
  `achievements` TEXT,
  `areas_of_improvement` TEXT,
  `goals_for_next_period` TEXT,
  `reviewer_comments` TEXT,
  `employee_comments` TEXT,
  `review_date` DATE NOT NULL,
  `status` ENUM('draft', 'submitted', 'acknowledged', 'finalized') DEFAULT 'draft',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_staff` (`staff_id`, `staff_type`),
  INDEX `idx_review_period` (`review_period_start`, `review_period_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. INVENTORY & ASSET MANAGEMENT MODULE
-- ============================================================================

-- Table: asset_categories
-- Categories for school assets
CREATE TABLE IF NOT EXISTS `asset_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `category_code` VARCHAR(20) UNIQUE NOT NULL,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: assets
-- School asset tracking
CREATE TABLE IF NOT EXISTS `assets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_code` VARCHAR(50) UNIQUE NOT NULL,
  `asset_name` VARCHAR(100) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `description` TEXT,
  `manufacturer` VARCHAR(100),
  `model_number` VARCHAR(50),
  `serial_number` VARCHAR(50),
  `purchase_date` DATE,
  `purchase_cost` DECIMAL(10,2),
  `supplier_name` VARCHAR(100),
  `warranty_expiry` DATE,
  `location` VARCHAR(255) COMMENT 'Department/Room where located',
  `assigned_to` INT(11) NULL COMMENT 'Staff ID if assigned',
  `condition` ENUM('excellent', 'good', 'fair', 'poor', 'damaged') DEFAULT 'good',
  `status` ENUM('in_use', 'available', 'maintenance', 'retired', 'lost') DEFAULT 'available',
  `photo` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `asset_categories`(`id`) ON DELETE RESTRICT,
  INDEX `idx_asset_code` (`asset_code`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_items
-- Consumable inventory items
CREATE TABLE IF NOT EXISTS `inventory_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_code` VARCHAR(50) UNIQUE NOT NULL,
  `item_name` VARCHAR(100) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `description` TEXT,
  `unit_of_measure` VARCHAR(20) COMMENT 'pieces, kg, liters, etc.',
  `reorder_level` INT(11) DEFAULT 0 COMMENT 'Minimum quantity before reorder',
  `current_stock` INT(11) DEFAULT 0,
  `unit_price` DECIMAL(10,2),
  `location` VARCHAR(255),
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `asset_categories`(`id`) ON DELETE RESTRICT,
  INDEX `idx_item_code` (`item_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stock_transactions
-- Inventory stock in/out tracking
CREATE TABLE IF NOT EXISTS `stock_transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `transaction_type` ENUM('in', 'out', 'adjustment') NOT NULL,
  `transaction_date` DATE NOT NULL,
  `quantity` INT(11) NOT NULL,
  `unit_price` DECIMAL(10,2),
  `total_amount` DECIMAL(10,2),
  `supplier_name` VARCHAR(100),
  `invoice_number` VARCHAR(50),
  `purpose` TEXT,
  `issued_to` VARCHAR(100),
  `performed_by` INT(11) NOT NULL,
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`item_id`) REFERENCES `inventory_items`(`id`) ON DELETE CASCADE,
  INDEX `idx_transaction_date` (`transaction_date`),
  INDEX `idx_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_orders
-- Purchase order management
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `po_number` VARCHAR(50) UNIQUE NOT NULL,
  `po_date` DATE NOT NULL,
  `supplier_name` VARCHAR(100) NOT NULL,
  `supplier_contact` VARCHAR(20),
  `supplier_address` TEXT,
  `items` TEXT NOT NULL COMMENT 'JSON array of items',
  `subtotal` DECIMAL(10,2) NOT NULL,
  `tax_amount` DECIMAL(10,2) DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `delivery_date` DATE,
  `payment_terms` VARCHAR(100),
  `status` ENUM('draft', 'submitted', 'approved', 'received', 'cancelled') DEFAULT 'draft',
  `approved_by` INT(11) NULL,
  `created_by` INT(11) NOT NULL,
  `remarks` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_po_number` (`po_number`),
  INDEX `idx_status` (`status`),
  INDEX `idx_po_date` (`po_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: suppliers
-- Supplier master data
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `supplier_code` VARCHAR(20) UNIQUE NOT NULL,
  `supplier_name` VARCHAR(100) NOT NULL,
  `contact_person` VARCHAR(100),
  `contact_number` VARCHAR(20),
  `email` VARCHAR(100),
  `address` TEXT,
  `city` VARCHAR(50),
  `state` VARCHAR(50),
  `pincode` VARCHAR(10),
  `gst_number` VARCHAR(20),
  `pan_number` VARCHAR(20),
  `bank_details` TEXT,
  `payment_terms` VARCHAR(100),
  `credit_limit` DECIMAL(10,2),
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_supplier_code` (`supplier_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Insert Default/Sample Data
-- ============================================================================

-- Default Grading Scheme
INSERT INTO `grading_schemes` (`scheme_name`, `min_percentage`, `max_percentage`, `grade`, `grade_point`, `description`, `is_passing`) VALUES
('Default Scheme', 90.00, 100.00, 'A+', 4.00, 'Outstanding', 1),
('Default Scheme', 80.00, 89.99, 'A', 3.70, 'Excellent', 1),
('Default Scheme', 70.00, 79.99, 'B+', 3.30, 'Very Good', 1),
('Default Scheme', 60.00, 69.99, 'B', 3.00, 'Good', 1),
('Default Scheme', 50.00, 59.99, 'C+', 2.70, 'Above Average', 1),
('Default Scheme', 40.00, 49.99, 'C', 2.00, 'Average', 1),
('Default Scheme', 33.00, 39.99, 'D', 1.00, 'Pass', 1),
('Default Scheme', 0.00, 32.99, 'F', 0.00, 'Fail', 0);

-- Default Leave Types
INSERT INTO `leave_types` (`leave_name`, `leave_code`, `total_days`, `applicable_to`, `is_paid`) VALUES
('Casual Leave', 'CL', 12, 'all', 1),
('Sick Leave', 'SL', 12, 'all', 1),
('Earned Leave', 'EL', 15, 'all', 1),
('Maternity Leave', 'ML', 180, 'all', 1),
('Paternity Leave', 'PL', 15, 'all', 1),
('Unpaid Leave', 'UL', 30, 'all', 0);

-- Default Asset Categories
INSERT INTO `asset_categories` (`category_name`, `category_code`, `description`) VALUES
('Computer Equipment', 'COMP', 'Computers, laptops, printers'),
('Furniture', 'FURN', 'Tables, chairs, cabinets'),
('Sports Equipment', 'SPORT', 'Sports items and equipment'),
('Laboratory Equipment', 'LAB', 'Science lab equipment'),
('Audio Visual', 'AV', 'Projectors, speakers, screens'),
('Vehicles', 'VEH', 'School vehicles'),
('Office Supplies', 'OFF', 'Stationery and office items'),
('Cleaning Supplies', 'CLEAN', 'Cleaning materials');

-- Default Departments
INSERT INTO `departments` (`department_name`, `department_code`, `description`) VALUES
('Administration', 'ADMIN', 'Administrative department'),
('Academics', 'ACAD', 'Academic affairs'),
('Finance', 'FIN', 'Finance and accounts'),
('IT Department', 'IT', 'Information technology'),
('Library', 'LIB', 'Library services'),
('Transport', 'TRANS', 'Transport management'),
('Maintenance', 'MAINT', 'Building and facility maintenance'),
('Sports', 'SPORT', 'Sports and physical education');

COMMIT;

-- ============================================================================
-- End of School Management System Database Schema
-- ============================================================================
