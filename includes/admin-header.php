<?php
/** Admin layout header. Expects $pageTitle and $admin (from require_admin()). */
$adminNav = [
    'index.php'    => 'Dashboard',
    'products.php' => 'Products',
    'orders.php'   => 'Orders',
    'users.php'    => 'Users',
    'messages.php' => 'Messages',
];
$currentFile = basename($_SERVER['SCRIPT_NAME']);
if ($currentFile === 'product-form.php') {
    $currentFile = 'products.php';
}
$unread = (int) db_query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title><?= e($pageTitle) ?> · SR Worldwide Admin</title>
  <link rel="icon" href="<?= e(asset('images/srlogo.jpg')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
  <script src="<?= e(asset('js/main.js')) ?>" defer></script>
</head>
<body class="admin">
<header class="admin-bar">
  <a class="brand" href="<?= e(url('admin/')) ?>">
    <img src="<?= e(asset('images/srlogo.jpg')) ?>" alt="" width="32" height="32">
    <span>SR Admin</span>
  </a>
  <nav class="admin-nav" aria-label="Admin">
    <?php foreach ($adminNav as $file => $label): ?>
      <a href="<?= e(url('admin/' . $file)) ?>" <?= $currentFile === $file ? 'aria-current="page"' : '' ?>>
        <?= e($label) ?><?php if ($file === 'messages.php' && $unread): ?> <span class="pill"><?= $unread ?></span><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="admin-user">
    <a href="<?= e(url()) ?>" target="_blank" rel="noopener">View site ↗</a>
    <form method="post" action="<?= e(url('admin/logout.php')) ?>" class="inline-form">
      <?= csrf_field() ?>
      <button type="submit" class="link-btn">Log out (<?= e($admin['username']) ?>)</button>
    </form>
  </div>
</header>

<main id="main" class="admin-main">
  <?php foreach (take_flashes() as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
  <?php endforeach; ?>
