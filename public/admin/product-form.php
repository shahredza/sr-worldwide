<?php
require __DIR__ . '/../../includes/bootstrap.php';
require APP_ROOT . '/includes/upload.php';

$admin = require_admin();

$id = (int) ($_GET['id'] ?? 0);
$existing = null;
if ($id) {
    $existing = db_query('SELECT * FROM products WHERE id = ?', [$id])->fetch();
    if (!$existing) {
        flash('error', 'Product not found.');
        redirect('admin/products.php');
    }
}

$form = [
    'name'        => $existing['name'] ?? '',
    'category_id' => (string) ($existing['category_id'] ?? ''),
    'price'       => $existing['price'] ?? '',
    'sizes'       => $existing['sizes'] ?? 'S,M,L,XL',
    'description' => $existing['description'] ?? '',
    'is_featured' => (bool) ($existing['is_featured'] ?? false),
    'is_active'   => (bool) ($existing['is_active'] ?? true),
];
$errors = [];

/** Turn a name into a URL slug that isn't used by another product. */
function unique_slug(string $name, int $ignoreId): string
{
    $base = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-') ?: 'product';
    $slug = $base;
    for ($n = 2; db_query('SELECT 1 FROM products WHERE slug = ? AND id <> ?', [$slug, $ignoreId])->fetchColumn(); $n++) {
        $slug = "$base-$n";
    }
    return $slug;
}

if (is_post()) {
    csrf_check();
    $form['name'] = post('name');
    $form['category_id'] = post('category_id');
    $form['price'] = post('price');
    $form['sizes'] = implode(',', product_sizes(post('sizes')));
    $form['description'] = post('description');
    $form['is_featured'] = isset($_POST['is_featured']);
    $form['is_active'] = isset($_POST['is_active']);

    if ($form['name'] === '' || mb_strlen($form['name']) > 150) {
        $errors[] = 'Name is required (max 150 characters).';
    }
    if (!in_array((int) $form['category_id'], array_column(categories(), 'id'), true)) {
        $errors[] = 'Choose a category.';
    }
    if (!is_numeric($form['price']) || $form['price'] < 0 || $form['price'] > 99999) {
        $errors[] = 'Price must be a number between 0 and 99999.';
    }
    if ($form['sizes'] === '' || mb_strlen($form['sizes']) > 100) {
        $errors[] = 'Enter at least one size, separated by commas (e.g. S,M,L or One Size).';
    }
    foreach (product_sizes($form['sizes']) as $s) {
        if (mb_strlen($s) > 20) {
            $errors[] = 'Each size must be 20 characters or less.';
            break;
        }
    }
    if ($form['description'] === '') {
        $errors[] = 'Description is required.';
    }

    // Only process uploads once the rest of the form is valid, so we don't leave orphan files.
    $front = $back = ['path' => null, 'error' => null];
    if (!$errors) {
        $front = handle_image_upload('image');
        $back = handle_image_upload('image_back');
        foreach ([$front, $back] as $u) {
            if ($u['error']) {
                $errors[] = $u['error'];
            }
        }
        if (!$existing && !$front['path'] && !$front['error']) {
            $errors[] = 'A main image is required.';
        }
        if ($errors) {
            delete_uploaded_image($front['path']);
            delete_uploaded_image($back['path']);
        }
    }

    if (!$errors) {
        $image = $front['path'] ?? $existing['image'];
        $imageBack = $back['path'] ?? $existing['image_back'] ?? null;
        if (!empty($_POST['remove_back']) && !$back['path']) {
            $imageBack = null;
        }

        $params = [
            (int) $form['category_id'], $form['name'], unique_slug($form['name'], $id), $form['description'],
            round((float) $form['price'], 2), $form['sizes'], $image, $imageBack,
            (int) $form['is_featured'], (int) $form['is_active'],
        ];

        if ($existing) {
            db_query(
                'UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, sizes = ?,
                 image = ?, image_back = ?, is_featured = ?, is_active = ? WHERE id = ?',
                [...$params, $id]
            );
            // Clean up replaced uploads.
            if ($image !== $existing['image']) {
                delete_uploaded_image($existing['image']);
            }
            if ($imageBack !== $existing['image_back']) {
                delete_uploaded_image($existing['image_back']);
            }
            flash('success', '“' . $form['name'] . '” saved.');
        } else {
            db_query(
                'INSERT INTO products (category_id, name, slug, description, price, sizes, image, image_back, is_featured, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $params
            );
            flash('success', '“' . $form['name'] . '” added.');
        }
        redirect('admin/products.php');
    }
}

$pageTitle = $existing ? 'Edit product' : 'Add product';
require APP_ROOT . '/includes/admin-header.php';
?>

<div class="admin-head">
  <h1><?= e($pageTitle) ?></h1>
  <a class="btn btn-outline btn-sm" href="<?= e(url('admin/products.php')) ?>">← All products</a>
</div>

<form method="post" enctype="multipart/form-data" class="form-card" style="max-width:760px" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="MAX_FILE_SIZE" value="<?= UPLOAD_MAX_BYTES ?>">

  <?php if ($errors): ?>
    <div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="form-grid">
    <div class="field span-2">
      <label for="name">Name</label>
      <input id="name" name="name" type="text" maxlength="150" required value="<?= e($form['name']) ?>">
    </div>
    <div class="field">
      <label for="category_id">Category</label>
      <select id="category_id" name="category_id" required>
        <option value="">Choose…</option>
        <?php foreach (categories() as $c): ?>
          <option value="<?= (int) $c['id'] ?>" <?= $form['category_id'] === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label for="price">Price (RM)</label>
      <input id="price" name="price" type="number" step="0.01" min="0" max="99999" required value="<?= e((string) $form['price']) ?>">
    </div>
    <div class="field span-2">
      <label for="sizes">Sizes</label>
      <input id="sizes" name="sizes" type="text" maxlength="100" required value="<?= e($form['sizes']) ?>">
      <p class="hint">Comma separated, e.g. <code>S,M,L,XL</code>, <code>28,30,32</code> or <code>One Size</code>.</p>
    </div>
    <div class="field span-2">
      <label for="description">Description</label>
      <textarea id="description" name="description" required><?= e($form['description']) ?></textarea>
    </div>

    <div class="field">
      <label for="image">Main image <?= $existing ? '(leave empty to keep)' : '' ?></label>
      <?php if ($existing): ?>
        <div class="current-image"><img src="<?= e(asset($existing['image'])) ?>" alt="Current main image"></div>
      <?php endif; ?>
      <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" <?= $existing ? '' : 'required' ?>>
      <p class="hint">JPG, PNG or WebP, max <?= UPLOAD_MAX_BYTES / 1024 / 1024 ?> MB.</p>
    </div>
    <div class="field">
      <label for="image_back">Second image (optional)</label>
      <?php if (!empty($existing['image_back'])): ?>
        <div class="current-image">
          <img src="<?= e(asset($existing['image_back'])) ?>" alt="Current second image">
          <label class="checkbox"><input type="checkbox" name="remove_back" value="1"> Remove</label>
        </div>
      <?php endif; ?>
      <input id="image_back" name="image_back" type="file" accept="image/jpeg,image/png,image/webp">
    </div>

    <div class="field span-2">
      <label class="checkbox"><input type="checkbox" name="is_active" value="1" <?= $form['is_active'] ? 'checked' : '' ?>> Live in the shop</label>
      <label class="checkbox"><input type="checkbox" name="is_featured" value="1" <?= $form['is_featured'] ? 'checked' : '' ?>> Featured on the home page</label>
    </div>
  </div>

  <button class="btn" type="submit"><?= $existing ? 'Save changes' : 'Add product' ?></button>
</form>

<?php require APP_ROOT . '/includes/admin-footer.php'; ?>
