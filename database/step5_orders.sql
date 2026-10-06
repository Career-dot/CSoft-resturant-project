-- ذائقہ (ZAIQA) — Step 5 schema additions
-- Orders/order_items tables, and a Drinks category + seed drinks
-- (needed for Budget Mode to assemble a main + side + drink +
-- dessert combo). Run after step4_reservations.sql.
--
-- Note: build-your-own customization groups (e.g. the burger's
-- bread/sauce/cheese/extras) live in PHP at
-- config/customization_options.php rather than in a DB column —
-- same "config as source of truth" pattern used for the taste
-- quiz (config/taste_quiz.php), so the page render and the
-- pricing logic can never drift apart. No migration needed for
-- that part.

-- Note: CREATE TABLE statements here are safe to re-run (IF NOT
-- EXISTS). The INSERT statements are not -- re-running them after
-- a successful import will duplicate seed rows.

USE zaiqa;

INSERT INTO categories (name, slug, sort_order) VALUES ('Drinks', 'drinks', 7);

INSERT INTO dishes (category_id, name, description, price, spice_level, image_path, mood_tags, dietary_tags) VALUES
 ((SELECT id FROM categories WHERE slug = 'drinks'), 'Cucumber Mint Cooler', 'Fresh cucumber, mint, and lime over ice.', 350, 0,
  'https://images.unsplash.com/photo-1621263764928-df1444c5e859?auto=format&fit=crop&w=600&q=80', 'fresh', 'Vegetarian,Gluten-Free'),
 ((SELECT id FROM categories WHERE slug = 'drinks'), 'Saffron Milk Cooler', 'Chilled saffron milk with a hint of cardamom.', 380, 0,
  'https://images.unsplash.com/photo-1600271886742-f049cd451bba?auto=format&fit=crop&w=600&q=80', 'sweet', 'Vegetarian'),
 ((SELECT id FROM categories WHERE slug = 'drinks'), 'Spiced Ginger Mocktail', 'Ginger, chili syrup, and citrus over crushed ice.', 400, 2,
  'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=600&q=80', 'spicy', 'Vegetarian,Gluten-Free');

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,               -- nullable until accounts exist
  reference_code VARCHAR(12) NOT NULL UNIQUE,
  customer_name VARCHAR(120) NOT NULL,
  customer_email VARCHAR(150) DEFAULT NULL,
  customer_phone VARCHAR(30) DEFAULT NULL,
  table_id INT DEFAULT NULL,              -- set when ordered via QR/table (later step)
  notes VARCHAR(500) DEFAULT NULL,
  subtotal INT NOT NULL,
  total INT NOT NULL,
  status ENUM('received','preparing','cooking','ready','served','cancelled') NOT NULL DEFAULT 'received',
  placed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (table_id) REFERENCES tables(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  dish_id INT NOT NULL,
  dish_name VARCHAR(120) NOT NULL,        -- snapshot, survives menu changes
  quantity TINYINT NOT NULL DEFAULT 1,
  unit_price INT NOT NULL,                -- base price + customization deltas
  customization_json JSON DEFAULT NULL,   -- e.g. {"bun":"sesame","extras":["jalapenos"]}
  line_total INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (dish_id) REFERENCES dishes(id)
) ENGINE=InnoDB;
