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

include __DIR__ . '/../layout/header.php';
?>


<section class="main-content">
  <?php if (!empty($_SESSION['message'])): ?>
    <div class="toast-popup show" id="toastMessage">
      <?= htmlspecialchars($_SESSION['message']) ?>
      <button type="button" class="close-toast" onclick="this.parentElement.classList.remove('show')">&times;</button>
    </div>
    <script>
      const toast = document.getElementById('toastMessage');
      if (toast) {
        setTimeout(() => {
          toast.classList.remove('show');
        }, 3000);
      }
    </script>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>


  <h1>Meine Bestellungen</h1>

  <?php if (empty($orders)): ?>
    <p>Du hast noch keine Bestellungen.</p>
  <?php else: ?>
    <?php
    $labels = [
      'neu' => '🕓 Neu – noch stornierbar',
      'bestellt' => '📦 Bestellt',
      'versandt_nicht_erhalten' => '🚚 Versandt, nicht erhalten',
      'in_bearbeitung' => '🔧 In Bearbeitung',
      'abgeschlossen' => '✅ Abgeschlossen',
      'abgelehnt' => '❌ Abgelehnt',
      'storniert' => '🚫 Storniert',
    ];
    ?>

    <table class="cart-table">
      <thead>
        <tr>
          <th>Bestell-ID</th>
          <th>Datum</th>
          <th>Status</th>
          <th>Details</th>
          <th>Grund</th>
          <th>Aktion</th>
        </tr>
      </thead>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td>#<?= (int)$order['id'] ?></td>
          <td><?= date("d.m.Y H:i", strtotime($order['created_at'])) ?></td>
          <td>
            <?php $statusKey = $order['status']; ?>
            <?= htmlspecialchars($labels[$statusKey] ?? $statusKey) ?>
          </td>
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
          <td><?= htmlspecialchars($order['rejection_reason'] ?? '') ?></td>
          <td>
            <?php if ($order['status'] === 'neu'): ?>
              <form method="post" action="index.php?page=order&action=cancel&id=<?= $order['id'] ?>" onsubmit="return confirm('Wirklich stornieren?');">
                <button type="submit" class="btn-delete-all">Stornieren</button>
              </form>
            <?php else: ?>
              <em>Keine Aktion</em>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>
