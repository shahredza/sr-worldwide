<?php
require __DIR__ . '/../includes/bootstrap.php';

$user = require_user();

$orders = db_query(
    'SELECT id, fullname, street, city, state, postcode, total, status, created_at
     FROM orders WHERE user_id = ? ORDER BY created_at DESC, id DESC',
    [$user['id']]
)->fetchAll();

$itemsByOrder = [];
if ($orders) {
    $ids = array_column($orders, 'id');
    $in = implode(',', array_fill(0, count($ids), '?'));
    foreach (db_query("SELECT order_id, product_name, size, unit_price, quantity FROM order_items WHERE order_id IN ($in)", $ids) as $item) {
        $itemsByOrder[$item['order_id']][] = $item;
    }
}

$pageTitle = 'My orders';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page narrow">
  <p class="eyebrow">My account</p>
  <h1>My orders</h1>
  <p class="muted">Signed in as <?= e($user['email']) ?></p>

  <?php if (!$orders): ?>
    <div class="empty-state">
      <p>You haven't placed any orders yet.</p>
      <a class="btn" href="<?= e(url('shop.php')) ?>">Browse the shop</a>
    </div>
  <?php endif; ?>

  <?php foreach ($orders as $o): ?>
    <article class="order-card">
      <header>
        <div>
          <strong>Order #<?= (int) $o['id'] ?></strong>
          <span class="muted"> · <?= e(date('j M Y, g:i a', strtotime($o['created_at']))) ?></span>
        </div>
        <span class="status status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span>
      </header>
      <ul>
        <?php foreach ($itemsByOrder[$o['id']] ?? [] as $item): ?>
          <li><?= (int) $item['quantity'] ?> × <?= e($item['product_name']) ?> (<?= e($item['size']) ?>), <?= e(money($item['unit_price'])) ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="muted" style="margin:12px 0 0;font-size:.9rem">
        Deliver to <?= e($o['fullname']) ?>, <?= e($o['street']) ?>, <?= e($o['postcode']) ?> <?= e($o['city']) ?>, <?= e($o['state']) ?>
      </p>
      <p style="margin:8px 0 0"><strong>Total <?= e(money($o['total'])) ?></strong></p>
    </article>
  <?php endforeach; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
