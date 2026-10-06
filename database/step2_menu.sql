-- ذائقہ (ZAIQA) — Step 2 schema additions
-- Adds what the interactive menu + dish detail modal need:
-- ingredients, flavor profiles (with the 5-stage flavor journey),
-- and dietary/pairing info on dishes. Run after database/zaiqa.sql.

USE zaiqa;

ALTER TABLE dishes
  ADD COLUMN dietary_tags VARCHAR(255) NOT NULL DEFAULT '' AFTER mood_tags,
  ADD COLUMN pairing_note VARCHAR(255) DEFAULT NULL AFTER dietary_tags;

CREATE TABLE IF NOT EXISTS ingredients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dish_ingredients (
  dish_id INT NOT NULL,
  ingredient_id INT NOT NULL,
  PRIMARY KEY (dish_id, ingredient_id),
  FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE,
  FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS flavor_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dish_id INT NOT NULL UNIQUE,
  spice TINYINT NOT NULL DEFAULT 0,      -- 0-5 scale, matches dot display
  saltiness TINYINT NOT NULL DEFAULT 0,
  tanginess TINYINT NOT NULL DEFAULT 0,
  richness TINYINT NOT NULL DEFAULT 0,
  journey_stage_1 VARCHAR(80) NOT NULL,  -- e.g. "First Bite" label text
  journey_stage_2 VARCHAR(80) NOT NULL,
  journey_stage_3 VARCHAR(80) NOT NULL,
  journey_stage_4 VARCHAR(80) NOT NULL,
  journey_stage_5 VARCHAR(80) NOT NULL,  -- "Finish"
  FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Example rows for dish 1 (Chicken Karahi) — repeat this pattern for
-- the remaining seeded dishes using config/seed_dishes.php as the
-- source of truth for values (kept in one place to avoid drift
-- between the PHP fallback and the SQL seed).

UPDATE dishes SET
  dietary_tags = 'Halal,Gluten-Free',
  pairing_note = 'Pairs beautifully with buttered naan and mint raita.'
WHERE id = 1;

INSERT INTO ingredients (name) VALUES
  ('Chicken'), ('Tomato'), ('Ginger'), ('Green Chili'), ('Ghee'), ('Coriander');

INSERT INTO dish_ingredients (dish_id, ingredient_id)
SELECT 1, id FROM ingredients WHERE name IN ('Chicken','Tomato','Ginger','Green Chili','Ghee','Coriander');

INSERT INTO flavor_profiles
  (dish_id, spice, saltiness, tanginess, richness, journey_stage_1, journey_stage_2, journey_stage_3, journey_stage_4, journey_stage_5)
VALUES
  (1, 3, 3, 3, 4, 'Tangy tomato hit', 'Warm ginger depth', 'Building green-chili heat', 'Buttery ghee finish', 'Lingering warmth');

-- Repeat INSERT/UPDATE blocks above for dish_id 2–10 using the same
-- values as config/seed_dishes.php before going live.
