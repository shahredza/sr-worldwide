<?php
require __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'About Us';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <div class="about-grid">
    <div>
      <p class="eyebrow">About Us</p>
      <h1>One-person mission</h1>
      <p>
        I’m Muhammad Shah Redza, a Diploma in Computer Technology (Big Data) student at TVETMARA Lumut.
        Before that I spent over a year and a half as a Sales Advisor at H&amp;M, which gave me a strong
        foundation in the fashion industry.
      </p>
      <p>
        SR Worldwide is where my two interests meet: fashion and technology. The goal is an online
        fashion platform that offers high-quality, stylish apparel and a shopping experience that feels
        as good as the clothes.
      </p>
      <a class="btn" href="<?= e(url('shop.php')) ?>">Shop the collection</a>
    </div>
    <div class="about-photos">
      <img src="<?= e(asset('images/aboutme1.jpg')) ?>" alt="Shah Redza" loading="lazy">
      <img src="<?= e(asset('images/aboutme2.jpg')) ?>" alt="" loading="lazy">
      <img src="<?= e(asset('images/aboutme3.jpg')) ?>" alt="" loading="lazy">
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container about-grid" style="align-items:start">
    <div class="mission" style="text-align:left;margin:0">
      <h2>What Drives Us</h2>
      <p>
        At SR Worldwide, our vision extends far beyond fashion. We entered this industry not merely to sell
        clothes, but to foster a vibrant, inclusive community. We don’t just offer style: we offer a sense
        of identity, empowerment, and belonging. Fashion is our medium, but community is our true purpose.
      </p>
    </div>
    <div>
      <h2>The story so far</h2>
      <ol class="timeline">
        <li><strong>Jan–Jun 2025</strong><br><span class="muted">Built as a Semester 1 group project (DFD10033 Big Data Programming Fundamental) with HTML, PHP and MySQL on MAMP.</span></li>
        <li><strong>2026</strong><br><span class="muted">Rebuilt from scratch: database-driven shop, cart and checkout, customer accounts, an admin dashboard, a mobile-first design and secure coding practices.</span></li>
      </ol>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
