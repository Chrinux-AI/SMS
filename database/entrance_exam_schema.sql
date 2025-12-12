-- ============================================
-- VERDANT SMS v3.0 — ENTRANCE EXAM MODULE
-- Full schema for exam system
-- ============================================

-- Exam Registrations (public sign-up for entrance exam)
CREATE TABLE IF NOT EXISTS exam_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    parent_name VARCHAR(255) NOT NULL,
    parent_phone VARCHAR(20) NOT NULL,
    date_of_birth DATE,
    previous_school VARCHAR(255),
    grade_applying_for VARCHAR(50),
    status ENUM('pending', 'approved', 'declined', 'exam_sent', 'exam_completed') DEFAULT 'pending',
    exam_link_sent_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Entrance Exams (created by Admin)
CREATE TABLE IF NOT EXISTS entrance_exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    grade_level VARCHAR(50),
    duration_minutes INT DEFAULT 60,
    pass_mark INT DEFAULT 50,
    total_marks INT DEFAULT 100,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Questions (MCQ + Subjective)
CREATE TABLE IF NOT EXISTS exam_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'subjective') DEFAULT 'mcq',
    option_a VARCHAR(500),
    option_b VARCHAR(500),
    option_c VARCHAR(500),
    option_d VARCHAR(500),
    correct_answer CHAR(1),  -- A, B, C, D for MCQ
    marks INT DEFAULT 1,
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES entrance_exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Attempts (student taking exam)
CREATE TABLE IF NOT EXISTS exam_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    exam_id INT NOT NULL,
    access_token VARCHAR(64) UNIQUE,
    started_at DATETIME,
    submitted_at DATETIME,
    time_remaining_seconds INT,
    score INT DEFAULT 0,
    total_marks INT,
    percentage DECIMAL(5,2),
    passed TINYINT(1) DEFAULT 0,
    entrance_id VARCHAR(50) UNIQUE,  -- Generated after passing: VERDANT-EXAM-XXXXX
    status ENUM('not_started', 'in_progress', 'submitted', 'graded') DEFAULT 'not_started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES exam_registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES entrance_exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Answers (student responses)
CREATE TABLE IF NOT EXISTS exam_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer CHAR(1),  -- A, B, C, D for MCQ
    text_answer TEXT,  -- For subjective
    is_correct TINYINT(1),
    marks_awarded INT DEFAULT 0,
    ai_feedback TEXT,  -- Grok AI feedback for subjective
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES exam_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES exam_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes for performance
CREATE INDEX idx_exam_reg_status ON exam_registrations(status);
CREATE INDEX idx_exam_reg_email ON exam_registrations(email);
CREATE INDEX idx_exam_attempts_token ON exam_attempts(access_token);
CREATE INDEX idx_exam_attempts_entrance ON exam_attempts(entrance_id);
CREATE INDEX idx_exam_questions_exam ON exam_questions(exam_id);

