<?php
require __DIR__ . '/../includes/bootstrap.php';

$slug = $_GET['cat'] ?? '';
$category = null;

if ($slug !== '') {
    $category = db_query('SELECT id, slug, name FROM categories WHERE slug = ?', [(string) $slug])->fetch();
    if (!$category) {
        not_found();
    }
}

$sort = $_GET['sort'] ?? 'new';
$orderBy = match ($sort) {
    'price-asc'  => 'p.price ASC',
    'price-desc' => 'p.price DESC',
    default      => 'p.created_at DESC, p.id',
};

$sql = "SELECT p.id, p.name, p.slug, p.price, p.image, p.image_back
        FROM products p JOIN categories c ON c.id = p.category_id
        WHERE p.is_active = 1" . ($category ? ' AND p.category_id = ?' : '') . "
        ORDER BY c.sort_order, $orderBy";
$products = db_query($sql, $category ? [$category['id']] : [])->fetchAll();

$pageTitle = $category['name'] ?? 'Shop';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <p class="eyebrow">Shop</p>
  <h1><?= e($category['name'] ?? 'All products') ?></h1>

  <nav class="filter-bar" aria-label="Categories">
    <a class="chip" href="<?= e(url('shop.php')) ?>" <?= $category ? '' : 'aria-current="page"' ?>>All</a>
    <?php foreach (categories() as $cat): ?>
      <a class="chip" href="<?= e(url('shop.php?cat=' . urlencode($cat['slug']))) ?>"
         <?= ($category['slug'] ?? '') === $cat['slug'] ? 'aria-current="page"' : '' ?>><?= e($cat['name']) ?></a>
    <?php endforeach; ?>

    <form method="get" style="margin-left:auto">
      <?php if ($category): ?><input type="hidden" name="cat" value="<?= e($category['slug']) ?>"><?php endif; ?>
      <label class="visually-hidden" for="sort">Sort by</label>
      <select id="sort" name="sort" onchange="this.form.submit()" style="min-height:38px;padding:4px 10px">
        <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Newest</option>
        <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: low to high</option>
        <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: high to low</option>
      </select>
      <noscript><button class="btn btn-sm" type="submit">Sort</button></noscript>
    </form>
  </nav>

  <?php if ($products): ?>
    <?php require __DIR__ . '/../includes/product-grid.php'; ?>
  <?php else: ?>
    <p class="empty-state">No products here yet. Check back soon.</p>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
