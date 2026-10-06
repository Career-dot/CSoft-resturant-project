This project currently uses hosted Unsplash image URLs directly in
HTML/CSS/PHP (see pages, dish seed data) rather than local files, so
no images are stored here yet. Once you're ready to replace them with
your own photography, drop files in this folder and update the
image_path / src values in:
  - config/seed_dishes.php
  - database/zaiqa.sql
  - assets/css/components.css (hero background)
