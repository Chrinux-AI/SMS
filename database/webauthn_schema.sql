-- WebAuthn Credentials Table for Passkey Authentication
-- Required for the /api/webauthn.php endpoint

CREATE TABLE IF NOT EXISTS `webauthn_credentials` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `credential_id` VARCHAR(500) NOT NULL COMMENT 'Base64URL encoded credential ID',
  `public_key` TEXT NOT NULL COMMENT 'JSON encoded public key data',
  `credential_name` VARCHAR(100) NOT NULL DEFAULT 'My Device' COMMENT 'User-friendly name',
  `transports` JSON NULL COMMENT 'Supported transports (usb, nfc, ble, internal)',
  `counter` INT DEFAULT 0 COMMENT 'Signature counter for replay protection',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_used_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_credential` (`credential_id`(255)),
  INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add full_name column to users if not exists
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `full_name` VARCHAR(200) GENERATED ALWAYS AS (CONCAT(first_name, ' ', last_name)) STORED;
