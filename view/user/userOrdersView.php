<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?page=auth&action=login&redirect=user&action=orders");
  exit;
}

require_once 'model/orderModel.php';
$orders = getOrdersByUser($_SESSION['user_id']);

include 'view/layout/header.php';
?>

<main class="form-wrapper" style="padding: 2em;">
  <h1 style="text-align: center;">📦 Meine Bestellungen</h1>

  <?php if (empty($orders)): ?>
    <p style="text-align: center;">Du hast noch keine Bestellungen aufgegeben.</p>
  <?php else: ?>
    <table class="cart-table" style="margin: auto;">
      <thead>
        <tr>
          <th>#</th>
          <th>Datum</th>
          <th>Status</th>
          <th>Details</th>
          <th>Aktion</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td>#<?= (int)$order['id'] ?></td>
            <td><?= date("d.m.Y H:i", strtotime($order['created_at'])) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td>
              <?php $items = json_decode($order['admin_comment'], true); ?>
              <?php if (is_array($items)): ?>
                <?php foreach ($items as $item): ?>
  <div><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>x Größe <?= $item['size'] ?>)</div>
<?php endforeach; ?>

              <?php else: ?>
                <em>Keine Details verfügbar</em>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($order['status'] === 'abgeschlossen'): ?>
                <a href="index.php?page=return&action=form&order_id=<?= (int)$order['id'] ?>">Retoure</a>
              <?php elseif ($order['status'] === 'neu'): ?>
                <form action="index.php?page=order&action=cancel" method="post">
                  <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                  <button type="submit" class="btn-checkout">Stornieren</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<?php include 'view/layout/footer.php'; ?>
