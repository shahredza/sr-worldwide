<?php
require __DIR__ . '/../includes/bootstrap.php';

$user = require_user();
$lines = cart_lines();

if (!$lines) {
    flash('info', 'Your cart is empty.');
    redirect('cart.php');
}

$total = cart_total($lines);
$fields = ['fullname' => '', 'nickname' => '', 'street' => '', 'city' => '', 'state' => '', 'postcode' => ''];
$errors = [];

if (is_post()) {
    csrf_check();
    foreach ($fields as $name => $_) {
        $fields[$name] = post($name);
    }

    if ($fields['fullname'] === '' || mb_strlen($fields['fullname']) > 100) {
        $errors[] = 'Please enter your full name (max 100 characters).';
    }
    if ($fields['nickname'] === '' || mb_strlen($fields['nickname']) > 50) {
        $errors[] = 'Please enter a nickname (max 50 characters).';
    }
    if ($fields['street'] === '' || mb_strlen($fields['street']) > 255) {
        $errors[] = 'Please enter your street address.';
    }
    if ($fields['city'] === '' || mb_strlen($fields['city']) > 100) {
        $errors[] = 'Please enter your city.';
    }
    if (!in_array($fields['state'], MY_STATES, true)) {
        $errors[] = 'Please choose a state.';
    }
    if (!preg_match('/^\d{5}$/', $fields['postcode'])) {
        $errors[] = 'Postcode must be 5 digits.';
    }

    if (!$errors) {
        $pdo = db();
        try {
            $pdo->beginTransaction();

            db_query(
                'INSERT INTO orders (user_id, fullname, nickname, street, city, state, postcode, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$user['id'], $fields['fullname'], $fields['nickname'], $fields['street'],
                 $fields['city'], $fields['state'], $fields['postcode'], $total]
            );
            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, size, unit_price, quantity)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            foreach ($lines as $line) {
                $p = $line['product'];
                $itemStmt->execute([$orderId, $p['id'], $p['name'], $line['size'], $p['price'], $line['qty']]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        cart_clear();
        flash('success', 'Thank you! Order #' . $orderId . ' has been placed.');
        redirect('account.php');
    }
}

$pageTitle = 'Checkout';
require __DIR__ . '/../includes/header.php';
?>

<section class="container page">
  <h1>Checkout</h1>

  <div class="checkout-layout">
    <form method="post" class="form-card" novalidate>
      <?= csrf_field() ?>
      <h2 style="font-size:1.4rem">Delivery details</h2>

      <?php if ($errors): ?>
        <div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="field">
          <label for="fullname">Full name</label>
          <input id="fullname" name="fullname" type="text" maxlength="100" autocomplete="name" required value="<?= e($fields['fullname']) ?>">
        </div>
        <div class="field">
          <label for="nickname">Nickname</label>
          <input id="nickname" name="nickname" type="text" maxlength="50" autocomplete="nickname" required value="<?= e($fields['nickname']) ?>">
        </div>
        <div class="field span-2">
          <label for="street">Street address</label>
          <input id="street" name="street" type="text" maxlength="255" autocomplete="street-address" required value="<?= e($fields['street']) ?>">
        </div>
        <div class="field">
          <label for="city">City</label>
          <input id="city" name="city" type="text" maxlength="100" autocomplete="address-level2" required value="<?= e($fields['city']) ?>">
        </div>
        <div class="field">
          <label for="state">State</label>
          <select id="state" name="state" autocomplete="address-level1" required>
            <option value="">Choose…</option>
            <?php foreach (MY_STATES as $s): ?>
              <option <?= $fields['state'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="postcode">Postcode</label>
          <input id="postcode" name="postcode" type="text" inputmode="numeric" pattern="\d{5}" maxlength="5" autocomplete="postal-code" required value="<?= e($fields['postcode']) ?>">
        </div>
      </div>

      <p class="muted" style="font-size:.9rem">This is a demo shop: no payment is taken and nothing is shipped.</p>
      <button class="btn btn-block" type="submit">Place order · <?= e(money($total)) ?></button>
    </form>

    <aside class="summary" aria-label="Order summary">
      <h2 style="font-size:1.4rem">Your order</h2>
      <?php foreach ($lines as $line): ?>
        <div class="summary-row">
          <span><?= (int) $line['qty'] ?> × <?= e($line['product']['name']) ?> (<?= e($line['size']) ?>)</span>
          <span><?= e(money($line['subtotal'])) ?></span>
        </div>
      <?php endforeach; ?>
      <div class="summary-row"><span>Shipping</span><span>Free</span></div>
      <div class="summary-row summary-total"><span>Total</span><span><?= e(money($total)) ?></span></div>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
