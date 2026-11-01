<?php
/**
 * Shopping cart stored in the PHP session.
 * $_SESSION['cart'] = ['<productId>|<size>' => quantity, ...]
 */

const CART_MAX_QTY = 10;

function cart_key(int $productId, string $size): string
{
    return $productId . '|' . $size;
}

function cart_add(int $productId, string $size, int $qty): void
{
    $key = cart_key($productId, $size);
    $current = $_SESSION['cart'][$key] ?? 0;
    $_SESSION['cart'][$key] = max(1, min(CART_MAX_QTY, $current + $qty));
}

function cart_set(string $key, int $qty): void
{
    if (!isset($_SESSION['cart'][$key])) {
        return;
    }
    if ($qty <= 0) {
        unset($_SESSION['cart'][$key]);
    } else {
        $_SESSION['cart'][$key] = min(CART_MAX_QTY, $qty);
    }
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}

function cart_count(): int
{
    return array_sum($_SESSION['cart'] ?? []);
}

/**
 * Cart lines with current product data and prices from the database.
 * Products that were deleted or hidden since being added are dropped.
 */
function cart_lines(): array
{
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) {
        return [];
    }

    $ids = array_unique(array_map(fn ($k) => (int) explode('|', $k)[0], array_keys($cart)));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $rows = db_query(
        "SELECT id, name, slug, price, image, sizes FROM products WHERE is_active = 1 AND id IN ($placeholders)",
        $ids
    )->fetchAll();
    $products = array_column($rows, null, 'id');

    $lines = [];
    foreach ($cart as $key => $qty) {
        [$id, $size] = explode('|', $key, 2);
        $product = $products[(int) $id] ?? null;
        if (!$product) {
            unset($_SESSION['cart'][$key]);
            continue;
        }
        $lines[] = [
            'key'      => $key,
            'product'  => $product,
            'size'     => $size,
            'qty'      => $qty,
            'subtotal' => $qty * (float) $product['price'],
        ];
    }
    return $lines;
}

function cart_total(array $lines): float
{
    return array_sum(array_column($lines, 'subtotal'));
}
