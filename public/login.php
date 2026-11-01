<?php
require __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('account.php');
}

$email = '';
$error = '';

if (is_post()) {
    csrf_check();
    $email = mb_strtolower(post('email'));
    $password = $_POST['password'] ?? '';

    if (login_throttled('user')) {
        $error = 'Too many failed attempts. Please wait a few minutes and try again.';
    } else {
        $user = db_query('SELECT id, password_hash FROM users WHERE email = ?', [$email])->fetch();

        if ($user && is_string($password) && password_verify($password, $user['password_hash'])) {
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                db_query('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }
            login_reset('user');
            login_user((int) $user['id']);

            // Go back to the page that asked for login (only same-site paths).
            $next = $_SESSION['after_login'] ?? '';
            unset($_SESSION['after_login']);
            if (is_string($next) && str_starts_with($next, BASE_URL . '/') && !str_starts_with($next, '//')) {
                header('Location: ' . $next);
                exit;
            }
            flash('success', 'Welcome back!');
            redirect('account.php');
        }

        login_failed('user');
        $error = 'Invalid email or password.';
    }
}

$pageTitle = 'Log in';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <div class="auth-wrap">
    <form method="post" class="form-card">
      <?= csrf_field() ?>
      <h1 style="font-size:2rem">Log in</h1>

      <?php if ($error): ?>
        <div class="alert alert-error" role="alert"><?= e($error) ?></div>
      <?php endif; ?>

      <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required value="<?= e($email) ?>">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <label class="checkbox"><input type="checkbox" data-toggle-password="password"> Show password</label>
      </div>

      <button class="btn btn-block" type="submit">Log in</button>
      <p class="form-foot">New here? <a href="<?= e(url('register.php')) ?>">Create an account</a></p>
    </form>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
