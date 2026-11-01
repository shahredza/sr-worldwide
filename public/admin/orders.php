<?php
require __DIR__ . '/../../includes/bootstrap.php';

$admin = require_admin();

if (is_post()) {
    csrf_check();
    $id = (int) post('id');
    $status = post('status');
    if (in_array($status, ORDER_STATUSES, true)) {
        $changed = db_query('UPDATE orders SET status = ? WHERE id = ?', [$status, $id])->rowCount();
        flash('success', $changed ? "Order #$id marked as $status." : "Order #$id unchanged.");
    } else {
        flash('error', 'Unknown status.');
    }
    $back = 'admin/orders.php';
    if (in_array($_GET['status'] ?? '', ORDER_STATUSES, true)) {
        $back .= '?status=' . $_GET['status'];
    }
    redirect($back . '#order-' . $id);
}

$filter = in_array($_GET['status'] ?? '', ORDER_STATUSES, true) ? $_GET['status'] : '';

$orders = db_query(
    'SELECT o.*, u.email FROM orders o LEFT JOIN users u ON u.id = o.user_id'
    . ($filter ? ' WHERE o.status = ?' : '') . ' ORDER BY o.created_at DESC, o.id DESC',
    $filter ? [$filter] : []
)->fetchAll();

$items = [];
if ($orders) {
    $ids = array_column($orders, 'id');
    $in = implode(',', array_fill(0, count($ids), '?'));
    foreach (db_query("SELECT * FROM order_items WHERE order_id IN ($in) ORDER BY id", $ids) as $item) {
        $items[$item['order_id']][] = $item;
    }
}

$pageTitle = 'Orders';
require APP_ROOT . '/includes/admin-header.php';
?>

<div class="admin-head">
  <h1>Orders</h1>
</div>

<nav class="filter-bar" aria-label="Filter by status">
  <a class="chip" href="<?= e(url('admin/orders.php')) ?>" <?= $filter === '' ? 'aria-current="page"' : '' ?>>All</a>
  <?php foreach (ORDER_STATUSES as $s): ?>
    <a class="chip" href="<?= e(url('admin/orders.php?status=' . $s)) ?>" <?= $filter === $s ? 'aria-current="page"' : '' ?>><?= e(ucfirst($s)) ?></a>
  <?php endforeach; ?>
</nav>

<?php if (!$orders): ?>
  <p class="empty-state">No orders<?= $filter ? ' with status “' . e($filter) . '”' : '' ?> yet.</p>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Date</th><th>Customer</th><th>Delivery</th><th>Items</th><th class="num">Total</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): $list = $items[$o['id']] ?? []; ?>
          <tr id="order-<?= (int) $o['id'] ?>">
            <td>#<?= (int) $o['id'] ?></td>
            <td><?= e(date('j M Y', strtotime($o['created_at']))) ?><br><small class="muted"><?= e(date('g:i a', strtotime($o['created_at']))) ?></small></td>
            <td><?= e($o['fullname']) ?> <small class="muted">(<?= e($o['nickname']) ?>)</small><br><small class="muted"><?= e($o['email'] ?? 'deleted user') ?></small></td>
            <td><small><?= e($o['street']) ?><br><?= e($o['postcode']) ?> <?= e($o['city']) ?>, <?= e($o['state']) ?></small></td>
            <td>
              <details>
                <summary><?= array_sum(array_column($list, 'quantity')) ?> item(s)</summary>
                <ul>
                  <?php foreach ($list as $it): ?>
                    <li><?= (int) $it['quantity'] ?> × <?= e($it['product_name']) ?> (<?= e($it['size']) ?>) @ <?= e(money($it['unit_price'])) ?></li>
                  <?php endforeach; ?>
                </ul>
              </details>
            </td>
            <td class="num"><?= e(money($o['total'])) ?></td>
            <td>
              <form method="post" class="status-form" action="<?= e(url('admin/orders.php' . ($filter ? '?status=' . $filter : ''))) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                <label class="visually-hidden" for="status-<?= (int) $o['id'] ?>">Status for order <?= (int) $o['id'] ?></label>
                <select id="status-<?= (int) $o['id'] ?>" name="status">
                  <?php foreach (ORDER_STATUSES as $s): ?>
                    <option value="<?= e($s) ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm" type="submit">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require APP_ROOT . '/includes/admin-footer.php'; ?>
