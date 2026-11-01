<?php
/**
 * Small helpers used across the site.
 */

/** Escape for HTML output. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build a site URL, e.g. url('shop.php?cat=tops'). */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** URL for a product image path stored in the DB (relative to public/assets/). */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function money(float|string $amount): string
{
    return 'RM' . number_format((float) $amount, 2);
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/** Read a trimmed string from POST. */
function post(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

/* Flash messages: shown once on the next page load. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/** Show a 404 page and stop. */
function not_found(): never
{
    http_response_code(404);
    $pageTitle = 'Not found';
    require APP_ROOT . '/includes/header.php';
    echo '<section class="container page narrow center"><h1>Page not found</h1>'
        . '<p class="muted">We couldn\'t find what you were looking for.</p>'
        . '<a class="btn" href="' . e(url('shop.php')) . '">Back to the shop</a></section>';
    require APP_ROOT . '/includes/footer.php';
    exit;
}

/** Categories for the navigation (cached per request). */
function categories(): array
{
    static $cats = null;
    return $cats ??= db_query('SELECT id, slug, name FROM categories ORDER BY sort_order, name')->fetchAll();
}

/** Sizes stored as "S,M,L" -> ['S','M','L']. */
function product_sizes(?string $sizes): array
{
    return array_values(array_filter(array_map('trim', explode(',', (string) $sizes))));
}

const ORDER_STATUSES = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];

const MY_STATES = [
    'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis',
    'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu',
    'W.P. Kuala Lumpur', 'W.P. Labuan', 'W.P. Putrajaya',
];
