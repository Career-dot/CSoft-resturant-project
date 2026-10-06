-- ذائقہ (ZAIQA) — Step 7 schema additions
-- Two secret dishes (for the "Hidden Table" secret menu) and a
-- kitchen_status table for a future admin dashboard to write real
-- numbers into. Run after step6_auth.sql.

-- Note: CREATE TABLE statements here are safe to re-run (IF NOT
-- EXISTS). The INSERT statements are not -- re-running them after
-- a successful import will duplicate seed rows.

USE zaiqa;

CREATE TABLE IF NOT EXISTS kitchen_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  activity_label VARCHAR(80) NOT NULL,   -- e.g. 'Grilling', 'Baking'
  emoji VARCHAR(10) NOT NULL DEFAULT '',
  count SMALLINT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- Not read from yet — api/kitchen-status.php currently generates a
-- clearly-labeled simulated snapshot instead (see that file's
-- docblock), since there's no admin write path to this table until
-- the admin dashboard step. Seed a few rows if you want to wire it
-- up sooner:
-- INSERT INTO kitchen_status (activity_label, emoji, count) VALUES
--   ('Grilling', '🔥', 3), ('Baking', '🍰', 2), ('Preparing', '🥤', 4);

INSERT INTO dishes (category_id, name, description, price, spice_level, image_path, mood_tags, dietary_tags, is_secret) VALUES
 ((SELECT id FROM categories WHERE slug = 'chef-specials'), 'The Hidden Karahi', 'Off-menu: double-marinated, 48-hour rest, finished tableside.', 2600, 4,
  'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=800&q=80', 'spicy,fancy', 'Halal', 1),
 ((SELECT id FROM categories WHERE slug = 'desserts'), 'Midnight Kunafa', 'Off-menu: dark chocolate kunafa with a burnt-honey drizzle.', 950, 0,
  'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=800&q=80', 'sweet,late_night', 'Vegetarian', 1);
