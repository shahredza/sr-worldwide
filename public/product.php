<?php
require __DIR__ . '/../includes/bootstrap.php';

$slug = (string) ($_GET['slug'] ?? '');
$product = db_query(
    'SELECT p.*, c.name AS category_name, c.slug AS category_slug
     FROM products p JOIN categories c ON c.id = p.category_id
     WHERE p.slug = ? AND p.is_active = 1',
    [$slug]
)->fetch();

if (!$product) {
    not_found();
}

$sizes = product_sizes($product['sizes']);
$errors = [];

// Add to cart
if (is_post()) {
    csrf_check();
    $size = post('size');
    $qty = (int) post('qty');

    if (!in_array($size, $sizes, true)) {
        $errors[] = 'Please choose a size.';
    }
    if ($qty < 1 || $qty > CART_MAX_QTY) {
        $errors[] = 'Quantity must be between 1 and ' . CART_MAX_QTY . '.';
    }

    if (!$errors) {
        cart_add((int) $product['id'], $size, $qty);
        flash('success', $product['name'] . ' (' . $size . ') added to your cart.');
        redirect('product.php?slug=' . urlencode($product['slug']));
    }
}

$images = array_filter([$product['image'], $product['image_back']]);

$related = db_query(
    'SELECT id, name, slug, price, image, image_back FROM products
     WHERE category_id = ? AND id <> ? AND is_active = 1 ORDER BY RAND() LIMIT 4',
    [$product['category_id'], $product['id']]
)->fetchAll();

$pageTitle = $product['name'];
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('shop.php')) ?>">Shop</a> /
    <a href="<?= e(url('shop.php?cat=' . urlencode($product['category_slug']))) ?>"><?= e($product['category_name']) ?></a> /
    <span aria-current="page"><?= e($product['name']) ?></span>
  </nav>

  <div class="product-detail">
    <div class="gallery">
      <div class="gallery-main">
        <img src="<?= e(asset($images[0])) ?>" alt="<?= e($product['name']) ?>">
      </div>
      <?php if (count($images) > 1): ?>
        <div class="gallery-thumbs">
          <?php foreach (array_values($images) as $i => $img): ?>
            <button type="button" data-src="<?= e(asset($img)) ?>" data-alt="<?= e($product['name']) ?>"
                    aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Show image <?= $i + 1 ?>">
              <img src="<?= e(asset($img)) ?>" alt="">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="product-info">
      <p class="eyebrow"><?= e($product['category_name']) ?></p>
      <h1><?= e($product['name']) ?></h1>
      <p class="price"><?= e(money($product['price'])) ?></p>

      <?php if ($errors): ?>
        <div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <form method="post">
        <?= csrf_field() ?>
        <fieldset class="size-options">
          <legend>Size</legend>
          <?php foreach ($sizes as $i => $s): ?>
            <label>
              <input type="radio" name="size" value="<?= e($s) ?>" required
                     <?= (post('size') === $s || count($sizes) === 1) ? 'checked' : '' ?>>
              <span><?= e($s) ?></span>
            </label>
          <?php endforeach; ?>
        </fieldset>

        <div class="qty-row">
          <div class="field">
            <label for="qty">Qty</label>
            <input id="qty" type="number" name="qty" value="1" min="1" max="<?= CART_MAX_QTY ?>" required>
          </div>
          <button class="btn" type="submit" style="flex:1">Add to cart</button>
        </div>
      </form>

      <div class="description">
        <?php foreach (preg_split('/\R{2,}/', $product['description']) as $para): ?>
          <p><?= nl2br(e($para)) ?></p>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section section-soft">
  <div class="container">
    <div class="section-head"><h2>You may also like</h2></div>
    <?php $products = $related; require __DIR__ . '/../includes/product-grid.php'; ?>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
