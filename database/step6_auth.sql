-- ذائقہ (ZAIQA) — Step 6 schema additions
-- Accounts, favorites, and the food passport. Run after step5_orders.sql.
--
-- Note: database/step3_taste.sql created `taste_profiles` with a
-- FK to `users`, which didn't exist yet at that point — on most
-- MySQL setups that CREATE TABLE would have failed outright (no
-- table created at all), since `users` wasn't there to reference.
-- This migration creates `users` first, then re-declares
-- `taste_profiles` with IF NOT EXISTS so it exists correctly
-- either way, whether or not step 3's version ever succeeded.
--
-- This whole file is safe to re-run: every CREATE TABLE uses
-- IF NOT EXISTS, and the two ALTER TABLE statements at the bottom
-- check information_schema first and only add their foreign key
-- if it isn't already there — so running this twice produces no
-- errors either way.

USE zaiqa;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS taste_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  personality_key VARCHAR(30) NOT NULL,
  personality_name VARCHAR(80) NOT NULL,
  description VARCHAR(500) NOT NULL,
  spice_pref VARCHAR(80) NOT NULL,
  computed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS favorites (
  user_id INT NOT NULL,
  dish_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, dish_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS food_passport (
  user_id INT NOT NULL,
  dish_id INT NOT NULL,
  tried_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, dish_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- A dish is stamped into a user's food passport automatically the
-- first time it appears in one of their placed orders (see
-- api/order.php). Badge tiers (e.g. "3 dishes explored") are
-- computed on the fly from this count in api/food-passport.php
-- rather than a separate achievements table — simple enough at
-- this catalog size; revisit if achievements need admin editing.

-- Now that `users` exists, link it up to the tables that were
-- left nullable-with-no-FK earlier. Each block below checks
-- information_schema first and only adds the constraint if it
-- isn't already there, so re-running this file is a no-op here
-- instead of an error.

SET @fk_reservations_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_reservations_user'
);
SET @sql_reservations = IF(
  @fk_reservations_exists = 0,
  'ALTER TABLE reservations ADD CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt_reservations FROM @sql_reservations;
EXECUTE stmt_reservations;
DEALLOCATE PREPARE stmt_reservations;

SET @fk_orders_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_orders_user'
);
SET @sql_orders = IF(
  @fk_orders_exists = 0,
  'ALTER TABLE orders ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt_orders FROM @sql_orders;
EXECUTE stmt_orders;
DEALLOCATE PREPARE stmt_orders;
