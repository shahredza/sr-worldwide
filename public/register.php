<?php
require __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('account.php');
}

$email = '';
$birthday = '';
$errors = [];

if (is_post()) {
    csrf_check();
    $email = mb_strtolower(post('email'));
    $password = $_POST['password'] ?? '';
    $birthday = post('birthday');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors[] = 'Please enter a valid email address.';
    }
    // Same rule as the original 2025 form: 8+ characters with upper case, lower case and a number.
    if (!is_string($password) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,72}$/', $password)) {
        $errors[] = 'Password must be 8–72 characters and include upper case, lower case and a number.';
    }
    $date = DateTime::createFromFormat('!Y-m-d', $birthday);
    if (!$date || $date->format('Y-m-d') !== $birthday || $date < new DateTime('1900-01-01') || $date > new DateTime('today')) {
        $errors[] = 'Please enter a valid birthday.';
    }

    if (!$errors) {
        $exists = db_query('SELECT 1 FROM users WHERE email = ?', [$email])->fetchColumn();
        if ($exists) {
            $errors[] = 'An account with this email already exists. Please log in.';
        }
    }

    if (!$errors) {
        db_query(
            'INSERT INTO users (email, password_hash, birthday) VALUES (?, ?, ?)',
            [$email, password_hash($password, PASSWORD_DEFAULT), $birthday]
        );
        login_user((int) db()->lastInsertId());
        flash('success', 'Registration successful! Welcome to SR Worldwide!');
        redirect('shop.php');
    }
}

$pageTitle = 'Register';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <div class="auth-wrap">
    <form method="post" class="form-card" novalidate>
      <?= csrf_field() ?>
      <h1 style="font-size:2rem">Create an account</h1>
      <p class="muted">Join SR Worldwide to check out faster and track your orders.</p>

      <?php if ($errors): ?>
        <div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required value="<?= e($email) ?>">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" maxlength="72" required>
        <p class="hint">At least 8 characters, with upper case, lower case and a number.</p>
        <label class="checkbox"><input type="checkbox" data-toggle-password="password"> Show password</label>
      </div>
      <div class="field">
        <label for="birthday">Birthday</label>
        <input id="birthday" name="birthday" type="date" min="1900-01-01" max="<?= date('Y-m-d') ?>" required value="<?= e($birthday) ?>">
      </div>

      <button class="btn btn-block" type="submit">Register</button>
      <p class="form-foot">Already have an account? <a href="<?= e(url('login.php')) ?>">Log in</a></p>
    </form>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
