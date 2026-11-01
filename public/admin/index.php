<?php
require __DIR__ . '/../../includes/bootstrap.php';

$admin = require_admin();

$stats = db_query(
    "SELECT
       (SELECT COUNT(*) FROM products WHERE is_active = 1)                   AS products,
       (SELECT COUNT(*) FROM orders)                                         AS orders,
       (SELECT COUNT(*) FROM orders WHERE status = 'pending')                AS pending,
       (SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'cancelled') AS revenue,
       (SELECT COUNT(*) FROM users)                                          AS users"
)->fetch();

$recent = db_query(
    'SELECT o.id, o.fullname, o.total, o.status, o.created_at, u.email
     FROM orders o LEFT JOIN users u ON u.id = o.user_id
     ORDER BY o.created_at DESC, o.id DESC LIMIT 6'
)->fetchAll();

$messages = db_query(
    'SELECT id, name, subject, created_at FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$pageTitle = 'Dashboard';
require __DIR__ . '/../../includes/admin-header.php';
?>

<div class="admin-head">
  <h1>Dashboard</h1>
  <a class="btn btn-sm" href="<?= e(url('admin/product-form.php')) ?>">+ Add product</a>
</div>

<div class="stats">
  <a class="stat" href="<?= e(url('admin/orders.php')) ?>"><p class="label">Revenue</p><p class="value"><?= e(money($stats['revenue'])) ?></p></a>
  <a class="stat" href="<?= e(url('admin/orders.php')) ?>"><p class="label">Orders</p><p class="value"><?= (int) $stats['orders'] ?></p></a>
  <a class="stat" href="<?= e(url('admin/orders.php?status=pending')) ?>"><p class="label">Pending</p><p class="value"><?= (int) $stats['pending'] ?></p></a>
  <a class="stat" href="<?= e(url('admin/products.php')) ?>"><p class="label">Live products</p><p class="value"><?= (int) $stats['products'] ?></p></a>
  <a class="stat" href="<?= e(url('admin/users.php')) ?>"><p class="label">Customers</p><p class="value"><?= (int) $stats['users'] ?></p></a>
</div>

<section class="panel">
  <h2>Latest orders</h2>
  <?php if (!$recent): ?>
    <p class="muted">No orders yet.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Customer</th><th>Date</th><th>Status</th><th class="num">Total</th></tr></thead>
        <tbody>
          <?php foreach ($recent as $o): ?>
            <tr>
              <td><a href="<?= e(url('admin/orders.php#order-' . $o['id'])) ?>">#<?= (int) $o['id'] ?></a></td>
              <td><?= e($o['fullname']) ?><br><small class="muted"><?= e($o['email'] ?? 'deleted user') ?></small></td>
              <td><?= e(date('j M Y', strtotime($o['created_at']))) ?></td>
              <td><span class="status status-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
              <td class="num"><?= e(money($o['total'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<section class="panel">
  <h2>Unread messages</h2>
  <?php if (!$messages): ?>
    <p class="muted">Inbox zero. Nice.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($messages as $m): ?>
        <li><a href="<?= e(url('admin/messages.php#msg-' . $m['id'])) ?>"><?= e($m['subject']) ?></a>
          <span class="muted">from <?= e($m['name']) ?>, <?= e(date('j M', strtotime($m['created_at']))) ?></span></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
