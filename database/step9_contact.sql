-- ذائقہ (ZAIQA) — Step 9 schema additions
-- Contact form submissions. Run after step8_admin.sql.
--
-- Note: CREATE TABLE here is safe to re-run (IF NOT EXISTS).

USE zaiqa;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message VARCHAR(1000) NOT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;
