-- ذائقہ (ZAIQA) — Step 4 schema additions
-- Seating areas + tables power the vibe selector and the 2D table
-- map. Reservations stores the booking with a generated reference
-- code. Run after step3_taste.sql.

-- Note: CREATE TABLE statements here are safe to re-run (IF NOT
-- EXISTS). The INSERT statements are not -- re-running them after
-- a successful import will duplicate seed rows.

USE zaiqa;

CREATE TABLE IF NOT EXISTS seating_areas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(40) NOT NULL UNIQUE,        -- 'garden', 'quiet-corner', etc.
  name VARCHAR(80) NOT NULL,
  emoji VARCHAR(10) NOT NULL DEFAULT '',
  description VARCHAR(255) NOT NULL,
  atmosphere VARCHAR(255) NOT NULL,
  occasions VARCHAR(255) NOT NULL DEFAULT '', -- comma list
  image_path VARCHAR(500) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tables (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seating_area_id INT NOT NULL,
  table_number VARCHAR(10) NOT NULL UNIQUE,  -- 'T1', 'T2', ...
  capacity TINYINT NOT NULL,
  grid_row TINYINT NOT NULL DEFAULT 1,       -- position within its area's mini floor plan
  grid_col TINYINT NOT NULL DEFAULT 1,
  status ENUM('available','reserved','unavailable') NOT NULL DEFAULT 'available',
  FOREIGN KEY (seating_area_id) REFERENCES seating_areas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,               -- nullable until accounts exist
  reference_code VARCHAR(12) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  reservation_date DATE NOT NULL,
  reservation_time TIME NOT NULL,
  guests TINYINT NOT NULL,
  seating_area_id INT DEFAULT NULL,
  table_id INT DEFAULT NULL,
  special_request VARCHAR(500) DEFAULT NULL,
  status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seating_area_id) REFERENCES seating_areas(id),
  FOREIGN KEY (table_id) REFERENCES tables(id),
  INDEX idx_reservations_date (reservation_date)
) ENGINE=InnoDB;
-- Note: user_id has no FK yet (added once the `users` table exists in
-- the auth step) — kept nullable so guest reservations work today.

INSERT INTO seating_areas (slug, name, emoji, description, atmosphere, occasions, image_path) VALUES
 ('garden', 'Garden', '🌿', 'Open-air seating among string lights and greenery.', 'Relaxed, breezy, golden-hour perfect.', 'Casual dinners,Friends gathering',
  'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=700&q=80'),
 ('quiet-corner', 'Quiet Corner', '🕯️', 'Tucked-away tables for low-key conversation.', 'Calm, intimate, softly lit.', 'Date night,Business dinner',
  'https://images.unsplash.com/photo-1554679665-f5537f187268?auto=format&fit=crop&w=700&q=80'),
 ('family-table', 'Family Table', '👨‍👩‍👧', 'Wide tables built for sharing and sprawling.', 'Warm, lively, unhurried.', 'Family dinner,Celebration',
  'https://images.unsplash.com/photo-1424847651672-bf20a4b0982b?auto=format&fit=crop&w=700&q=80'),
 ('music-area', 'Music Area', '🎵', 'Seating near the evening live set.', 'Energetic, social, a little louder.', 'Friends night,Birthday',
  'https://images.unsplash.com/photo-1470337458703-46ad1756a187?auto=format&fit=crop&w=700&q=80'),
 ('window-seat', 'Window Seat', '🪟', 'Street-facing tables with natural light.', 'Bright by day, moody by night.', 'Casual dinners,Solo dining',
  'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=700&q=80');

INSERT INTO tables (seating_area_id, table_number, capacity, grid_row, grid_col, status) VALUES
 (1,'T1',2,1,1,'available'), (1,'T2',4,1,2,'available'), (1,'T3',6,2,1,'reserved'),
 (2,'T4',2,1,1,'available'), (2,'T5',2,1,2,'available'), (2,'T6',4,2,1,'unavailable'),
 (3,'T7',6,1,1,'available'), (3,'T8',8,1,2,'available'), (3,'T9',6,2,1,'available'),
 (4,'T10',4,1,1,'available'), (4,'T11',4,1,2,'reserved'), (4,'T12',2,2,1,'available'),
 (5,'T13',2,1,1,'available'), (5,'T14',2,1,2,'available'), (5,'T15',4,2,1,'available');
