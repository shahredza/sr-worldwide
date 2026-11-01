# SR Worldwide — project context for Claude Code

> **Update (Nov 2026):** Shah decided the `main` branch is a **design-only static site** (HTML/CSS/JS,
> no PHP, no database, no MAMP) hosted on GitHub Pages. Product data is in `assets/js/data.js`; cart,
> demo login, orders and messages live in the browser's localStorage. The full PHP + MySQL build described
> below is kept on the `php-mysql-version` branch. The original brand assets moved to `legacy/assets/`.

## What this is
SR Worldwide is a fashion / streetwear e-commerce website (tops, bottoms, accessories).
It started as Shah Redza's Semester 1 group project (DFD10033 Big Data Programming Fundamental,
TVETMARA Lumut, Jan–Jun 2025) built with PHP + MySQL on MAMP. We are now **rebuilding it properly**
so it can be published on GitHub as a portfolio project for web-developer job applications.

## Folder guide
- `legacy/` — original 2025 pages (index.html, register.php, admin.php). **Reference only**: use them
  for the look, wording and features, then rebuild cleanly. Do not edit them.
- `assets/images/` — original brand assets: `srlogo.jpg` (logo), `background.jpg`, `cursorSR.png`
  (custom cursor), `item.jpg` (product photo), social icons, `aboutme1-3.jpeg`, `logoikm.png`/`logomara.png`.
- `assets/fonts/fontgothic.ttf` — original font.
- `database/schema.sql` — original DB structure (tables `admins`, `user`, `orders`), **no data**.
- `_reference/` — PRIVATE. Contains the original SQL dump with real people's emails, birthdays,
  password hashes and a home address, plus school documents. **Never read data from it into the new
  project, never copy it, never commit it.** It is in `.gitignore` — keep it that way.

## Stack
- PHP 8 + MySQL, runs locally with **MAMP** (Apache port 8888, MySQL user `root` / password `root`).
- No framework. Plain PHP with a small, clean structure. Vanilla JS + CSS (no jQuery).
- Database name: `sr_worldwide`.

## Features to build
Shop
- Home page (hero, featured products, brand mission "What Drives Us", footer).
- Shop by category: Tops, Bottoms, Accessories — product grid loaded from DB.
- Product detail page (images, price, sizes, add to cart).
- Cart (PHP session) and checkout that saves to an `orders` table
  (keep the original fields: fullname, nickname, street, city, state, postcode; add order items + total).
- About Us and Contact pages (contact form stores messages in DB).
User accounts
- Register (email, password, birthday — like the original), login, logout, "My orders" page.
Admin
- Admin login and dashboard: add / edit / delete products (with image upload), view orders and
  update status, view users and contact messages.
- **No public admin registration page** (the original had one — remove it). Create the first admin
  from `database/seed.sql` or a CLI script.

## Design
- Keep the SR Worldwide identity: logo, grey palette (original main colour #808080), `background.jpg`,
  custom cursor, gothic font for the logo/headings.
- Make it modern, clean and **mobile-first responsive**.
- Remove from the old version: autoplay background music (`kendrick.mp3` — copyrighted, do not
  include), the external `dfdweb.scrapping.cc` link, and the personal email in the privacy policy
  (use a placeholder like hello@srworldwide.example).
- Keep the IKM/MARA logos only in the footer credit line ("Semester 1 project, TVETMARA Lumut").

## Security rules (important — this is a portfolio piece)
- PDO with prepared statements everywhere. `password_hash` / `password_verify`.
- CSRF token on every form. `session_regenerate_id()` on login. Escape all output with `htmlspecialchars`.
- DB credentials in `config.php`, created from a committed `config.example.php`; `config.php` is gitignored.
- No `display_errors` in production mode; validate uploads (type + size).

## Database
- `database/schema.sql` — full new schema (users, admins, categories, products, orders, order_items,
  contact_messages).
- `database/seed.sql` — **fake demo data only**: ~12 products across the 3 categories and one demo admin
  (`admin` / `admin123`, clearly labelled as demo in the README).

## Suggested structure
```
public/            index.php, shop.php, product.php, cart.php, checkout.php, about.php, contact.php,
                   login.php, register.php, logout.php, account.php
public/admin/      login.php, index.php (dashboard), products.php, orders.php, users.php, messages.php
public/assets/     css/, js/, images/, fonts/, uploads/ (uploads gitignored except .gitkeep)
includes/          config.example.php, db.php, auth.php, csrf.php, header.php, footer.php, functions.php
database/          schema.sql, seed.sql
legacy/            original 2025 files (unchanged)
```

## README.md
Write a good README: project description, screenshots (take them once it runs), features, tech stack,
how to run with MAMP step by step (import schema + seed in phpMyAdmin), demo admin login,
project history (Semester 1 → rebuilt 2026), credits.

## Git & GitHub
- `git init`, commit in small logical steps with clear messages.
- Before the first commit run `git status` and confirm `_reference/` and `config.php` are NOT listed.
- Create a public GitHub repo named `sr-worldwide` with `gh repo create` and push.
  **Ask me before creating the repo and before pushing.**

## How to work with me
- I'm Shah, a Diploma in Computer Technology (Big Data) student. Explain briefly what you changed.
- Show a short plan first, then build step by step. Test pages in the browser at http://localhost:8888.
