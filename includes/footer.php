</main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <a class="brand brand-footer" href="<?= e(url()) ?>">
        <img src="<?= e(asset('images/srlogo.jpg')) ?>" alt="" width="36" height="36">
        <span>SR Worldwide</span>
      </a>
      <p class="muted">High quality fashion for your everyday style.</p>
    </div>
    <div>
      <h2 class="footer-title">Shop</h2>
      <ul class="footer-links">
        <?php foreach (categories() as $cat): ?>
          <li><a href="<?= e(url('shop.php?cat=' . urlencode($cat['slug']))) ?>"><?= e($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h2 class="footer-title">Info</h2>
      <ul class="footer-links">
        <li><a href="<?= e(url('about.php')) ?>">About Us</a></li>
        <li><a href="<?= e(url('contact.php')) ?>">Contact</a></li>
        <li><a href="<?= e(url('privacy.php')) ?>">Privacy Policy</a></li>
      </ul>
    </div>
  </div>

  <div class="container footer-bottom">
    <p>&copy; <?= date('Y') ?> SR Worldwide. Demo shop, no real orders are shipped.</p>
    <p class="credit">
      <img src="<?= e(asset('images/logoikm.png')) ?>" alt="IKM" height="28">
      <img src="<?= e(asset('images/logomara.png')) ?>" alt="MARA" height="28">
      <span>Semester 1 project, TVETMARA Lumut</span>
    </p>
  </div>
</footer>
</body>
</html>
