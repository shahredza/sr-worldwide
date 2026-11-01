// SR Worldwide: design-only version. All "data" lives in data.js and the visitor's browser (localStorage).
(function () {
  'use strict';

  const ROOT = document.body.dataset.root || '';
  const PAGE = document.body.dataset.page || '';

  /* ---------- Helpers ---------- */
  const $ = (sel, el = document) => el.querySelector(sel);
  const $$ = (sel, el = document) => [...el.querySelectorAll(sel)];
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const money = (n) => 'RM' + Number(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  const url = (p) => ROOT + p;
  const img = (p) => ROOT + 'assets/' + p;
  const params = new URLSearchParams(location.search);
  const fmtDate = (iso, time) => new Date(iso).toLocaleString('en-GB', time
    ? { day: 'numeric', month: 'short', year: 'numeric', hour: 'numeric', minute: '2-digit' }
    : { day: 'numeric', month: 'short', year: 'numeric' });

  // Browser storage can be blocked (private mode etc.), so fall back to memory.
  const memory = {};
  const store = {
    get(key, fallback) {
      try { const v = localStorage.getItem('sr_' + key); return v === null ? fallback : JSON.parse(v); }
      catch (e) { return key in memory ? memory[key] : fallback; }
    },
    set(key, value) {
      try { localStorage.setItem('sr_' + key, JSON.stringify(value)); }
      catch (e) { memory[key] = value; }
    },
  };

  function flash(type, message) {
    try { sessionStorage.setItem('sr_flash', JSON.stringify({ type, message })); } catch (e) { /* ignore */ }
  }
  function showAlert(type, message, target) {
    const box = target || $('#flash');
    if (!box) return;
    box.innerHTML = `<div class="alert alert-${esc(type)}" role="status">${esc(message)}</div>`;
    box.scrollIntoView({ block: 'nearest' });
  }
  function errorList(errors) {
    return `<div class="alert alert-error" role="alert"><ul>${errors.map((e) => `<li>${esc(e)}</li>`).join('')}</ul></div>`;
  }
  function go(path, flashType, flashMessage) {
    if (flashMessage) flash(flashType, flashMessage);
    location.href = url(path);
  }

  const productBy = (key, value) => SR.products.find((p) => p[key] === value);

  /* ---------- Cart & demo account ---------- */
  const cart = {
    all: () => store.get('cart', {}),
    count: () => Object.values(cart.all()).reduce((a, b) => a + b, 0),
    add(id, size, qty) {
      const c = cart.all(); const k = id + '|' + size;
      c[k] = Math.max(1, Math.min(10, (c[k] || 0) + qty));
      store.set('cart', c);
    },
    set(k, qty) {
      const c = cart.all();
      if (qty <= 0) delete c[k]; else c[k] = Math.min(10, qty);
      store.set('cart', c);
    },
    clear: () => store.set('cart', {}),
    lines() {
      return Object.entries(cart.all()).map(([k, qty]) => {
        const [id, size] = k.split('|');
        const product = productBy('id', Number(id));
        return product ? { key: k, product, size, qty, subtotal: qty * product.price } : null;
      }).filter(Boolean);
    },
  };
  const currentUser = () => store.get('user', null);

  /* ---------- Layout ---------- */
  function renderHeader() {
    const user = currentUser();
    const count = cart.count();
    const cat = params.get('cat');
    const el = $('#site-header');
    if (!el) return;
    el.className = 'site-header';
    el.innerHTML = `
      <div class="demo-banner">Design demo · nothing you enter leaves your browser</div>
      <div class="header-inner">
        <a class="brand" href="${url('index.html')}"><img src="${img('images/srlogo.jpg')}" alt="" width="40" height="40"><span>SR Worldwide</span></a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu"><span></span><span></span><span></span></button>
        <nav id="site-nav" class="site-nav" aria-label="Main">
          <ul class="nav-main">
            <li class="has-dropdown">
              <button type="button" class="dropdown-btn" aria-expanded="false">Shop <span aria-hidden="true">▾</span></button>
              <ul class="dropdown">
                <li><a href="${url('shop.html')}">All products</a></li>
                ${SR.categories.map((c) => `<li><a href="${url('shop.html?cat=' + c.slug)}" ${PAGE === 'shop' && cat === c.slug ? 'aria-current="page"' : ''}>${esc(c.name)}</a></li>`).join('')}
              </ul>
            </li>
            <li><a href="${url('about.html')}" ${PAGE === 'about' ? 'aria-current="page"' : ''}>About Us</a></li>
            <li><a href="${url('contact.html')}" ${PAGE === 'contact' ? 'aria-current="page"' : ''}>Contact</a></li>
          </ul>
          <ul class="nav-account">
            ${user
              ? `<li><a href="${url('account.html')}">My orders</a></li><li><button type="button" class="link-btn" id="logout">Log out</button></li>`
              : `<li><a href="${url('login.html')}">Log in</a></li><li><a href="${url('register.html')}">Register</a></li>`}
          </ul>
        </nav>
        <a class="cart-link" href="${url('cart.html')}" aria-label="Cart, ${count} items">
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M6 7h12l-1 13H7L6 7Zm3 0a3 3 0 0 1 6 0"/></svg>
          ${count ? `<span class="cart-count">${count}</span>` : ''}
        </a>
      </div>`;

    $('#logout')?.addEventListener('click', () => {
      store.set('user', null);
      go('index.html', 'success', 'You have been logged out.');
    });
  }

  function renderFooter() {
    const el = $('#site-footer');
    if (!el) return;
    el.className = 'site-footer';
    el.innerHTML = `
      <div class="container footer-grid">
        <div>
          <a class="brand brand-footer" href="${url('index.html')}"><img src="${img('images/srlogo.jpg')}" alt="" width="36" height="36"><span>SR Worldwide</span></a>
          <p class="muted">High quality fashion for your everyday style.</p>
        </div>
        <div>
          <h2 class="footer-title">Shop</h2>
          <ul class="footer-links">${SR.categories.map((c) => `<li><a href="${url('shop.html?cat=' + c.slug)}">${esc(c.name)}</a></li>`).join('')}</ul>
        </div>
        <div>
          <h2 class="footer-title">Info</h2>
          <ul class="footer-links">
            <li><a href="${url('about.html')}">About Us</a></li>
            <li><a href="${url('contact.html')}">Contact</a></li>
            <li><a href="${url('privacy.html')}">Privacy Policy</a></li>
            <li><a href="${url('admin/login.html')}">Admin demo</a></li>
          </ul>
        </div>
      </div>
      <div class="container footer-bottom">
        <p>&copy; ${new Date().getFullYear()} SR Worldwide. Design demo, no real orders are shipped.</p>
        <p class="credit">
          <img src="${img('images/logoikm.png')}" alt="IKM" height="28">
          <img src="${img('images/logomara.png')}" alt="MARA" height="28">
          <span>Semester 1 project, TVETMARA Lumut</span>
        </p>
      </div>`;
  }

  function bindUi() {
    const toggle = $('.nav-toggle');
    toggle?.addEventListener('click', () => {
      const open = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });
    $$('.has-dropdown').forEach((item) => {
      const btn = $('.dropdown-btn', item);
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        btn.setAttribute('aria-expanded', String(item.classList.toggle('open')));
      });
    });
    document.addEventListener('click', (e) => {
      $$('.has-dropdown.open').forEach((item) => {
        if (!item.contains(e.target)) { item.classList.remove('open'); $('.dropdown-btn', item).setAttribute('aria-expanded', 'false'); }
      });
    });
    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      $$('.has-dropdown.open').forEach((item) => item.classList.remove('open'));
      if (document.body.classList.contains('nav-open')) toggle?.click();
    });
    $$('[data-toggle-password]').forEach((box) => {
      box.addEventListener('change', () => { $('#' + box.dataset.togglePassword).type = box.checked ? 'text' : 'password'; });
    });

    // One-time message from the previous page
    try {
      const f = JSON.parse(sessionStorage.getItem('sr_flash') || 'null');
      sessionStorage.removeItem('sr_flash');
      if (f) showAlert(f.type, f.message);
    } catch (e) { /* ignore */ }
  }

  /* ---------- Shared pieces ---------- */
  function productGrid(products) {
    return `<ul class="product-grid">${products.map((p) => `
      <li class="product-card">
        <a href="${url('product.html?slug=' + p.slug)}">
          <div class="thumb">
            <img src="${img(p.image)}" alt="${esc(p.name)}" loading="lazy">
            ${p.back ? `<img class="alt" src="${img(p.back)}" alt="" loading="lazy">` : ''}
          </div>
          <h3>${esc(p.name)}</h3>
          <p class="price">${money(p.price)}</p>
        </a>
      </li>`).join('')}</ul>`;
  }

  function notFound() {
    document.title = 'Not found · SR Worldwide';
    $('#main').innerHTML = `<section class="container page narrow center"><h1>Page not found</h1>
      <p class="muted">We couldn’t find what you were looking for.</p><a class="btn" href="${url('shop.html')}">Back to the shop</a></section>`;
  }

  const validEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) && v.length <= 255;

  /* ---------- Pages ---------- */
  const pages = {
    home() {
      $('#category-tiles').innerHTML = SR.categories.map((c) => {
        const cover = SR.products.find((p) => p.cat === c.slug && p.featured) || SR.products.find((p) => p.cat === c.slug);
        return `<a class="category-tile" href="${url('shop.html?cat=' + c.slug)}"><img src="${img(cover.image)}" alt="" loading="lazy"><span>${esc(c.name)}</span></a>`;
      }).join('');
      $('#featured').innerHTML = productGrid(SR.products.filter((p) => p.featured).slice(0, 4));
    },

    shop() {
      const slug = params.get('cat');
      const category = SR.categories.find((c) => c.slug === slug);
      if (slug && !category) return notFound();
      const sort = params.get('sort') || 'new';

      let list = SR.products.filter((p) => !category || p.cat === category.slug);
      if (sort === 'price-asc') list = [...list].sort((a, b) => a.price - b.price);
      if (sort === 'price-desc') list = [...list].sort((a, b) => b.price - a.price);

      document.title = (category ? category.name : 'Shop') + ' · SR Worldwide';
      $('#shop-title').textContent = category ? category.name : 'All products';
      $('#filters').innerHTML = `
        <a class="chip" href="${url('shop.html')}" ${category ? '' : 'aria-current="page"'}>All</a>
        ${SR.categories.map((c) => `<a class="chip" href="${url('shop.html?cat=' + c.slug)}" ${category === c ? 'aria-current="page"' : ''}>${esc(c.name)}</a>`).join('')}
        <form method="get" style="margin-left:auto">
          ${category ? `<input type="hidden" name="cat" value="${esc(category.slug)}">` : ''}
          <label class="visually-hidden" for="sort">Sort by</label>
          <select id="sort" name="sort" style="min-height:38px;padding:4px 10px">
            <option value="new" ${sort === 'new' ? 'selected' : ''}>Newest</option>
            <option value="price-asc" ${sort === 'price-asc' ? 'selected' : ''}>Price: low to high</option>
            <option value="price-desc" ${sort === 'price-desc' ? 'selected' : ''}>Price: high to low</option>
          </select>
        </form>`;
      $('#sort').addEventListener('change', (e) => e.target.form.submit());
      $('#products').innerHTML = productGrid(list);
    },

    product() {
      const p = productBy('slug', params.get('slug'));
      if (!p) return notFound();
      const cat = SR.categories.find((c) => c.slug === p.cat);
      const images = [p.image, p.back].filter(Boolean);
      document.title = p.name + ' · SR Worldwide';

      $('#product').innerHTML = `
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="${url('shop.html')}">Shop</a> / <a href="${url('shop.html?cat=' + cat.slug)}">${esc(cat.name)}</a> / <span aria-current="page">${esc(p.name)}</span>
        </nav>
        <div class="product-detail">
          <div class="gallery">
            <div class="gallery-main"><img src="${img(images[0])}" alt="${esc(p.name)}"></div>
            ${images.length > 1 ? `<div class="gallery-thumbs">${images.map((src, i) => `
              <button type="button" data-src="${img(src)}" aria-pressed="${i === 0}" aria-label="Show image ${i + 1}"><img src="${img(src)}" alt=""></button>`).join('')}</div>` : ''}
          </div>
          <div class="product-info">
            <p class="eyebrow">${esc(cat.name)}</p>
            <h1>${esc(p.name)}</h1>
            <p class="price">${money(p.price)}</p>
            <div id="form-errors"></div>
            <form id="add-form" novalidate>
              <fieldset class="size-options">
                <legend>Size</legend>
                ${p.sizes.map((s) => `<label><input type="radio" name="size" value="${esc(s)}" ${p.sizes.length === 1 ? 'checked' : ''}><span>${esc(s)}</span></label>`).join('')}
              </fieldset>
              <div class="qty-row">
                <div class="field"><label for="qty">Qty</label><input id="qty" type="number" name="qty" value="1" min="1" max="10"></div>
                <button class="btn" type="submit" style="flex:1">Add to cart</button>
              </div>
            </form>
            <div class="description"><p>${esc(p.description)}</p></div>
          </div>
        </div>`;

      const mainImg = $('.gallery-main img');
      $$('.gallery-thumbs button').forEach((b, _, all) => b.addEventListener('click', () => {
        mainImg.src = b.dataset.src;
        all.forEach((x) => x.setAttribute('aria-pressed', String(x === b)));
      }));

      $('#add-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const size = $('input[name=size]:checked')?.value;
        const qty = parseInt($('#qty').value, 10);
        const errors = [];
        if (!size) errors.push('Please choose a size.');
        if (!(qty >= 1 && qty <= 10)) errors.push('Quantity must be between 1 and 10.');
        if (errors.length) { $('#form-errors').innerHTML = errorList(errors); return; }
        $('#form-errors').innerHTML = '';
        cart.add(p.id, size, qty);
        renderHeader(); bindHeaderOnly();
        showAlert('success', `${p.name} (${size}) added to your cart.`);
      });

      const related = SR.products.filter((x) => x.cat === p.cat && x.id !== p.id).slice(0, 4);
      $('#related').innerHTML = productGrid(related);
    },

    cart() {
      const render = () => {
        const lines = cart.lines();
        const total = lines.reduce((a, l) => a + l.subtotal, 0);
        renderHeader(); bindHeaderOnly();
        if (!lines.length) {
          $('#cart').innerHTML = `<div class="empty-state"><p>Your cart is empty.</p><a class="btn" href="${url('shop.html')}">Start shopping</a></div>`;
          return;
        }
        $('#cart').innerHTML = `
          <div class="cart-layout">
            <div>
              <ul class="cart-list">${lines.map((l) => `
                <li class="cart-item">
                  <img src="${img(l.product.image)}" alt="">
                  <div>
                    <h3><a href="${url('product.html?slug=' + l.product.slug)}">${esc(l.product.name)}</a></h3>
                    <p class="meta">Size ${esc(l.size)} · ${money(l.product.price)} each · <strong>${money(l.subtotal)}</strong></p>
                    <div class="cart-actions">
                      <label class="visually-hidden" for="q-${esc(l.key)}">Quantity</label>
                      <input id="q-${esc(l.key)}" type="number" min="0" max="10" value="${l.qty}" data-key="${esc(l.key)}">
                      <button class="btn btn-outline btn-sm" type="button" data-update="${esc(l.key)}">Update</button>
                      <button class="link-btn" type="button" data-remove="${esc(l.key)}">Remove</button>
                    </div>
                  </div>
                </li>`).join('')}
              </ul>
              <button class="link-btn muted" type="button" id="clear-cart">Empty cart</button>
            </div>
            <aside class="summary" aria-label="Order summary">
              <h2 style="font-size:1.4rem">Summary</h2>
              <div class="summary-row"><span>Items</span><span>${cart.count()}</span></div>
              <div class="summary-row"><span>Shipping</span><span>Free</span></div>
              <div class="summary-row summary-total"><span>Total</span><span>${money(total)}</span></div>
              <a class="btn btn-block" style="margin-top:16px" href="${url('checkout.html')}">Checkout</a>
              <a class="btn btn-outline btn-block" style="margin-top:10px" href="${url('shop.html')}">Continue shopping</a>
            </aside>
          </div>`;
        $$('[data-update]').forEach((b) => b.addEventListener('click', () => {
          cart.set(b.dataset.update, parseInt($(`input[data-key="${CSS.escape(b.dataset.update)}"]`).value, 10) || 0);
          render(); showAlert('success', 'Cart updated.');
        }));
        $$('[data-remove]').forEach((b) => b.addEventListener('click', () => {
          cart.set(b.dataset.remove, 0); render(); showAlert('success', 'Item removed.');
        }));
        $('#clear-cart').addEventListener('click', () => {
          if (confirm('Remove everything from your cart?')) { cart.clear(); render(); showAlert('success', 'Cart emptied.'); }
        });
      };
      render();
    },

    checkout() {
      const user = currentUser();
      if (!user) {
        try { sessionStorage.setItem('sr_next', 'checkout.html'); } catch (e) { /* ignore */ }
        return go('login.html', 'info', 'Please log in first.');
      }
      const lines = cart.lines();
      if (!lines.length) return go('cart.html', 'info', 'Your cart is empty.');
      const total = lines.reduce((a, l) => a + l.subtotal, 0);

      $('#state').innerHTML = '<option value="">Choose…</option>' + SR.states.map((s) => `<option>${esc(s)}</option>`).join('');
      $('#place-order').textContent = 'Place order · ' + money(total);
      $('#order-summary').innerHTML = `<h2 style="font-size:1.4rem">Your order</h2>
        ${lines.map((l) => `<div class="summary-row"><span>${l.qty} × ${esc(l.product.name)} (${esc(l.size)})</span><span>${money(l.subtotal)}</span></div>`).join('')}
        <div class="summary-row"><span>Shipping</span><span>Free</span></div>
        <div class="summary-row summary-total"><span>Total</span><span>${money(total)}</span></div>`;

      $('#checkout-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const f = Object.fromEntries(new FormData(e.target).entries());
        Object.keys(f).forEach((k) => (f[k] = f[k].trim()));
        const errors = [];
        if (!f.fullname) errors.push('Please enter your full name.');
        if (!f.nickname) errors.push('Please enter a nickname.');
        if (!f.street) errors.push('Please enter your street address.');
        if (!f.city) errors.push('Please enter your city.');
        if (!SR.states.includes(f.state)) errors.push('Please choose a state.');
        if (!/^\d{5}$/.test(f.postcode)) errors.push('Postcode must be 5 digits.');
        if (errors.length) { $('#form-errors').innerHTML = errorList(errors); $('#form-errors').scrollIntoView({ block: 'nearest' }); return; }

        const orders = store.get('orders', []);
        const id = 2000 + orders.length + 1;
        orders.unshift({
          id, email: user.email, ...f, status: 'pending', date: new Date().toISOString(),
          items: lines.map((l) => ({ name: l.product.name, size: l.size, price: l.product.price, qty: l.qty })),
        });
        store.set('orders', orders);
        cart.clear();
        go('account.html', 'success', `Thank you! Order #${id} has been placed (demo, saved in this browser only).`);
      });
    },

    account() {
      const user = currentUser();
      if (!user) return go('login.html', 'info', 'Please log in first.');
      const orders = store.get('orders', []).filter((o) => o.email === user.email);
      $('#signed-in').textContent = 'Signed in as ' + user.email;
      $('#orders').innerHTML = !orders.length
        ? `<div class="empty-state"><p>You haven’t placed any orders yet.</p><a class="btn" href="${url('shop.html')}">Browse the shop</a></div>`
        : orders.map((o) => {
          const total = o.items.reduce((a, i) => a + i.price * i.qty, 0);
          return `<article class="order-card">
            <header><div><strong>Order #${o.id}</strong><span class="muted"> · ${fmtDate(o.date, true)}</span></div>
              <span class="status status-${esc(o.status)}">${esc(o.status)}</span></header>
            <ul>${o.items.map((i) => `<li>${i.qty} × ${esc(i.name)} (${esc(i.size)}), ${money(i.price)}</li>`).join('')}</ul>
            <p class="muted" style="margin:12px 0 0;font-size:.9rem">Deliver to ${esc(o.fullname)}, ${esc(o.street)}, ${esc(o.postcode)} ${esc(o.city)}, ${esc(o.state)}</p>
            <p style="margin:8px 0 0"><strong>Total ${money(total)}</strong></p>
          </article>`;
        }).join('');
    },

    login() {
      if (currentUser()) return go('account.html');
      $('#login-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const email = $('#email').value.trim().toLowerCase();
        if (!validEmail(email) || !$('#password').value) {
          $('#form-errors').innerHTML = errorList(['Please enter your email and password.']);
          return;
        }
        // Demo: any email + password logs in. Passwords are never stored.
        store.set('user', { email });
        let next = 'account.html';
        try { next = sessionStorage.getItem('sr_next') || next; sessionStorage.removeItem('sr_next'); } catch (err) { /* ignore */ }
        go(next === 'checkout.html' ? next : 'account.html', 'success', 'Welcome back!');
      });
    },

    register() {
      if (currentUser()) return go('account.html');
      $('#birthday').max = new Date().toISOString().slice(0, 10);
      $('#register-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const email = $('#email').value.trim().toLowerCase();
        const password = $('#password').value;
        const birthday = $('#birthday').value;
        const errors = [];
        if (!validEmail(email)) errors.push('Please enter a valid email address.');
        // Same rule as the original 2025 form
        if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,72}$/.test(password)) errors.push('Password must be 8–72 characters and include upper case, lower case and a number.');
        if (!birthday || birthday < '1900-01-01' || birthday > $('#birthday').max) errors.push('Please enter a valid birthday.');
        if (errors.length) { $('#form-errors').innerHTML = errorList(errors); return; }
        store.set('user', { email });
        go('shop.html', 'success', 'Registration successful! Welcome to SR Worldwide! (demo account, saved in this browser only)');
      });
    },

    contact() {
      const user = currentUser();
      if (user) $('#email').value = user.email;
      $('#contact-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const f = Object.fromEntries(new FormData(e.target).entries());
        Object.keys(f).forEach((k) => (f[k] = f[k].trim()));
        const errors = [];
        if (!f.name) errors.push('Please enter your name.');
        if (!validEmail(f.email)) errors.push('Please enter a valid email address.');
        if (!f.subject) errors.push('Please enter a subject.');
        if (f.message.length < 10) errors.push('Your message should be at least 10 characters.');
        if (errors.length) { $('#form-errors').innerHTML = errorList(errors); return; }
        const messages = store.get('messages', []);
        messages.unshift({ id: 100 + messages.length, ...f, date: new Date().toISOString(), read: false });
        store.set('messages', messages);
        e.target.reset();
        $('#form-errors').innerHTML = '';
        showAlert('success', 'Thanks for your message! (Demo: it’s only saved in this browser, and you can see it in the admin demo.)');
      });
    },
  };

  function bindHeaderOnly() {
    // Re-bind header buttons after re-rendering it (cart count changes).
    const toggle = $('.nav-toggle');
    toggle?.addEventListener('click', () => {
      const open = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', String(open));
    });
    $$('.has-dropdown').forEach((item) => {
      const btn = $('.dropdown-btn', item);
      btn.addEventListener('click', (e) => { e.stopPropagation(); btn.setAttribute('aria-expanded', String(item.classList.toggle('open'))); });
    });
  }

  // Expose a few helpers for admin.js
  SR.util = { $, $$, esc, money, url, img, store, fmtDate, flash, showAlert, params };

  if (!document.body.classList.contains('admin')) {
    renderHeader();
    renderFooter();
    bindUi();
    pages[PAGE]?.();
  }
})();
