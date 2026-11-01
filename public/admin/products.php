<?php
require __DIR__ . '/../../includes/bootstrap.php';
require APP_ROOT . '/includes/upload.php';

$admin = require_admin();

if (is_post()) {
    csrf_check();
    $id = (int) post('id');
    $product = db_query('SELECT id, name, image, image_back FROM products WHERE id = ?', [$id])->fetch();

    if (!$product) {
        flash('error', 'Product not found.');
    } elseif (post('action') === 'delete') {
        // Past orders keep the product name and price (order_items.product_id becomes NULL).
        db_query('DELETE FROM products WHERE id = ?', [$id]);
        delete_uploaded_image($product['image']);
        delete_uploaded_image($product['image_back']);
        flash('success', '“' . $product['name'] . '” deleted.');
    } elseif (post('action') === 'toggle') {
        db_query('UPDATE products SET is_active = 1 - is_active WHERE id = ?', [$id]);
        flash('success', '“' . $product['name'] . '” updated.');
    }
    redirect('admin/products.php');
}

$products = db_query(
    'SELECT p.id, p.name, p.slug, p.price, p.image, p.is_active, p.is_featured, c.name AS category
     FROM products p JOIN categories c ON c.id = p.category_id
     ORDER BY c.sort_order, p.name'
)->fetchAll();

$pageTitle = 'Products';
require APP_ROOT . '/includes/admin-header.php';
?>

<div class="admin-head">
  <h1>Products</h1>
  <a class="btn btn-sm" href="<?= e(url('admin/product-form.php')) ?>">+ Add product</a>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th></th><th>Name</th><th>Category</th><th class="num">Price</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr class="<?= $p['is_active'] ? '' : 'row-muted' ?>">
          <td><img class="thumb-sm" src="<?= e(asset($p['image'])) ?>" alt=""></td>
          <td>
            <strong><?= e($p['name']) ?></strong>
            <?php if ($p['is_featured']): ?><span class="badge">Featured</span><?php endif; ?>
          </td>
          <td><?= e($p['category']) ?></td>
          <td class="num"><?= e(money($p['price'])) ?></td>
          <td><?= $p['is_active'] ? 'Live' : 'Hidden' ?></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-outline btn-sm" href="<?= e(url('admin/product-form.php?id=' . $p['id'])) ?>">Edit</a>
              <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <input type="hidden" name="action" value="toggle">
                <button class="btn btn-outline btn-sm" type="submit"><?= $p['is_active'] ? 'Hide' : 'Show' ?></button>
              </form>
              <form method="post" data-confirm="Delete “<?= e($p['name']) ?>”? This can't be undone.">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require APP_ROOT . '/includes/admin-footer.php'; ?>
