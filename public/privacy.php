<?php
require __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Privacy Policy';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <div class="prose">
    <p class="eyebrow">Legal</p>
    <h1>Privacy Policy</h1>
    <p>
      This Privacy Policy outlines how SR Worldwide ("we", "us", or "our") collects, uses, and safeguards your
      personal information when you use our services, interact with our website, or communicate with us at
      <strong>hello@srworldwide.example</strong>.
    </p>
    <p>
      "You" refers to all users of our site, including customers, visitors, or anyone whose data we handle.
      By accessing our services, you consent to the practices described in this policy. We encourage you to read it carefully.
    </p>

    <h2>What we collect</h2>
    <p>
      When you register we store your email address, your birthday and a securely hashed version of your password
      (we never store or see your actual password). When you place an order we store the delivery details you enter.
      When you use the contact form we store your name, email and message.
    </p>

    <h2>How we use it</h2>
    <p>Only to run the shop: to let you log in, to process and show your orders, and to reply to your messages.</p>

    <h2>Cookies</h2>
    <p>We use one session cookie to keep you logged in and to remember your cart. We don’t use tracking or advertising cookies.</p>

    <h2>Demo notice</h2>
    <p class="muted">SR Worldwide is a student portfolio project. Please don’t enter real personal or payment details.</p>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
