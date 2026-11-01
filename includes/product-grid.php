<?php
/** Product card grid. Expects $products (id, name, slug, price, image, image_back). */
?>
<ul class="product-grid">
  <?php foreach ($products as $p): ?>
    <li class="product-card">
      <a href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>">
        <div class="thumb">
          <img src="<?= e(asset($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
          <?php if (!empty($p['image_back'])): ?>
            <img class="alt" src="<?= e(asset($p['image_back'])) ?>" alt="" loading="lazy">
          <?php endif; ?>
        </div>
        <h3><?= e($p['name']) ?></h3>
        <p class="price"><?= e(money($p['price'])) ?></p>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
