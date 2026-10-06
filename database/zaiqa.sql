-- ذائقہ (ZAIQA) — Step 1 schema
-- Covers what the homepage mood selector needs. More tables
-- (users, orders, reservations, etc.) are added as each feature
-- from the Phase 1 architecture doc gets built.
--
-- The CREATE TABLE statements below are safe to re-run (IF NOT
-- EXISTS). The INSERT statements are NOT — re-running this file
-- after it already succeeded will insert duplicate categories and
-- dishes (no error, just doubled-up menu items). If you need to
-- re-run just the table structure, comment out the INSERT blocks
-- first, or check `SELECT COUNT(*) FROM dishes;` — if it's already
-- 10+, the seed data is already in.

CREATE DATABASE IF NOT EXISTS zaiqa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zaiqa;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dishes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  name_urdu VARCHAR(120) DEFAULT NULL,
  description VARCHAR(255) NOT NULL,
  price INT NOT NULL,                     -- PKR, whole rupees
  prep_time_min SMALLINT DEFAULT NULL,
  spice_level TINYINT DEFAULT 0,          -- 0-5
  image_path VARCHAR(500) NOT NULL,
  mood_tags VARCHAR(255) NOT NULL DEFAULT '', -- comma list: comfort,spicy,fancy,fresh,sweet,late_night
  is_secret TINYINT(1) NOT NULL DEFAULT 0,
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  INDEX idx_dishes_available (is_available)
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, sort_order) VALUES
 ('Starters', 'starters', 1),
 ('Main Course', 'main-course', 2),
 ('Burgers', 'burgers', 3),
 ('Pasta', 'pasta', 4),
 ('Desserts', 'desserts', 5),
 ('Chef Specials', 'chef-specials', 6);

INSERT INTO dishes (category_id, name, description, price, spice_level, image_path, mood_tags) VALUES
 (2, 'Chicken Karahi', 'Slow-cooked in tomato and ginger, finished with green chili.', 1450, 3,
  'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=600&q=80', 'comfort,spicy'),
 (4, 'Creamy Alfredo Pasta', 'Fettuccine in a slow-reduced garlic cream sauce.', 1100, 1,
  'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?auto=format&fit=crop&w=600&q=80', 'comfort'),
 (6, 'Signature Fire Steak', 'Charred sirloin, house chili oil, smoked paprika crust.', 1950, 4,
  'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=600&q=80', 'spicy,fancy'),
 (1, 'Peri Peri Wings', 'Double-fried, tossed in a hand-blended peri peri glaze.', 890, 4,
  'https://images.unsplash.com/photo-1608039755401-742074f0548d?auto=format&fit=crop&w=600&q=80', 'spicy,late_night'),
 (6, 'Truffle Risotto', 'Arborio rice, wild mushroom, shaved black truffle.', 2200, 1,
  'https://images.unsplash.com/photo-1476124369491-e7addf5db371?auto=format&fit=crop&w=600&q=80', 'fancy'),
 (2, 'Pan-Seared Salmon', 'Citrus beurre blanc, charred asparagus.', 2450, 1,
  'https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=600&q=80', 'fancy,fresh'),
 (1, 'Citrus Burrata Salad', 'Heirloom tomato, basil oil, creamy burrata.', 1200, 0,
  'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?auto=format&fit=crop&w=600&q=80', 'fresh'),
 (5, 'Molten Lava Cake', 'Dark chocolate centre, vanilla bean ice cream.', 650, 0,
  'https://images.unsplash.com/photo-1624353365286-3f8d62daad51?auto=format&fit=crop&w=600&q=80', 'sweet'),
 (5, 'Kunafa Cheesecake', 'Crisp kunafa base, saffron syrup.', 750, 0,
  'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=600&q=80', 'sweet'),
 (3, 'Zaiqa Smash Burger', 'Double patty, caramelized onion, secret sauce.', 1050, 2,
  'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=80', 'late_night,comfort');
