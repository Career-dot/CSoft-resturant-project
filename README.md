# ذائقہ (ZAIQA) — Project Setup

## Status (as of this build)

**Built and working:**
- Base template — `includes/header.php`, `navbar.php`, `footer.php` (also compute `BASE_URL` so the site works from a subfolder, e.g. XAMPP's `htdocs/zaiqa/` — see "If your CSS/pages don't load" below)
- Homepage — hero + "How are you feeling today?" mood selector (`index.php`)
- Interactive menu — search/filter/spice grid + "Taste Before You Order" modal with flavor map and flavor journey (`pages/menu.php`)
- Taste Personality quiz — one-question-at-a-time stepper, weighted scoring, animated result reveal, saved to the logged-in user's profile (`pages/discover.php`)
- Reservation flow — vibe selector → 2D table map → form → confirmation with a generated reference code (`pages/reserve.php`)
- Ordering — cart (localStorage via `assets/js/cart.js`), dish customization ("Build Your Burger" style, `config/customization_options.php`), 🎲 Surprise Me, Budget Mode ("Build My Meal"), checkout (`pages/order.php`)
- Accounts — register/login/logout, session-based auth (`pages/register.php`, `pages/login.php`, `config/session.php`)
- My Taste — saved taste personality, Food Passport (dishes auto-stamped on order), favorites (`pages/my-taste.php`)
- Order tracking — live status stepper (Received → Preparing → Cooking → Almost Ready → Served), polls every 5s, reachable from the checkout confirmation or directly via `pages/tracking.php?ref=ZO-XXXXXX`
- Secret Menu — "The Hidden Table" (`pages/secret-menu.php`), unlocked by clicking the navbar logo 5× within 3 seconds (an Easter egg, not real access control — the page itself is always reachable by URL)
- Our Kitchen + Chef — philosophy, a simulated "what's happening right now" kitchen status, chef profile, and a click-to-reveal Ask the Chef FAQ (`pages/kitchen.php`)
- Admin dashboard (`admin/`) — separate login/session from customer accounts, first-admin bootstrap (`admin/setup.php`, no hardcoded default credentials), dashboard stats, menu management (add/edit/delete/availability/secret-flag), order status updates (drives customer tracking), reservation confirm/cancel, read-only customer list, table status management, and a dedicated Secret Menu view
- Backend — every `api/*.php` endpoint is PDO/prepared-statement backed; menu/mood/taste/reservation endpoints fall back to seed data when MySQL isn't connected, while account- and order-specific endpoints (login, register, favorites, food-passport, order-status) correctly require the real database
- Database — see the numbered files in `database/`, run in order (details below)

- Polish pass — page-specific SEO meta descriptions on every public page, `noindex` on account/utility pages (login, register, my-taste, tracking) so they don't show up in search results, and `loading="lazy"` added to the remaining dynamically-rendered images (cart, favorites, Food Passport, Surprise Me, secret menu, dish modal)

**This completes every step in the original build order, including the final polish pass.** A few things are intentionally out of scope rather than silently missing — see "Known simplifications" below, especially the Reviews note. Accessibility basics (semantic HTML, `:focus-visible` states, `aria-label`/`aria-pressed`, `prefers-reduced-motion` support) and image alt text were already in place from earlier steps — this pass was additive, not a rescue job.

**Known simplifications:**
- Reservation table availability is a single status flag per table (available/reserved/unavailable), not scoped to a specific date and time. A real deployment needs to check existing reservations for the requested date/time window rather than one global status per table — flagged in a comment at the top of `api/reserve.php`.
- Order prices are recomputed server-side from the dish's base price + customization deltas (never trusted from the client) — see `api/order.php`. Only the Zaiqa Smash Burger currently has customization options defined; add more dishes to `config/customization_options.php` to extend it.
- Budget Mode (`api/budget.php`) uses a simple greedy best-fit strategy across small dish pools — good enough for a menu this size, but not a general knapsack solver.
- Food Passport badge tiers are computed on read (not stored) from a simple dish-count threshold in `api/food-passport.php` — no separate achievements table yet.
- Order tracking (`api/order-status.php`) now reads the real `orders.status` the moment an admin sets it via `admin/orders.php` — the simulated time-based progression only kicks in while an order is still sitting at its default "received" status (i.e. before any admin has touched it), so a freshly placed order still feels alive before staff get to it.
- Kitchen status (`api/kitchen-status.php`) is still explicitly **simulated** (clearly labeled `simulated: true` in the response) — the `kitchen_status` table exists (`database/step7_secret_kitchen.sql`) but nothing writes to it yet; wiring an admin panel for it was judged lower priority than the core CRUD panels built here.
- **Reviews (Section 30 of the original brief) were never built**, on either the customer or admin side — there's no review submission form, no `reviews` table, and so no admin moderation panel either. Building an admin "approve/hide/delete" UI for a feature that doesn't exist elsewhere would just be a UI shell with nothing real behind it, so it's left out rather than faked. Add it as a future step: a `reviews` table (user_id nullable, dish_id nullable, rating, comment, is_approved), a submission form on the menu or a dedicated page, and an `admin/reviews.php` modeled on `admin/reservations.php`.
- The admin dish form doesn't yet edit flavor maps, ingredients, or the flavor journey (those still need direct SQL, following the pattern in `database/step2_menu.sql`) — flagged in the form itself.
- No CSRF tokens on admin forms yet (session-based auth only) — reasonable for a single-admin demo, but worth adding (e.g. a per-session token checked on every POST) before any real deployment.

---

## Full folder structure

```
zaiqa/
├── README.md
├── index.php                          — Homepage: hero + mood selector
│
├── config/
│   ├── base_url.php                   — Computes BASE_URL (subfolder-safe paths)
│   ├── database.php                   — PDO connection
│   ├── session.php                    — Auth/session helpers (login, logout, require-login)
│   ├── seed_dishes.php                — Shared dish seed data (dev fallback) incl. 2 secret dishes
│   ├── seed_reservations.php          — Seating areas + tables seed data
│   ├── taste_quiz.php                 — Quiz questions + scoring weights
│   ├── taste_personalities.php        — Personality result definitions
│   └── customization_options.php      — Build-your-own groups (e.g. the burger)
│
├── includes/
│   ├── header.php                     — <head>, fonts, meta, BASE_URL/APP_USER setup
│   ├── navbar.php                     — Sticky nav (login-aware, cart badge)
│   └── footer.php                     — Footer + main.js/cart.js includes
│
├── pages/
│   ├── discover.php                   — Taste Personality quiz
│   ├── menu.php                       — Interactive menu + dish modal
│   ├── reserve.php                    — Reservation wizard
│   ├── order.php                      — Cart, Surprise Me, Budget Mode, checkout
│   ├── tracking.php                   — Order tracking stepper
│   ├── kitchen.php                    — Our Kitchen, chef profile, Ask the Chef
│   ├── secret-menu.php                — The Hidden Table (secret menu)
│   ├── login.php                      — Log in
│   ├── register.php                   — Create account
│   └── my-taste.php                   — Taste profile, Food Passport, favorites (login required)
│
├── admin/
│   ├── includes/admin-session.php     — Separate admin auth (different session key than customers)
│   ├── includes/admin-chrome-open.php / admin-chrome-close.php  — Shared sidebar layout
│   ├── setup.php                      — One-time first-admin bootstrap (locks itself after)
│   ├── login.php / logout.php
│   ├── dashboard.php                  — Quick stats
│   ├── menu.php / dish-form.php       — Menu CRUD
│   ├── orders.php                     — Order status updates
│   ├── reservations.php               — Confirm/cancel reservations
│   ├── customers.php                  — Read-only customer list
│   ├── tables.php                     — Table status management
│   └── secret-menu.php                — Secret dish management
│
├── api/
│   ├── recommendations.php            — Mood-based dish suggestions
│   ├── menu.php                       — Menu search/filter (excludes secret dishes)
│   ├── dish.php                       — Single dish detail (+ customization groups)
│   ├── taste.php                      — Quiz scoring (POST) + saved profile (GET)
│   ├── tables.php                     — Seating areas + table map
│   ├── reserve.php                    — Create a reservation
│   ├── order.php                      — Checkout (server-side price recompute, stamps Food Passport)
│   ├── order-status.php               — Order tracking lookup (simulated progress)
│   ├── surprise.php                   — 🎲 Surprise Me
│   ├── budget.php                     — Build My Meal
│   ├── secret-menu.php                — Secret dish list
│   ├── kitchen-status.php             — Simulated live kitchen activity
│   ├── register.php / login.php / logout.php
│   └── favorites.php / food-passport.php
│
├── assets/
│   ├── css/   (base, components, menu, discover, reserve, order, auth, kitchen, tracking)
│   ├── js/    (main, cart, mood-selector, menu, discover-quiz, reserve, order, auth, my-taste, kitchen, tracking, secret-menu)
│   └── images/ (placeholder note — see below)
│
└── database/
    ├── zaiqa.sql                      — categories, dishes
    ├── step2_menu.sql                 — ingredients, flavor_profiles
    ├── step3_taste.sql                — taste_questions, taste_answers, taste_profiles
    ├── step4_reservations.sql         — seating_areas, tables, reservations
    ├── step5_orders.sql               — orders, order_items, Drinks category + seed drinks
    ├── step6_auth.sql                 — users, favorites, food_passport + FK hookups
    ├── step7_secret_kitchen.sql       — 2 secret dishes, kitchen_status table
    └── step8_admin.sql                — admins table (no seed row — see admin/setup.php)
```

---

## Local setup (XAMPP / Laragon / MAMP)

1. Drop the `zaiqa/` folder into your server's document root (e.g. `htdocs/zaiqa`).
2. Create the database, importing every file in `database/` **in order**:
   ```
   mysql -u root -p < database/zaiqa.sql
   mysql -u root -p < database/step2_menu.sql
   mysql -u root -p < database/step3_taste.sql
   mysql -u root -p < database/step4_reservations.sql
   mysql -u root -p < database/step5_orders.sql
   mysql -u root -p < database/step6_auth.sql
   mysql -u root -p < database/step7_secret_kitchen.sql
   mysql -u root -p < database/step8_admin.sql
   mysql -u root -p < database/step9_contact.sql
   ```
3. By default the app connects as `root` with no password on `127.0.0.1`. To override, set environment variables before starting PHP (or edit `config/database.php` directly):
   ```
   ZAIQA_DB_HOST=127.0.0.1
   ZAIQA_DB_NAME=zaiqa
   ZAIQA_DB_USER=root
   ZAIQA_DB_PASS=
   ```
4. Visit `http://localhost/zaiqa/` for the homepage, `http://localhost/zaiqa/pages/menu.php` for the menu, and so on.

## Local setup (PHP built-in server, no Apache)

```
cd zaiqa
php -S localhost:8000
```
Then open `http://localhost:8000/`.

**Note:** the built-in server ignores `.htaccess`-style rewrites, but this project doesn't rely on any yet — all links are plain `.php` paths, so this works fine for now.

## Admin access

1. Visit `http://localhost/zaiqa/admin/setup.php` — this only works while the `admins` table is empty, and redirects to the login page afterward (so it can't be used again to create a second account by anyone who finds the URL).
2. Fill in a name, email, and password (8+ characters) to create the first admin and you're logged straight in.
3. From then on, admin access is at `http://localhost/zaiqa/admin/login.php`. Admin sessions are completely separate from customer accounts — logging into one doesn't affect the other.

## If your CSS/pages don't load ("CSS is not working")

This almost always means the project is being served from a **subfolder** (e.g. XAMPP's `htdocs/zaiqa/`, so the site is at `http://localhost/zaiqa/`) while assets were being requested from the domain root. This is handled automatically: `config/base_url.php` computes a `BASE_URL` constant from `DOCUMENT_ROOT` vs. the project's actual folder, and every internal link, stylesheet, script tag, and JS `fetch()` call is written relative to it (PHP via `<?= BASE_URL ?>`, JS via `window.APP_BASE`, set in a small inline script in the `<head>`).

If pages still don't style correctly:
1. Check your browser's dev tools → Network tab — are `/assets/css/*.css` (or `/zaiqa/assets/css/*.css`) requests 404ing?
2. Confirm `DOCUMENT_ROOT` is set correctly by your server (it is by default in XAMPP/Laragon/MAMP and PHP's built-in server).
3. Make sure you're using the latest `config/base_url.php` and `includes/header.php` — an older copy without them will break every page's styling and API calls.

## Re-running a database/*.sql file

Every `CREATE TABLE` in every migration file uses `IF NOT EXISTS`, and `step6_auth.sql`'s two `ALTER TABLE ... ADD CONSTRAINT` statements check `information_schema` first — so re-running any file after a partial or failed import (e.g. if you hit an error partway through) is safe and won't throw "table already exists" errors.

The one thing that **isn't** safe to blindly re-run is the `INSERT INTO` seed data in `zaiqa.sql`, `step3_taste.sql`, `step4_reservations.sql`, `step5_orders.sql`, and `step7_secret_kitchen.sql` — re-running those specifically will duplicate menu items, questions, tables, etc. (no error, just doubled-up data). If you only need to fix the table structure after an error, comment out that file's `INSERT` blocks before re-running, or check first with e.g. `SELECT COUNT(*) FROM dishes;` — if it's already populated, skip straight to the next file.

## If a page says "Couldn't load..." (e.g. reservations)

This means the database **connects** fine, but a specific table that page needs doesn't exist yet — almost always because the `database/*.sql` files weren't all imported, or weren't imported **in order**. For example: the reservation page's vibe/table selector needs `seating_areas` and `tables`, which come from `database/step4_reservations.sql` specifically.

Every data-reading endpoint now catches this and falls back to demo data automatically (so the page still works), but a real booking won't be saved until the actual migration is imported. Check your PHP error log for a line like `[zaiqa] tables.php: seating_areas/tables query failed (has step4_reservations.sql been imported?)` — it names the exact file to import. The fix is always the same: re-run the missing file from the numbered list in "Local setup" above, in order, starting from wherever you left off.

## If MySQL isn't set up yet

Menu/mood/taste/reservation/secret-menu endpoints fall back to seed data in `config/` when they can't reach MySQL, so the homepage, menu, and reservation flow are fully clickable without a database connection. Account- and order-specific endpoints (`api/register.php`, `api/login.php`, `api/favorites.php`, `api/food-passport.php`, `api/order-status.php`) correctly require the real database instead of pretending to work — there's no meaningful "fake" version of a user account or a specific order lookup. This fallback pattern is a development convenience, not a substitute for the real DB — wire up MySQL before treating anything as production-ready.

## Footer, Contact, Privacy, Terms

The footer was redesigned into four columns (Brand, Explore, Account, Visit Us) with working mail/phone/map icon links. The "Account" column shows **My Taste** when logged in, or **Log In / Create an Account** when not — it no longer links to a login-gated page for a logged-out visitor.

`pages/contact.php`, `pages/privacy.php`, and `pages/terms.php` are now real pages (they were linked from the footer from early on but never built, so they 404'd). Contact has a working form — `api/contact.php` stores submissions in `contact_messages` (`database/step9_contact.sql`, same graceful-degrade pattern as reservations/orders if the DB isn't connected). Privacy and Terms are static content pages with genuine, if generic, policy text for a project at this stage — review and replace before any real-world use.

## Images

No local image files are included yet — dishes currently reference hosted Unsplash URLs (see `config/seed_dishes.php`, `database/*.sql`, and the hero background in `assets/css/components.css`). Swap in your own photography by replacing those URLs and dropping files in `assets/images/`.
