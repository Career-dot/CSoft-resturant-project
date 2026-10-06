-- ذائقہ (ZAIQA) — Step 8 schema additions
-- Admin accounts. Run after step7_secret_kitchen.sql.
--
-- Deliberately no seed admin row here — a hardcoded default
-- admin/password in a public repo is a real security smell. The
-- first admin account is created through admin/setup.php, which
-- only works while this table is empty and locks itself after.

USE zaiqa;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(30) NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
