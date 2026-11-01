<?php
require __DIR__ . '/../../includes/bootstrap.php';

if (current_admin()) {
    redirect('admin/');
}

$username = '';
$error = '';

if (is_post()) {
    csrf_check();
    $username = post('username');
    $password = $_POST['password'] ?? '';

    if (login_throttled('admin')) {
        $error = 'Too many failed attempts. Please wait a few minutes and try again.';
    } else {
        $admin = db_query('SELECT id, password_hash FROM admins WHERE username = ?', [$username])->fetch();
        if ($admin && is_string($password) && password_verify($password, $admin['password_hash'])) {
            login_reset('admin');
            login_admin((int) $admin['id']);
            redirect('admin/');
        }
        login_failed('admin');
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Admin login · SR Worldwide</title>
  <link rel="icon" href="<?= e(asset('images/srlogo.jpg')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
  <script src="<?= e(asset('js/main.js')) ?>" defer></script>
</head>
<body class="admin">
<main class="admin-login">
  <form method="post" class="form-card">
    <?= csrf_field() ?>
    <a class="brand" href="<?= e(url()) ?>">
      <img src="<?= e(asset('images/srlogo.jpg')) ?>" alt="" width="40" height="40">
      <span>SR Worldwide</span>
    </a>
    <h1 style="font-size:1.8rem;text-align:center">Admin login</h1>

    <?php if ($error): ?>
      <div class="alert alert-error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="field">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" autocomplete="username" required value="<?= e($username) ?>">
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>
    </div>
    <button class="btn btn-block" type="submit">Log in</button>
    <p class="form-foot"><a href="<?= e(url()) ?>">← Back to the shop</a></p>
  </form>
</main>
</body>
</html>
