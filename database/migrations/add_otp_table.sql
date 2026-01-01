-- OTP (One-Time Password) Table for Email Verification
-- Run this migration to add OTP support to Verdant SMS

CREATE TABLE IF NOT EXISTS user_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    otp_code VARCHAR(255) NOT NULL COMMENT 'Hashed OTP for security',
    token VARCHAR(64) NOT NULL COMMENT 'Direct verification link token',
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used TINYINT(1) DEFAULT 0,
    INDEX idx_user_id (user_id),
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add email_verified columns to users table if not exists
ALTER TABLE users
ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS email_verified_at DATETIME NULL;

-- Create index for faster lookups
CREATE INDEX IF NOT EXISTS idx_email_verified ON users(email_verified);
