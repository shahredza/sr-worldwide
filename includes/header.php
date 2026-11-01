<?php
/**
 * Public site header. Set $pageTitle (and optionally $bodyClass) before including.
 */
$pageTitle = isset($pageTitle) ? $pageTitle . ' · SR Worldwide' : 'SR Worldwide';
$user = current_user();
$cartCount = cart_count();
$currentCat = $_GET['cat'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="SR Worldwide: streetwear tops, bottoms and accessories.">
  <link rel="icon" href="<?= e(asset('images/srlogo.jpg')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
  <script src="<?= e(asset('js/main.js')) ?>" defer></script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header">
  <div class="header-inner">
    <a class="brand" href="<?= e(url()) ?>">
      <img src="<?= e(asset('images/srlogo.jpg')) ?>" alt="" width="40" height="40">
      <span>SR Worldwide</span>
    </a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>

    <nav id="site-nav" class="site-nav" aria-label="Main">
      <ul class="nav-main">
        <li class="has-dropdown">
          <button type="button" class="dropdown-btn" aria-expanded="false">Shop <span aria-hidden="true">▾</span></button>
          <ul class="dropdown">
            <li><a href="<?= e(url('shop.php')) ?>">All products</a></li>
            <?php foreach (categories() as $cat): ?>
              <li><a href="<?= e(url('shop.php?cat=' . urlencode($cat['slug']))) ?>"
                     <?= $currentCat === $cat['slug'] ? 'aria-current="page"' : '' ?>><?= e($cat['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li><a href="<?= e(url('about.php')) ?>">About Us</a></li>
        <li><a href="<?= e(url('contact.php')) ?>">Contact</a></li>
      </ul>

      <ul class="nav-account">
        <?php if ($user): ?>
          <li><a href="<?= e(url('account.php')) ?>">My orders</a></li>
          <li>
            <form method="post" action="<?= e(url('logout.php')) ?>" class="inline-form">
              <?= csrf_field() ?>
              <button type="submit" class="link-btn">Log out</button>
            </form>
          </li>
        <?php else: ?>
          <li><a href="<?= e(url('login.php')) ?>">Log in</a></li>
          <li><a href="<?= e(url('register.php')) ?>">Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <a class="cart-link" href="<?= e(url('cart.php')) ?>" aria-label="Cart, <?= $cartCount ?> items">
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M6 7h12l-1 13H7L6 7Zm3 0a3 3 0 0 1 6 0"/></svg>
      <?php if ($cartCount > 0): ?><span class="cart-count"><?= $cartCount ?></span><?php endif; ?>
    </a>
  </div>
</header>

<main id="main">
<?php $flashes = take_flashes(); if ($flashes): ?>
  <div class="container flashes">
    <?php foreach ($flashes as $f): ?>
      <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
