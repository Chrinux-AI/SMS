-- Nigerian Education System - Complete Schema
-- Verdant SMS v3.0 — 12 December 2025
-- Includes: Grading, Terms, Sessions, Subjects, Fees

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. NIGERIAN GRADING SYSTEM (A1 - F9)
-- ============================================
DROP TABLE IF EXISTS grading_scales;
CREATE TABLE grading_scales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grade VARCHAR(5) NOT NULL,
    min_score INT NOT NULL,
    max_score INT NOT NULL,
    description VARCHAR(50) NOT NULL,
    grade_point DECIMAL(3,2) DEFAULT NULL,
    is_passing BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO grading_scales (grade, min_score, max_score, description, grade_point, is_passing) VALUES
('A1', 75, 100, 'Excellent', 4.00, TRUE),
('B2', 70, 74, 'Very Good', 3.50, TRUE),
('B3', 65, 69, 'Good', 3.25, TRUE),
('C4', 60, 64, 'Credit', 3.00, TRUE),
('C5', 55, 59, 'Credit', 2.75, TRUE),
('C6', 50, 54, 'Credit', 2.50, TRUE),
('D7', 45, 49, 'Pass', 2.00, TRUE),
('E8', 40, 44, 'Pass', 1.50, TRUE),
('F9', 0, 39, 'Fail', 0.00, FALSE);

-- ============================================
-- 2. ACADEMIC SESSIONS (e.g., 2024/2025)
-- ============================================
DROP TABLE IF EXISTS academic_sessions;
CREATE TABLE academic_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL, -- '2024/2025'
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed current session
INSERT INTO academic_sessions (name, start_date, end_date, is_current) VALUES
('2024/2025', '2024-09-09', '2025-07-31', TRUE),
('2025/2026', '2025-09-08', '2026-07-31', FALSE);

-- ============================================
-- 3. ACADEMIC TERMS (3 Terms per Session)
-- ============================================
DROP TABLE IF EXISTS academic_terms;
CREATE TABLE academic_terms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    term_name ENUM('First Term', 'Second Term', 'Third Term') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE
);

-- Seed 2024/2025 terms
INSERT INTO academic_terms (session_id, term_name, start_date, end_date, is_current) VALUES
(1, 'First Term', '2024-09-09', '2024-12-20', FALSE),
(1, 'Second Term', '2025-01-06', '2025-04-11', TRUE),
(1, 'Third Term', '2025-04-28', '2025-07-31', FALSE);

-- Seed 2025/2026 terms
INSERT INTO academic_terms (session_id, term_name, start_date, end_date, is_current) VALUES
(2, 'First Term', '2025-09-08', '2025-12-19', FALSE),
(2, 'Second Term', '2026-01-05', '2026-04-10', FALSE),
(2, 'Third Term', '2026-04-27', '2026-07-31', FALSE);

-- ============================================
-- 4. SUBJECTS (Nigerian Curriculum)
-- ============================================
DROP TABLE IF EXISTS subjects;
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) DEFAULT NULL,
    section ENUM('primary', 'junior_secondary', 'senior_secondary', 'all') DEFAULT 'all',
    category ENUM('core', 'elective', 'vocational') DEFAULT 'core',
    track ENUM('science', 'arts', 'commercial', 'general') DEFAULT 'general',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PRIMARY SUBJECTS
INSERT INTO subjects (name, code, section, category, track) VALUES
('English Language', 'ENG', 'primary', 'core', 'general'),
('Mathematics', 'MTH', 'primary', 'core', 'general'),
('Basic Science', 'BSC', 'primary', 'core', 'general'),
('Social Studies', 'SST', 'primary', 'core', 'general'),
('Yoruba', 'YOR', 'primary', 'core', 'general'),
('Igbo', 'IGB', 'primary', 'elective', 'general'),
('Hausa', 'HAU', 'primary', 'elective', 'general'),
('Christian Religious Studies', 'CRS', 'primary', 'core', 'general'),
('Islamic Religious Studies', 'IRS', 'primary', 'elective', 'general'),
('Agricultural Science', 'AGR', 'primary', 'core', 'general'),
('Physical & Health Education', 'PHE', 'primary', 'core', 'general'),
('Creative Arts', 'CRA', 'primary', 'core', 'general'),
('Computer Studies', 'ICT', 'primary', 'core', 'general'),
('Music', 'MUS', 'primary', 'elective', 'general'),
('Fine Arts', 'FAR', 'primary', 'elective', 'general');

-- JUNIOR SECONDARY (JSS) SUBJECTS
INSERT INTO subjects (name, code, section, category, track) VALUES
('English Language', 'ENG', 'junior_secondary', 'core', 'general'),
('Mathematics', 'MTH', 'junior_secondary', 'core', 'general'),
('Basic Science', 'BSC', 'junior_secondary', 'core', 'general'),
('Basic Technology', 'BTC', 'junior_secondary', 'core', 'general'),
('Social Studies', 'SST', 'junior_secondary', 'core', 'general'),
('Civic Education', 'CVE', 'junior_secondary', 'core', 'general'),
('Business Studies', 'BSS', 'junior_secondary', 'core', 'general'),
('Agricultural Science', 'AGR', 'junior_secondary', 'core', 'general'),
('Home Economics', 'HEC', 'junior_secondary', 'core', 'general'),
('Physical & Health Education', 'PHE', 'junior_secondary', 'core', 'general'),
('Nigerian Language', 'NGL', 'junior_secondary', 'core', 'general'),
('Christian Religious Studies', 'CRS', 'junior_secondary', 'core', 'general'),
('Islamic Religious Studies', 'IRS', 'junior_secondary', 'elective', 'general'),
('French', 'FRN', 'junior_secondary', 'elective', 'general'),
('Computer Studies', 'ICT', 'junior_secondary', 'core', 'general');

-- SENIOR SECONDARY (SSS) SUBJECTS - Science Track
INSERT INTO subjects (name, code, section, category, track) VALUES
('English Language', 'ENG', 'senior_secondary', 'core', 'general'),
('Mathematics', 'MTH', 'senior_secondary', 'core', 'general'),
('Physics', 'PHY', 'senior_secondary', 'core', 'science'),
('Chemistry', 'CHM', 'senior_secondary', 'core', 'science'),
('Biology', 'BIO', 'senior_secondary', 'core', 'science'),
('Further Mathematics', 'FMT', 'senior_secondary', 'elective', 'science'),
('Agricultural Science', 'AGR', 'senior_secondary', 'elective', 'science'),
('Technical Drawing', 'TDR', 'senior_secondary', 'elective', 'science'),
('Data Processing', 'DPR', 'senior_secondary', 'elective', 'science');

-- SENIOR SECONDARY (SSS) SUBJECTS - Arts Track
INSERT INTO subjects (name, code, section, category, track) VALUES
('Literature in English', 'LIT', 'senior_secondary', 'core', 'arts'),
('Government', 'GOV', 'senior_secondary', 'core', 'arts'),
('History', 'HIS', 'senior_secondary', 'elective', 'arts'),
('Geography', 'GEO', 'senior_secondary', 'elective', 'arts'),
('Christian Religious Studies', 'CRS', 'senior_secondary', 'elective', 'arts'),
('Islamic Religious Studies', 'IRS', 'senior_secondary', 'elective', 'arts'),
('French', 'FRN', 'senior_secondary', 'elective', 'arts'),
('Music', 'MUS', 'senior_secondary', 'elective', 'arts'),
('Fine Arts', 'FAR', 'senior_secondary', 'elective', 'arts');

-- SENIOR SECONDARY (SSS) SUBJECTS - Commercial Track
INSERT INTO subjects (name, code, section, category, track) VALUES
('Economics', 'ECO', 'senior_secondary', 'core', 'commercial'),
('Commerce', 'COM', 'senior_secondary', 'core', 'commercial'),
('Financial Accounting', 'ACC', 'senior_secondary', 'core', 'commercial'),
('Office Practice', 'OFP', 'senior_secondary', 'elective', 'commercial'),
('Insurance', 'INS', 'senior_secondary', 'elective', 'commercial'),
('Typewriting', 'TYP', 'senior_secondary', 'elective', 'commercial');

-- Common SSS subjects
INSERT INTO subjects (name, code, section, category, track) VALUES
('Civic Education', 'CVE', 'senior_secondary', 'core', 'general'),
('Computer Studies', 'ICT', 'senior_secondary', 'core', 'general');

-- ============================================
-- 5. FEE CATEGORIES
-- ============================================
DROP TABLE IF EXISTS fee_categories;
CREATE TABLE fee_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    frequency ENUM('termly', 'annually', 'one_time') DEFAULT 'termly',
    is_mandatory BOOLEAN DEFAULT TRUE,
    applies_to ENUM('primary', 'junior_secondary', 'senior_secondary', 'all') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO fee_categories (name, description, frequency, is_mandatory, applies_to) VALUES
('Tuition Fee', 'Main school fees per term', 'termly', TRUE, 'all'),
('Development Levy', 'School development and maintenance', 'annually', TRUE, 'all'),
('PTA Levy', 'Parent Teacher Association dues', 'annually', TRUE, 'all'),
('Sports/Games Fee', 'Sports equipment and activities', 'termly', TRUE, 'all'),
('Library Fee', 'Library resources and maintenance', 'termly', TRUE, 'all'),
('Laboratory Fee', 'Science laboratory equipment and materials', 'termly', TRUE, 'junior_secondary'),
('Laboratory Fee', 'Science laboratory equipment and materials', 'termly', TRUE, 'senior_secondary'),
('ICT Fee', 'Computer lab and internet services', 'termly', TRUE, 'all'),
('Examination Fee', 'Internal examination materials', 'termly', TRUE, 'all'),
('WAEC Fee', 'West African Examination Council', 'one_time', TRUE, 'senior_secondary'),
('NECO Fee', 'National Examination Council', 'one_time', FALSE, 'senior_secondary'),
('Uniform Fee', 'School uniform (new students)', 'one_time', FALSE, 'all'),
('ID Card Fee', 'Student identification card', 'one_time', FALSE, 'all'),
('Medical Fee', 'School clinic and first aid', 'termly', FALSE, 'all'),
('Transport Fee', 'School bus service (optional)', 'termly', FALSE, 'all');

-- ============================================
-- 6. FEE STRUCTURES (Amount per Section)
-- ============================================
DROP TABLE IF EXISTS fee_structures;
CREATE TABLE fee_structures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    term_id INT DEFAULT NULL,
    category_id INT NOT NULL,
    section ENUM('primary', 'junior_secondary', 'senior_secondary') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(5) DEFAULT 'NGN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES fee_categories(id) ON DELETE CASCADE
);

-- Sample fee structure for 2024/2025 First Term (Primary)
INSERT INTO fee_structures (session_id, term_id, category_id, section, amount) VALUES
(1, 1, 1, 'primary', 50000.00),      -- Tuition
(1, NULL, 2, 'primary', 15000.00),   -- Dev Levy (annual)
(1, NULL, 3, 'primary', 5000.00),    -- PTA (annual)
(1, 1, 4, 'primary', 3000.00),       -- Sports
(1, 1, 5, 'primary', 2000.00),       -- Library
(1, 1, 8, 'primary', 5000.00);       -- ICT

-- Sample fee structure for 2024/2025 First Term (JSS)
INSERT INTO fee_structures (session_id, term_id, category_id, section, amount) VALUES
(1, 1, 1, 'junior_secondary', 65000.00),  -- Tuition
(1, NULL, 2, 'junior_secondary', 20000.00), -- Dev Levy
(1, NULL, 3, 'junior_secondary', 5000.00),  -- PTA
(1, 1, 4, 'junior_secondary', 4000.00),   -- Sports
(1, 1, 5, 'junior_secondary', 3000.00),   -- Library
(1, 1, 6, 'junior_secondary', 8000.00),   -- Lab
(1, 1, 8, 'junior_secondary', 7000.00);   -- ICT

-- Sample fee structure for 2024/2025 First Term (SSS)
INSERT INTO fee_structures (session_id, term_id, category_id, section, amount) VALUES
(1, 1, 1, 'senior_secondary', 80000.00),  -- Tuition
(1, NULL, 2, 'senior_secondary', 25000.00), -- Dev Levy
(1, NULL, 3, 'senior_secondary', 5000.00),  -- PTA
(1, 1, 4, 'senior_secondary', 5000.00),   -- Sports
(1, 1, 5, 'senior_secondary', 4000.00),   -- Library
(1, 1, 7, 'senior_secondary', 12000.00),  -- Lab (SSS)
(1, 1, 8, 'senior_secondary', 10000.00),  -- ICT
(1, NULL, 10, 'senior_secondary', 35000.00); -- WAEC

-- ============================================
-- 7. EXTERNAL EXAMS (WAEC/NECO/JAMB)
-- ============================================
DROP TABLE IF EXISTS external_exams;
CREATE TABLE external_exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    exam_type ENUM('WAEC', 'NECO', 'JAMB', 'GCE', 'NABTEB') NOT NULL,
    exam_year INT NOT NULL,
    exam_number VARCHAR(50) DEFAULT NULL,
    results JSON DEFAULT NULL, -- {'English': 'B2', 'Mathematics': 'A1', ...}
    aggregate INT DEFAULT NULL,
    result_slip VARCHAR(255) DEFAULT NULL, -- File path to uploaded result
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student (student_id),
    INDEX idx_exam_type (exam_type, exam_year)
);

-- ============================================
-- 8. STUDENT GRADES/RESULTS
-- ============================================
DROP TABLE IF EXISTS student_results;
CREATE TABLE student_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_id INT NOT NULL,
    term_id INT NOT NULL,
    session_id INT NOT NULL,
    ca1_score DECIMAL(5,2) DEFAULT 0, -- Continuous Assessment 1 (10%)
    ca2_score DECIMAL(5,2) DEFAULT 0, -- Continuous Assessment 2 (10%)
    ca3_score DECIMAL(5,2) DEFAULT 0, -- Continuous Assessment 3 (10%)
    exam_score DECIMAL(5,2) DEFAULT 0, -- Exam (70%)
    total_score DECIMAL(5,2) GENERATED ALWAYS AS (ca1_score + ca2_score + ca3_score + exam_score) STORED,
    grade VARCHAR(5) DEFAULT NULL,
    remark VARCHAR(100) DEFAULT NULL,
    position_in_subject INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_result (student_id, subject_id, term_id, session_id)
);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify all tables
SELECT 'grading_scales' AS 'Table', COUNT(*) AS 'Rows' FROM grading_scales
UNION ALL SELECT 'academic_sessions', COUNT(*) FROM academic_sessions
UNION ALL SELECT 'academic_terms', COUNT(*) FROM academic_terms
UNION ALL SELECT 'subjects', COUNT(*) FROM subjects
UNION ALL SELECT 'fee_categories', COUNT(*) FROM fee_categories
UNION ALL SELECT 'fee_structures', COUNT(*) FROM fee_structures;
