-- Verdant SMS - Complete Database Schema for 42 Modules
-- School Management System Maximum Edition

-- HR & Payroll Tables
CREATE TABLE IF NOT EXISTS `leave_requests` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `leave_type` ENUM('sick', 'casual', 'vacation', 'emergency') DEFAULT 'casual',
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `reason` TEXT,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `approved_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

CREATE TABLE IF NOT EXISTS `payroll_records` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `month` INT NOT NULL,
    `year` INT NOT NULL,
    `basic_salary` DECIMAL(10, 2),
    `allowances` DECIMAL(10, 2) DEFAULT 0,
    `deductions` DECIMAL(10, 2) DEFAULT 0,
    `net_salary` DECIMAL(10, 2),
    `payment_date` DATE,
    `status` ENUM('pending', 'processed', 'paid') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- Inventory & Asset Tracking
CREATE TABLE IF NOT EXISTS `inventory_assets` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `asset_name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100),
    `purchased_date` DATE,
    `purchase_value` DECIMAL(10, 2),
    `current_value` DECIMAL(10, 2),
    `location` VARCHAR(255),
    `status` ENUM('active', 'maintenance', 'retired') DEFAULT 'active',
    `qr_code` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Health & Medical Records
CREATE TABLE IF NOT EXISTS `health_visits` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `visit_date` DATETIME NOT NULL,
    `ailment` VARCHAR(255),
    `symptoms` TEXT,
    `action_taken` VARCHAR(255),
    `referred_to_doctor` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `health_checkups` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `checkup_date` DATE NOT NULL,
    `height_cm` DECIMAL(5, 2),
    `weight_kg` DECIMAL(5, 2),
    `blood_pressure` VARCHAR(20),
    `vision` VARCHAR(50),
    `notes` TEXT,
    `status` ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `health_vaccinations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `vaccine_name` VARCHAR(255) NOT NULL,
    `due_date` DATE,
    `administered_date` DATE,
    `status` ENUM('pending', 'completed', 'skipped') DEFAULT 'pending',
    `notes` TEXT,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `health_medications` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `medication_name` VARCHAR(255) NOT NULL,
    `dosage` VARCHAR(100),
    `frequency` VARCHAR(100),
    `start_date` DATE,
    `end_date` DATE,
    `status` ENUM('active', 'completed', 'discontinued') DEFAULT 'active',
    `prescribed_by` VARCHAR(255),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

-- Discipline & Behavior
CREATE TABLE IF NOT EXISTS `discipline_incidents` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `incident_date` DATE NOT NULL,
    `incident_type` VARCHAR(100),
    `description` TEXT,
    `severity` ENUM('low', 'medium', 'high') DEFAULT 'medium',
    `action_taken` TEXT,
    `status` ENUM('pending', 'resolved', 'escalated') DEFAULT 'pending',
    `reported_by` INT,
    `resolved_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `behavior_points` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `points` INT DEFAULT 0,
    `point_type` ENUM('merit', 'demerit') NOT NULL,
    `reason` VARCHAR(255),
    `awarded_by` INT,
    `awarded_date` DATE NOT NULL,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

-- Canteen & POS
CREATE TABLE IF NOT EXISTS `canteen_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `item_name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100),
    `price` DECIMAL(8, 2) NOT NULL,
    `is_available` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `canteen_inventory` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `item_id` INT NOT NULL,
    `stock_quantity` INT DEFAULT 0,
    `reorder_level` INT DEFAULT 10,
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`item_id`) REFERENCES `canteen_items`(`id`)
);

CREATE TABLE IF NOT EXISTS `canteen_wallets` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `balance` DECIMAL(10, 2) DEFAULT 0,
    `last_topup_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

CREATE TABLE IF NOT EXISTS `canteen_transactions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` ENUM('cash', 'wallet', 'card') DEFAULT 'wallet',
    `transaction_date` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `canteen_transaction_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `transaction_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `amount` DECIMAL(8, 2) NOT NULL,
    FOREIGN KEY (`transaction_id`) REFERENCES `canteen_transactions`(`id`),
    FOREIGN KEY (`item_id`) REFERENCES `canteen_items`(`id`)
);

-- Transport System
CREATE TABLE IF NOT EXISTS `transport_vehicles` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `vehicle_number` VARCHAR(50) NOT NULL UNIQUE,
    `vehicle_type` VARCHAR(50),
    `capacity` INT,
    `status` ENUM('active', 'maintenance', 'retired') DEFAULT 'active',
    `assigned_route_id` INT,
    `driver_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `transport_routes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `route_name` VARCHAR(255) NOT NULL,
    `route_code` VARCHAR(50),
    `start_location` VARCHAR(255),
    `end_location` VARCHAR(255),
    `total_stops` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `transport_drivers` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `license_number` VARCHAR(100),
    `license_expiry` DATE,
    `phone_number` VARCHAR(20),
    `is_active` BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

CREATE TABLE IF NOT EXISTS `transport_assignments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `route_id` INT NOT NULL,
    `pickup_location` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `assigned_date` DATE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`),
    FOREIGN KEY (`route_id`) REFERENCES `transport_routes`(`id`)
);

CREATE TABLE IF NOT EXISTS `transport_maintenance` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `vehicle_id` INT NOT NULL,
    `maintenance_type` VARCHAR(100),
    `scheduled_date` DATE,
    `completed_date` DATE,
    `cost` DECIMAL(10, 2),
    `status` ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (`vehicle_id`) REFERENCES `transport_vehicles`(`id`)
);

CREATE TABLE IF NOT EXISTS `transport_expenses` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `vehicle_id` INT,
    `expense_type` VARCHAR(100),
    `amount` DECIMAL(10, 2),
    `expense_date` DATE,
    `notes` TEXT,
    FOREIGN KEY (`vehicle_id`) REFERENCES `transport_vehicles`(`id`)
);

-- Hostel Management
CREATE TABLE IF NOT EXISTS `hostel_rooms` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `room_number` VARCHAR(50) NOT NULL,
    `block_name` VARCHAR(100),
    `floor` INT,
    `capacity` INT DEFAULT 4,
    `room_type` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `hostel_allocations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `room_id` INT NOT NULL,
    `allocated_on` DATE NOT NULL,
    `vacated_on` DATE,
    `is_active` BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`),
    FOREIGN KEY (`room_id`) REFERENCES `hostel_rooms`(`id`)
);

CREATE TABLE IF NOT EXISTS `mess_attendance` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `meal_type` ENUM('breakfast', 'lunch', 'dinner') NOT NULL,
    `attendance_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `hostel_leave_requests` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `reason` TEXT,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `hostel_expenses` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `expense_type` VARCHAR(100),
    `amount` DECIMAL(10, 2),
    `expense_date` DATE,
    `description` TEXT
);

-- Career Guidance & Counseling
CREATE TABLE IF NOT EXISTS `counseling_sessions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `session_date` DATE NOT NULL,
    `session_time` TIME,
    `concern_type` VARCHAR(100),
    `notes` TEXT,
    `status` ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    `counselor_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `career_assessments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `assessment_type` VARCHAR(100),
    `assessment_date` DATE,
    `results` JSON,
    `status` ENUM('pending', 'completed') DEFAULT 'pending',
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

-- Events & Activities
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_name` VARCHAR(255) NOT NULL,
    `event_type` VARCHAR(100),
    `event_date` DATE NOT NULL,
    `start_time` TIME,
    `end_time` TIME,
    `location` VARCHAR(255),
    `description` TEXT,
    `status` ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `event_participants` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `role` VARCHAR(100),
    `performance` TEXT,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

-- Gamification & House System
CREATE TABLE IF NOT EXISTS `gamification_houses` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `house_name` VARCHAR(100) NOT NULL,
    `house_color` VARCHAR(50),
    `house_motto` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `gamification_points` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `house_id` INT,
    `total_points` INT DEFAULT 0,
    `badges_earned` INT DEFAULT 0,
    `current_level` INT DEFAULT 1,
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`),
    FOREIGN KEY (`house_id`) REFERENCES `gamification_houses`(`id`)
);

CREATE TABLE IF NOT EXISTS `gamification_badges` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `badge_name` VARCHAR(100) NOT NULL,
    `badge_icon` VARCHAR(255),
    `points_required` INT DEFAULT 0,
    `description` TEXT
);

-- Certificates & Documents
CREATE TABLE IF NOT EXISTS `certificates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `certificate_type` VARCHAR(100) NOT NULL,
    `issue_date` DATE NOT NULL,
    `certificate_number` VARCHAR(100) UNIQUE,
    `template_id` INT,
    `file_path` VARCHAR(255),
    `blockchain_hash` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `certificate_templates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `template_name` VARCHAR(255) NOT NULL,
    `template_type` VARCHAR(100),
    `html_content` TEXT,
    `is_active` BOOLEAN DEFAULT TRUE
);

-- Alumni Portal
CREATE TABLE IF NOT EXISTS `alumni` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `graduation_year` INT,
    `current_occupation` VARCHAR(255),
    `company_name` VARCHAR(255),
    `linkedin_profile` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE IF NOT EXISTS `alumni_donations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `alumni_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `donation_date` DATE NOT NULL,
    `purpose` TEXT,
    `payment_method` VARCHAR(50),
    FOREIGN KEY (`alumni_id`) REFERENCES `alumni`(`id`)
);

-- Multi-School Support
CREATE TABLE IF NOT EXISTS `schools` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `school_name` VARCHAR(255) NOT NULL,
    `school_code` VARCHAR(50) UNIQUE,
    `address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `country` VARCHAR(100),
    `phone` VARCHAR(20),
    `email` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sustainability Tracking
CREATE TABLE IF NOT EXISTS `sustainability_metrics` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `metric_date` DATE NOT NULL,
    `energy_consumption_kwh` DECIMAL(10, 2),
    `water_consumption_liters` DECIMAL(10, 2),
    `waste_recycled_kg` DECIMAL(10, 2),
    `carbon_footprint_kg` DECIMAL(10, 2),
    `trees_planted` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- API & Integrations Log
CREATE TABLE IF NOT EXISTS `api_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `endpoint` VARCHAR(255),
    `method` VARCHAR(10),
    `request_data` JSON,
    `response_data` JSON,
    `status_code` INT,
    `ip_address` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Sessions (for tracking active users)
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `session_token` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(50),
    `user_agent` VARCHAR(255),
    `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);
