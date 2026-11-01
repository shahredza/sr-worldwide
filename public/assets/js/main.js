// SR Worldwide: small UI helpers (no libraries).

document.addEventListener('DOMContentLoaded', () => {
  // Mobile menu
  const toggle = document.querySelector('.nav-toggle');
  if (toggle) {
    toggle.addEventListener('click', () => {
      const open = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });
  }

  // Shop dropdown
  document.querySelectorAll('.has-dropdown').forEach((item) => {
    const btn = item.querySelector('.dropdown-btn');
    btn.addEventListener('click', (event) => {
      event.stopPropagation();
      const open = item.classList.toggle('open');
      btn.setAttribute('aria-expanded', String(open));
    });
  });
  document.addEventListener('click', (event) => {
    document.querySelectorAll('.has-dropdown.open').forEach((item) => {
      if (!item.contains(event.target)) {
        item.classList.remove('open');
        item.querySelector('.dropdown-btn').setAttribute('aria-expanded', 'false');
      }
    });
  });
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('.has-dropdown.open').forEach((item) => item.classList.remove('open'));
    if (document.body.classList.contains('nav-open')) toggle?.click();
  });

  // Product gallery thumbnails
  const mainImg = document.querySelector('.gallery-main img');
  document.querySelectorAll('.gallery-thumbs button').forEach((thumb, _, all) => {
    thumb.addEventListener('click', () => {
      mainImg.src = thumb.dataset.src;
      mainImg.alt = thumb.dataset.alt || '';
      all.forEach((b) => b.setAttribute('aria-pressed', String(b === thumb)));
    });
  });

  // Show / hide password
  document.querySelectorAll('[data-toggle-password]').forEach((box) => {
    const input = document.getElementById(box.dataset.togglePassword);
    box.addEventListener('change', () => { input.type = box.checked ? 'text' : 'password'; });
  });

  // Confirm before destructive actions
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
  });
});
