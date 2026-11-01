# SR Worldwide

A streetwear e-commerce website (tops, bottoms and accessories) built with **plain PHP 8 and MySQL**, no framework.
It began as my Semester 1 group project at TVETMARA Lumut (2025) and was rebuilt from scratch in 2026 as a
clean, secure, mobile-first portfolio project.

![Home page](docs/screenshots/home.jpg)

## Features

**Shop**
- Home page with hero, shop-by-category tiles, featured products and the brand mission ("What Drives Us")
- Category pages (Tops, Bottoms, Accessories) loaded from the database, with sorting by newest or price
- Product pages with front/back image gallery, size picker and quantity
- Session-based cart: update quantities, remove items, live total
- Checkout that saves the order and its items in a database transaction (keeps the original delivery fields:
  full name, nickname, street, city, state, postcode)
- About Us, Contact (messages stored in the database) and Privacy Policy pages

**Customer accounts**
- Register with email, password and birthday (same password rule as the original: 8+ characters with upper case, lower case and a number)
- Log in / log out, and a **My orders** page with order status

**Admin dashboard** (`/admin/`)
- Stats: revenue, orders, pending orders, live products, customers
- Products: add, edit, hide/show, delete, with **image upload**
- Orders: filter by status, view items, update status (pending → paid → shipped → completed / cancelled)
- Users list with order count and total spent
- Contact messages: mark read/unread, delete
- No public admin sign-up page: admins are created with the seed file or a CLI script

## Screenshots

| Shop | Product |
| --- | --- |
| ![Shop](docs/screenshots/shop.jpg) | ![Product](docs/screenshots/product.jpg) |
| **Cart** | **Checkout** |
| ![Cart](docs/screenshots/cart.jpg) | ![Checkout](docs/screenshots/checkout.jpg) |
| **Admin dashboard** | **Admin products** |
| ![Admin dashboard](docs/screenshots/admin-dashboard.jpg) | ![Admin products](docs/screenshots/admin-products.jpg) |

**Admin orders**

![Admin orders](docs/screenshots/admin-orders.jpg)

**Mobile** (home, menu, product)

![Mobile views](docs/screenshots/mobile.jpg)

## Tech stack

- PHP 8.3 (no framework), PDO
- MySQL 8
- HTML, CSS (custom, mobile-first, no CSS framework), vanilla JavaScript (no jQuery)
- Developed on MAMP (Apache + MySQL) on macOS

## Security

This project follows the basics every PHP site should have:

- **SQL injection:** every query uses PDO prepared statements (emulated prepares turned off)
- **Passwords:** `password_hash()` / `password_verify()`, rehashed automatically when the algorithm improves
- **CSRF:** a token on every form (including logout), checked with `hash_equals()`
- **XSS:** all output escaped with `htmlspecialchars()` through a small `e()` helper
- **Sessions:** `session_regenerate_id()` on login and logout, HttpOnly + SameSite cookies, strict mode
- **Brute force:** simple login throttling (5 failed attempts per 10 minutes)
- **Uploads:** real MIME type checked with `finfo`, must be a valid image, 2 MB limit, random file names,
  and an `.htaccess` that blocks scripts in the uploads folder
- **Config:** database credentials live in `includes/config.php`, which is git-ignored; `display_errors` is off in production mode
- Only `public/` is served by the web server: `includes/`, `database/` and `scripts/` are not reachable from the browser

## Run it locally with MAMP

1. **Install and start [MAMP](https://www.mamp.info/)** (Apache on port 8888, MySQL on port 8889, user `root` / password `root`).

2. **Get the code**
   ```bash
   cd ~/Documents
   git clone https://github.com/shahredza/sr-worldwide.git
   ```

3. **Serve the `public/` folder.** The easiest way is a symlink in MAMP's `htdocs`:
   ```bash
   ln -s ~/Documents/sr-worldwide/public /Applications/MAMP/htdocs/sr-worldwide
   ```
   The site will be at **http://localhost:8888/sr-worldwide/**.
   (Or point MAMP's document root at the `public/` folder and set `base_url` to `''` in step 5.)

4. **Create the database** in phpMyAdmin (http://localhost:8888/phpMyAdmin/):
   - Click **New**, name it `sr_worldwide`, collation `utf8mb4_unicode_ci`, then **Create**
   - With `sr_worldwide` selected, open **Import**, choose `database/schema.sql`, click **Import**
   - Import `database/seed.sql` the same way (12 demo products and a demo admin)

5. **Create your config file**
   ```bash
   cp includes/config.example.php includes/config.php
   ```
   The defaults already match MAMP. Set `'env' => 'development'` if you want to see PHP errors while working.

6. Open **http://localhost:8888/sr-worldwide/** 🎉

### Demo logins

| Area | URL | Username | Password |
| --- | --- | --- | --- |
| Admin | `/sr-worldwide/admin/` | `admin` | `admin123` |
| Customer | `/sr-worldwide/register.php` | register your own account | |

> ⚠️ **`admin / admin123` is a demo account only.** Change it before putting the site anywhere public:
> `php scripts/create-admin.php admin` sets a new password (it also creates new admins).

### Troubleshooting

- **"Missing includes/config.php"**: you skipped step 5.
- **"Forbidden" or a blank page on macOS:** Apache may not be allowed to read `~/Documents`. Give MAMP
  *Full Disk Access* in System Settings → Privacy & Security, or move the project into `/Applications/MAMP/htdocs/`.
- **Image upload fails:** make sure `public/assets/uploads/` is writable by Apache.

## Project structure

```
public/              Web root: the only folder Apache serves
  index.php          Home
  shop.php           Product grid (all or ?cat=tops|bottoms|accessories)
  product.php        Product detail + add to cart
  cart.php           Cart
  checkout.php       Checkout (login required)
  account.php        My orders
  login.php  register.php  logout.php
  about.php  contact.php  privacy.php
  admin/             Admin login, dashboard, products, orders, users, messages
  assets/            css/, js/, images/, fonts/, products/ (demo photos), uploads/ (git-ignored)
includes/            Bootstrap, DB connection, auth, CSRF, cart, upload helpers, layout
database/            schema.sql, seed.sql
scripts/             create-admin.php (CLI)
legacy/              Original 2025 pages, kept unchanged for reference
docs/screenshots/    Images used in this README
```

## Project history

| | 2025: Semester 1 version | 2026: Rebuild |
| --- | --- | --- |
| Pages | ~30 separate HTML/PHP files, one per product | Database-driven templates |
| Products | Hard-coded in HTML | Stored in MySQL, managed in the admin |
| Cart / checkout | "Buy now" form, address only | Session cart, order items, totals, order status |
| Security | Some prepared statements, `display_errors` on, public admin registration | PDO everywhere, CSRF, escaping, session hardening, no public admin sign-up |
| Design | Desktop-first, inline CSS per page, jQuery date picker, autoplay music | One mobile-first stylesheet, vanilla JS, no autoplay |

The original files are in [`legacy/`](legacy/) for comparison.

## Credits

- **Muhammad Shah Redza**: design and development
- Built as a Semester 1 project for *DFD10033 Big Data Programming Fundamental*, Diploma in Computer Technology (Big Data),
  **TVETMARA Lumut** (IKM Lumut / MARA), January–June 2025. Rebuilt 2026.
- Gothic display font and product photos come from the original 2025 student project.
- This is a portfolio/demo shop: no real products are sold, no payments are taken.
