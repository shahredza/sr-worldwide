<?php
require __DIR__ . '/../includes/bootstrap.php';

$user = current_user();
$form = ['name' => '', 'email' => $user['email'] ?? '', 'subject' => '', 'message' => ''];
$errors = [];

if (is_post()) {
    csrf_check();
    foreach ($form as $key => $_) {
        $form[$key] = post($key);
    }

    // Honeypot: real people never fill this hidden field in.
    if (post('website') !== '') {
        redirect('contact.php');
    }

    if ($form['name'] === '' || mb_strlen($form['name']) > 100) {
        $errors[] = 'Please enter your name.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL) || strlen($form['email']) > 255) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($form['subject'] === '' || mb_strlen($form['subject']) > 150) {
        $errors[] = 'Please enter a subject (max 150 characters).';
    }
    if (mb_strlen($form['message']) < 10 || mb_strlen($form['message']) > 5000) {
        $errors[] = 'Your message should be between 10 and 5000 characters.';
    }

    if (!$errors) {
        db_query(
            'INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)',
            [$form['name'], $form['email'], $form['subject'], $form['message']]
        );
        flash('success', 'Thanks for your message! We’ll get back to you soon.');
        redirect('contact.php');
    }
}

$pageTitle = 'Contact';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <div class="contact-grid">
    <div>
      <p class="eyebrow">Contact Us</p>
      <h1>Get in touch</h1>
      <p class="muted">Questions about sizing, an order or a collaboration? Send us a message and we’ll reply by email.</p>
      <ul class="contact-list">
        <li><img src="<?= e(asset('images/whatappslogo.png')) ?>" alt=""> <span>WhatsApp · +60 12-345 6789 <small class="muted">(demo)</small></span></li>
        <li><img src="<?= e(asset('images/iglogo.png')) ?>" alt=""> <span>Instagram · @srworldwide</span></li>
        <li><img src="<?= e(asset('images/facebooklogo.png')) ?>" alt=""> <span>Facebook · SR Worldwide</span></li>
        <li><img src="<?= e(asset('images/xlogo.png')) ?>" alt=""> <span>X · @srworldwide</span></li>
      </ul>
      <p class="muted" style="margin-top:16px">Email: hello@srworldwide.example</p>
    </div>

    <form method="post" class="form-card" novalidate>
      <?= csrf_field() ?>
      <?php if ($errors): ?>
        <div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="field">
          <label for="name">Name</label>
          <input id="name" name="name" type="text" maxlength="100" autocomplete="name" required value="<?= e($form['name']) ?>">
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" maxlength="255" autocomplete="email" required value="<?= e($form['email']) ?>">
        </div>
        <div class="field span-2">
          <label for="subject">Subject</label>
          <input id="subject" name="subject" type="text" maxlength="150" required value="<?= e($form['subject']) ?>">
        </div>
        <div class="field span-2">
          <label for="message">Message</label>
          <textarea id="message" name="message" maxlength="5000" required><?= e($form['message']) ?></textarea>
        </div>
      </div>
      <div class="visually-hidden" aria-hidden="true">
        <label for="website">Leave this empty</label>
        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
      </div>
      <button class="btn btn-block" type="submit">Send message</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
