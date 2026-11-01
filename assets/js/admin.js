// SR Worldwide admin: design demo. Uses sample data from data.js plus anything created in this browser.
(function () {
  'use strict';
  const { $, $$, esc, money, url, img, store, fmtDate, flash, showAlert, params } = SR.util;
  const PAGE = document.body.dataset.page;

  const DEMO_NOTE = 'Design demo: changes to products aren’t saved.';

  /* ---------- Data (sample + this browser) ---------- */
  function allOrders() {
    const overrides = store.get('order_status', {});
    const local = store.get('orders', []).map((o) => ({ ...o, local: true }));
    return [...local, ...SR.sampleOrders]
      .map((o) => ({
        ...o,
        status: overrides[o.id] || o.status,
        email: o.email || (SR.sampleUsers.find((u) => u.id === o.userId) || {}).email,
        total: o.items.reduce((a, i) => a + i.price * i.qty, 0),
      }))
      .sort((a, b) => b.date.localeCompare(a.date));
  }
  function allMessages() {
    const read = store.get('msg_read', {});
    const deleted = store.get('msg_deleted', []);
    return [...store.get('messages', []), ...SR.sampleMessages]
      .filter((m) => !deleted.includes(m.id))
      .map((m) => ({ ...m, read: m.id in read ? read[m.id] : m.read }))
      .sort((a, b) => Number(a.read) - Number(b.read) || b.date.localeCompare(a.date));
  }

  /* ---------- Layout ---------- */
  function renderBar() {
    const nav = [['index.html', 'Dashboard'], ['products.html', 'Products'], ['orders.html', 'Orders'], ['users.html', 'Users'], ['messages.html', 'Messages']];
    const current = PAGE === 'admin-product-form' ? 'products.html' : PAGE.replace('admin-', '') + '.html';
    const unread = allMessages().filter((m) => !m.read).length;
    $('#admin-bar').innerHTML = `
      <a class="brand" href="index.html"><img src="${img('images/srlogo.jpg')}" alt="" width="32" height="32"><span>SR Admin</span></a>
      <nav class="admin-nav" aria-label="Admin">
        ${nav.map(([f, label]) => `<a href="${f}" ${f === current ? 'aria-current="page"' : ''}>${label}${f === 'messages.html' && unread ? ` <span class="pill">${unread}</span>` : ''}</a>`).join('')}
      </nav>
      <div class="admin-user">
        <a href="${url('index.html')}" target="_blank" rel="noopener">View site ↗</a>
        <button type="button" class="link-btn" id="admin-logout">Log out (admin)</button>
      </div>`;
    $('#admin-logout').addEventListener('click', () => { store.set('admin', false); location.href = 'login.html'; });
  }

  function showFlash() {
    try {
      const f = JSON.parse(sessionStorage.getItem('sr_flash') || 'null');
      sessionStorage.removeItem('sr_flash');
      if (f) showAlert(f.type, f.message);
    } catch (e) { /* ignore */ }
  }

  const statusBadge = (s) => `<span class="status status-${esc(s)}">${esc(s)}</span>`;

  /* ---------- Pages ---------- */
  const pages = {
    'admin-login'() {
      if (store.get('admin', false)) { location.href = 'index.html'; return; }
      $('#admin-login-form').addEventListener('submit', (e) => {
        e.preventDefault();
        if ($('#username').value.trim() === 'admin' && $('#password').value === 'admin123') {
          store.set('admin', true);
          location.href = 'index.html';
        } else {
          $('#form-errors').innerHTML = '<div class="alert alert-error" role="alert">Invalid username or password.</div>';
        }
      });
    },

    'admin-index'() {
      const orders = allOrders();
      const revenue = orders.filter((o) => o.status !== 'cancelled').reduce((a, o) => a + o.total, 0);
      const stats = [
        ['Revenue', money(revenue), 'orders.html'],
        ['Orders', orders.length, 'orders.html'],
        ['Pending', orders.filter((o) => o.status === 'pending').length, 'orders.html?status=pending'],
        ['Live products', SR.products.length, 'products.html'],
        ['Customers', SR.sampleUsers.length, 'users.html'],
      ];
      $('#stats').innerHTML = stats.map(([label, value, href]) => `<a class="stat" href="${href}"><p class="label">${label}</p><p class="value">${value}</p></a>`).join('');
      $('#latest').innerHTML = `<div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Customer</th><th>Date</th><th>Status</th><th class="num">Total</th></tr></thead>
        <tbody>${orders.slice(0, 6).map((o) => `<tr>
          <td><a href="orders.html#order-${o.id}">#${o.id}</a></td>
          <td>${esc(o.fullname)}<br><small class="muted">${esc(o.email || '')}</small></td>
          <td>${fmtDate(o.date)}</td><td>${statusBadge(o.status)}</td><td class="num">${money(o.total)}</td></tr>`).join('')}</tbody></table></div>`;
      const unread = allMessages().filter((m) => !m.read);
      $('#unread').innerHTML = unread.length
        ? `<ul>${unread.slice(0, 5).map((m) => `<li><a href="messages.html#msg-${m.id}">${esc(m.subject)}</a> <span class="muted">from ${esc(m.name)}, ${fmtDate(m.date)}</span></li>`).join('')}</ul>`
        : '<p class="muted">Inbox zero. Nice.</p>';
    },

    'admin-products'() {
      $('#product-rows').innerHTML = SR.products.map((p) => `<tr>
        <td><img class="thumb-sm" src="${img(p.image)}" alt=""></td>
        <td><strong>${esc(p.name)}</strong> ${p.featured ? '<span class="badge">Featured</span>' : ''}</td>
        <td>${esc(SR.categories.find((c) => c.slug === p.cat).name)}</td>
        <td class="num">${money(p.price)}</td>
        <td>Live</td>
        <td><div class="row-actions">
          <a class="btn btn-outline btn-sm" href="product-form.html?id=${p.id}">Edit</a>
          <button class="btn btn-outline btn-sm" type="button" data-demo>Hide</button>
          <button class="btn btn-danger btn-sm" type="button" data-demo>Delete</button>
        </div></td></tr>`).join('');
      $$('[data-demo]').forEach((b) => b.addEventListener('click', () => showAlert('info', DEMO_NOTE)));
    },

    'admin-product-form'() {
      const p = SR.products.find((x) => x.id === Number(params.get('id')));
      $('#form-title').textContent = p ? 'Edit product' : 'Add product';
      document.title = (p ? 'Edit product' : 'Add product') + ' · SR Worldwide Admin';
      $('#category').innerHTML = '<option value="">Choose…</option>' + SR.categories.map((c) => `<option value="${c.slug}" ${p && p.cat === c.slug ? 'selected' : ''}>${esc(c.name)}</option>`).join('');
      if (p) {
        $('#name').value = p.name;
        $('#price').value = p.price.toFixed(2);
        $('#sizes').value = p.sizes.join(',');
        $('#description').value = p.description;
        $('#featured').checked = !!p.featured;
        $('#current-image').innerHTML = `<div class="current-image"><img src="${img(p.image)}" alt="Current main image"></div>`;
        if (p.back) $('#current-back').innerHTML = `<div class="current-image"><img src="${img(p.back)}" alt="Current second image"></div>`;
        $('#submit').textContent = 'Save changes';
      }
      $('#product-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const errors = [];
        if (!$('#name').value.trim()) errors.push('Name is required.');
        if (!$('#category').value) errors.push('Choose a category.');
        const price = parseFloat($('#price').value);
        if (!(price >= 0 && price <= 99999)) errors.push('Price must be a number between 0 and 99999.');
        if (!$('#sizes').value.trim()) errors.push('Enter at least one size.');
        if (!$('#description').value.trim()) errors.push('Description is required.');
        const file = $('#image').files[0];
        if (!p && !file) errors.push('A main image is required.');
        [file, $('#image_back').files[0]].forEach((f) => {
          if (f && !['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) errors.push('Only JPG, PNG or WebP images are allowed.');
          if (f && f.size > 2 * 1024 * 1024) errors.push('Image is too large (max 2 MB).');
        });
        if (errors.length) {
          $('#form-errors').innerHTML = `<div class="alert alert-error" role="alert"><ul>${errors.map((x) => `<li>${esc(x)}</li>`).join('')}</ul></div>`;
          return;
        }
        flash('info', 'Looks good! ' + DEMO_NOTE);
        location.href = 'products.html';
      });
    },

    'admin-orders'() {
      const filter = SR.statuses.includes(params.get('status')) ? params.get('status') : '';
      $('#filters').innerHTML = `<a class="chip" href="orders.html" ${filter ? '' : 'aria-current="page"'}>All</a>` +
        SR.statuses.map((s) => `<a class="chip" href="orders.html?status=${s}" ${filter === s ? 'aria-current="page"' : ''}>${s[0].toUpperCase() + s.slice(1)}</a>`).join('');
      const orders = allOrders().filter((o) => !filter || o.status === filter);
      if (!orders.length) { $('#orders').innerHTML = `<p class="empty-state">No orders with status “${esc(filter)}”.</p>`; return; }
      $('#orders').innerHTML = `<div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Date</th><th>Customer</th><th>Delivery</th><th>Items</th><th class="num">Total</th><th>Status</th></tr></thead>
        <tbody>${orders.map((o) => `<tr id="order-${o.id}">
          <td>#${o.id}${o.local ? '<br><span class="badge">yours</span>' : ''}</td>
          <td>${fmtDate(o.date)}</td>
          <td>${esc(o.fullname)} <small class="muted">(${esc(o.nickname)})</small><br><small class="muted">${esc(o.email || '')}</small></td>
          <td><small>${esc(o.street)}<br>${esc(o.postcode)} ${esc(o.city)}, ${esc(o.state)}</small></td>
          <td><details><summary>${o.items.reduce((a, i) => a + i.qty, 0)} item(s)</summary><ul>
            ${o.items.map((i) => `<li>${i.qty} × ${esc(i.name)} (${esc(i.size)}) @ ${money(i.price)}</li>`).join('')}</ul></details></td>
          <td class="num">${money(o.total)}</td>
          <td><form class="status-form" data-id="${o.id}">
            <label class="visually-hidden" for="status-${o.id}">Status for order ${o.id}</label>
            <select id="status-${o.id}">${SR.statuses.map((s) => `<option value="${s}" ${o.status === s ? 'selected' : ''}>${s[0].toUpperCase() + s.slice(1)}</option>`).join('')}</select>
            <button class="btn btn-sm" type="submit">Save</button>
          </form></td></tr>`).join('')}</tbody></table></div>`;
      $$('.status-form').forEach((form) => form.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = form.dataset.id;
        const status = $('select', form).value;
        const map = store.get('order_status', {}); map[id] = status; store.set('order_status', map);
        // Keep the customer's own "My orders" page in sync for orders made in this browser.
        const local = store.get('orders', []);
        local.forEach((o) => { if (String(o.id) === id) o.status = status; });
        store.set('orders', local);
        flash('success', `Order #${id} marked as ${status}.`);
        location.reload();
      }));
    },

    'admin-users'() {
      const orders = allOrders();
      $('#user-count').textContent = SR.sampleUsers.length + ' registered (sample data)';
      $('#user-rows').innerHTML = SR.sampleUsers.map((u) => {
        const mine = orders.filter((o) => o.email === u.email);
        const spent = mine.filter((o) => o.status !== 'cancelled').reduce((a, o) => a + o.total, 0);
        return `<tr><td>${u.id}</td><td>${esc(u.email)}</td><td>${fmtDate(u.birthday)}</td><td>${fmtDate(u.joined)}</td>
          <td class="num">${mine.length}</td><td class="num">${money(spent)}</td></tr>`;
      }).join('');
    },

    'admin-messages'() {
      const messages = allMessages();
      if (!messages.length) { $('#messages').innerHTML = '<p class="empty-state">No messages yet.</p>'; return; }
      $('#messages').innerHTML = `<div class="table-wrap"><table>
        <thead><tr><th>From</th><th>Message</th><th>Received</th><th>Actions</th></tr></thead>
        <tbody>${messages.map((m) => `<tr id="msg-${m.id}" class="${m.read ? 'row-muted' : 'unread'}">
          <td><strong>${esc(m.name)}</strong><br><a href="mailto:${esc(m.email)}">${esc(m.email)}</a></td>
          <td style="min-width:260px"><strong>${esc(m.subject)}</strong><p class="message-body">${esc(m.message)}</p></td>
          <td>${fmtDate(m.date, true)}</td>
          <td><div class="row-actions">
            <button class="btn btn-outline btn-sm" type="button" data-read="${m.id}" data-value="${m.read ? 0 : 1}">${m.read ? 'Mark unread' : 'Mark read'}</button>
            <button class="btn btn-danger btn-sm" type="button" data-delete="${m.id}">Delete</button>
          </div></td></tr>`).join('')}</tbody></table></div>`;
      $$('[data-read]').forEach((b) => b.addEventListener('click', () => {
        const map = store.get('msg_read', {}); map[b.dataset.read] = b.dataset.value === '1'; store.set('msg_read', map);
        location.reload();
      }));
      $$('[data-delete]').forEach((b) => b.addEventListener('click', () => {
        if (!confirm('Delete this message?')) return;
        store.set('msg_deleted', [...store.get('msg_deleted', []), Number(b.dataset.delete)]);
        flash('success', 'Message deleted.');
        location.reload();
      }));
    },
  };

  // Admin pages (except login) need the demo admin login.
  if (PAGE !== 'admin-login' && !store.get('admin', false)) {
    location.replace('login.html');
    return;
  }
  if ($('#admin-bar')) renderBar();
  showFlash();
  pages[PAGE]?.();
})();
