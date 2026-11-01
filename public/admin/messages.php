<?php
require __DIR__ . '/../../includes/bootstrap.php';

$admin = require_admin();

if (is_post()) {
    csrf_check();
    $id = (int) post('id');
    match (post('action')) {
        'read'   => db_query('UPDATE contact_messages SET is_read = 1 WHERE id = ?', [$id]),
        'unread' => db_query('UPDATE contact_messages SET is_read = 0 WHERE id = ?', [$id]),
        'delete' => db_query('DELETE FROM contact_messages WHERE id = ?', [$id]),
        default  => null,
    };
    if (post('action') === 'delete') {
        flash('success', 'Message deleted.');
    }
    redirect('admin/messages.php');
}

$messages = db_query('SELECT * FROM contact_messages ORDER BY is_read, created_at DESC, id DESC')->fetchAll();

$pageTitle = 'Messages';
require APP_ROOT . '/includes/admin-header.php';
?>

<div class="admin-head">
  <h1>Messages</h1>
</div>

<?php if (!$messages): ?>
  <p class="empty-state">No messages yet.</p>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>From</th><th>Message</th><th>Received</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($messages as $m): ?>
          <tr id="msg-<?= (int) $m['id'] ?>" class="<?= $m['is_read'] ? 'row-muted' : 'unread' ?>">
            <td>
              <strong><?= e($m['name']) ?></strong><br>
              <a href="mailto:<?= e($m['email']) ?>?subject=<?= e(rawurlencode('Re: ' . $m['subject'])) ?>"><?= e($m['email']) ?></a>
            </td>
            <td style="min-width:260px">
              <strong><?= e($m['subject']) ?></strong>
              <p class="message-body"><?= e($m['message']) ?></p>
            </td>
            <td><?= e(date('j M Y, g:i a', strtotime($m['created_at']))) ?></td>
            <td>
              <div class="row-actions">
                <form method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                  <input type="hidden" name="action" value="<?= $m['is_read'] ? 'unread' : 'read' ?>">
                  <button class="btn btn-outline btn-sm" type="submit"><?= $m['is_read'] ? 'Mark unread' : 'Mark read' ?></button>
                </form>
                <form method="post" data-confirm="Delete this message?">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require APP_ROOT . '/includes/admin-footer.php'; ?>
