<?php
require __DIR__ . '/../includes/bootstrap.php';

if (is_post()) {
    csrf_check();
    $action = post('action');
    $key = post('key');

    if ($action === 'update') {
        cart_set($key, (int) post('qty'));
        flash('success', 'Cart updated.');
    } elseif ($action === 'remove') {
        cart_set($key, 0);
        flash('success', 'Item removed.');
    } elseif ($action === 'clear') {
        cart_clear();
        flash('success', 'Cart emptied.');
    }
    redirect('cart.php');
}

$lines = cart_lines();
$total = cart_total($lines);

$pageTitle = 'Cart';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <h1>Your cart</h1>

  <?php if (!$lines): ?>
    <div class="empty-state">
      <p>Your cart is empty.</p>
      <a class="btn" href="<?= e(url('shop.php')) ?>">Start shopping</a>
    </div>
  <?php else: ?>
    <div class="cart-layout">
      <div>
        <ul class="cart-list">
          <?php foreach ($lines as $line): $p = $line['product']; ?>
            <li class="cart-item">
              <img src="<?= e(asset($p['image'])) ?>" alt="">
              <div>
                <h3><a href="<?= e(url('product.php?slug=' . urlencode($p['slug']))) ?>"><?= e($p['name']) ?></a></h3>
                <p class="meta">Size <?= e($line['size']) ?> · <?= e(money($p['price'])) ?> each · <strong><?= e(money($line['subtotal'])) ?></strong></p>
                <div class="cart-actions">
                  <form method="post" class="cart-actions">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="key" value="<?= e($line['key']) ?>">
                    <label class="visually-hidden" for="qty-<?= e(md5($line['key'])) ?>">Quantity</label>
                    <input id="qty-<?= e(md5($line['key'])) ?>" type="number" name="qty" min="0" max="<?= CART_MAX_QTY ?>" value="<?= (int) $line['qty'] ?>">
                    <button class="btn btn-outline btn-sm" type="submit">Update</button>
                  </form>
                  <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="key" value="<?= e($line['key']) ?>">
                    <button class="link-btn" type="submit">Remove</button>
                  </form>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <form method="post" data-confirm="Remove everything from your cart?">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="clear">
          <button class="link-btn muted" type="submit">Empty cart</button>
        </form>
      </div>

      <aside class="summary" aria-label="Order summary">
        <h2 style="font-size:1.4rem">Summary</h2>
        <div class="summary-row"><span>Items</span><span><?= cart_count() ?></span></div>
        <div class="summary-row"><span>Shipping</span><span>Free</span></div>
        <div class="summary-row summary-total"><span>Total</span><span><?= e(money($total)) ?></span></div>
        <a class="btn btn-block" style="margin-top:16px" href="<?= e(url('checkout.php')) ?>">Checkout</a>
        <a class="btn btn-outline btn-block" style="margin-top:10px" href="<?= e(url('shop.php')) ?>">Continue shopping</a>
      </aside>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
