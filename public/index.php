<?php
require __DIR__ . '/../includes/bootstrap.php';

$featured = db_query(
    'SELECT id, name, slug, price, image, image_back FROM products
     WHERE is_active = 1 AND is_featured = 1 ORDER BY created_at DESC, id LIMIT 4'
)->fetchAll();

// One cover image per category for the category tiles.
$covers = db_query(
    'SELECT c.slug, c.name,
            (SELECT p.image FROM products p WHERE p.category_id = c.id AND p.is_active = 1
             ORDER BY p.is_featured DESC, p.id LIMIT 1) AS image
     FROM categories c ORDER BY c.sort_order'
)->fetchAll();

$bodyClass = 'home';
require __DIR__ . '/../includes/header.php';
?>

<section class="hero">
  <div class="hero-media">
    <img src="<?= e(asset('images/background.jpg')) ?>" alt="" fetchpriority="high">
  </div>
  <div class="hero-content">
    <p class="eyebrow">Streetwear · Since 2025</p>
    <h1>Welcome to SR Worldwide</h1>
    <p>High quality fashion for your everyday style.</p>
    <div class="hero-actions">
      <a class="btn btn-light" href="<?= e(url('shop.php')) ?>">Shop now</a>
      <a class="btn btn-outline-light" href="<?= e(url('about.php')) ?>">Our story</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>Shop by category</h2>
    </div>
    <div class="category-tiles">
      <?php foreach ($covers as $c): ?>
        <a class="category-tile" href="<?= e(url('shop.php?cat=' . urlencode($c['slug']))) ?>">
          <?php if ($c['image']): ?><img src="<?= e(asset($c['image'])) ?>" alt="" loading="lazy"><?php endif; ?>
          <span><?= e($c['name']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($featured): ?>
<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <h2>Featured</h2>
      <a class="btn btn-outline btn-sm" href="<?= e(url('shop.php')) ?>">View all</a>
    </div>
    <?php $products = $featured; require __DIR__ . '/../includes/product-grid.php'; ?>
  </div>
</section>
<?php endif; ?>

<section class="section" id="our-mission">
  <div class="container mission">
    <p class="eyebrow">Our mission</p>
    <h2>What Drives Us</h2>
    <p>
      At SR Worldwide, our vision extends far beyond fashion. We entered this industry not merely to sell clothes,
      but to foster a vibrant, inclusive community. We don’t just offer style: we offer a sense of identity,
      empowerment, and belonging. Fashion is our medium, but community is our true purpose.
    </p>
    <a class="btn btn-outline" href="<?= e(url('about.php')) ?>">Meet the founder</a>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
