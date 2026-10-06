-- ذائقہ (ZAIQA) — Step 3 schema additions
-- Taste Personality quiz storage. The quiz currently RUNS on
-- config/taste_quiz.php + config/taste_personalities.php (so the
-- page render and the scoring API can never drift apart), but the
-- same structure is seeded here for an eventual admin-editable
-- quiz and for storing results once accounts exist.

-- Note: CREATE TABLE statements here are safe to re-run (IF NOT
-- EXISTS). The INSERT statements are not -- re-running them after
-- a successful import will duplicate seed rows.

USE zaiqa;

CREATE TABLE IF NOT EXISTS taste_questions (
  id VARCHAR(40) PRIMARY KEY,             -- matches config question 'id', e.g. 'spice'
  question_text VARCHAR(200) NOT NULL,
  sort_order TINYINT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS taste_answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id VARCHAR(40) NOT NULL,
  option_id VARCHAR(40) NOT NULL,          -- matches config option 'id', e.g. 'hot'
  answer_text VARCHAR(150) NOT NULL,
  weight_json JSON NOT NULL,               -- e.g. {"fire": 2, "adventurer": 1}
  FOREIGN KEY (question_id) REFERENCES taste_questions(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_question_option (question_id, option_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS taste_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  personality_key VARCHAR(30) NOT NULL,    -- e.g. 'fire', 'comfort'
  personality_name VARCHAR(80) NOT NULL,
  description VARCHAR(500) NOT NULL,
  spice_pref VARCHAR(80) NOT NULL,
  computed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- Note: this FK will fail until the `users` table exists (added
-- when auth is built). Run this table's CREATE after that step,
-- or drop the FK line for now if you want Step 3 alone to import cleanly.

INSERT INTO taste_questions (id, question_text, sort_order) VALUES
 ('spice', 'How do you like your spice?', 1),
 ('sweet_savory', 'Sweet or savory — where do you land?', 2),
 ('food_type', 'What sounds best right now?', 3),
 ('rich_light', 'Rich and indulgent, or light and fresh?', 4),
 ('adventure', 'How adventurous are you with new cuisines?', 5),
 ('mood', 'What\u2019s tonight\u2019s dining mood?', 6);

INSERT INTO taste_answers (question_id, option_id, answer_text, weight_json) VALUES
 ('spice', 'mild', 'Mild — keep it gentle', '{"comfort":2,"classic":1}'),
 ('spice', 'medium', 'Medium — a little kick', '{"classic":2,"comfort":1}'),
 ('spice', 'hot', 'Hot — bring the heat', '{"fire":2,"adventurer":1}'),
 ('spice', 'fire', 'As hot as it gets', '{"fire":3}'),

 ('sweet_savory', 'sweet', 'Sweet tooth, always', '{"sweet":3}'),
 ('sweet_savory', 'savory', 'Savory over sweet', '{"classic":1,"comfort":1}'),
 ('sweet_savory', 'balance', 'A balance of both', '{"fresh":1,"adventurer":1}'),

 ('food_type', 'classics', 'Comfort classics', '{"comfort":3}'),
 ('food_type', 'bold', 'Bold & spicy', '{"fire":3}'),
 ('food_type', 'elegant', 'Elegant & fine dining', '{"classic":2,"adventurer":1}'),
 ('food_type', 'fresh_light', 'Fresh & light', '{"fresh":3}'),
 ('food_type', 'surprise', 'Surprise me with anything', '{"adventurer":3}'),

 ('rich_light', 'rich', 'Rich & indulgent', '{"comfort":2,"sweet":1}'),
 ('rich_light', 'light', 'Light & fresh', '{"fresh":3}'),
 ('rich_light', 'depends', 'Depends entirely on my mood', '{"adventurer":2}'),

 ('adventure', 'rarely', 'Rarely — I know what I like', '{"classic":3}'),
 ('adventure', 'sometimes', 'Sometimes, if it\u2019s recommended', '{"comfort":1,"fresh":1}'),
 ('adventure', 'always', 'Always exploring something new', '{"adventurer":3,"fire":1}'),

 ('mood', 'cozy', 'Cozy & familiar', '{"comfort":3}'),
 ('mood', 'exciting', 'Exciting & bold', '{"fire":3}'),
 ('mood', 'romantic', 'Romantic & refined', '{"classic":2,"sweet":1}'),
 ('mood', 'energizing', 'Light & energizing', '{"fresh":3}');
