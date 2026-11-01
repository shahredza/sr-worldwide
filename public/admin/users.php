<?php
require __DIR__ . '/../../includes/bootstrap.php';

$admin = require_admin();

$users = db_query(
    "SELECT u.id, u.email, u.birthday, u.created_at,
            COUNT(o.id) AS order_count,
            COALESCE(SUM(CASE WHEN o.status <> 'cancelled' THEN o.total END), 0) AS spent
     FROM users u LEFT JOIN orders o ON o.user_id = u.id
     GROUP BY u.id ORDER BY u.created_at DESC"
)->fetchAll();

$pageTitle = 'Users';
require APP_ROOT . '/includes/admin-header.php';
?>

<div class="admin-head">
  <h1>Users</h1>
  <span class="muted"><?= count($users) ?> registered</span>
</div>

<?php if (!$users): ?>
  <p class="empty-state">No customers have registered yet.</p>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>Email</th><th>Birthday</th><th>Joined</th><th class="num">Orders</th><th class="num">Spent</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e(date('j M Y', strtotime($u['birthday']))) ?></td>
            <td><?= e(date('j M Y', strtotime($u['created_at']))) ?></td>
            <td class="num"><?= (int) $u['order_count'] ?></td>
            <td class="num"><?= e(money($u['spent'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require APP_ROOT . '/includes/admin-footer.php'; ?>
