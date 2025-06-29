<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="form-wrapper" style="padding: 2em; max-width: 10000px; margin: 20px;">
  <?php
  $statusTitleMap = [
    'neu' => 'Neue Aufträge',
    'in_bearbeitung' => 'Aufträge in Bearbeitung',
    'abgelehnt' => 'Abgelehnte Aufträge',
    'abgeschlossen' => 'Abgeschlossene Aufträge',
    'storniert' => 'Stornierte Aufträge'
  ];
  $statusTitle = $statusTitleMap[$statusFilter] ?? '';
  ?>
  <h1 style="text-align:center;">📦 <?= htmlspecialchars($statusTitle) ?></h1>

  <nav class="order-nav" style="text-align:center;margin-bottom:1em;">
    <a href="index.php?page=order&action=admin&status=neu" class="<?= $statusFilter === 'neu' ? 'active' : '' ?>">Neu</a> |
    <a href="index.php?page=order&action=admin&status=in_bearbeitung" class="<?= $statusFilter === 'in_bearbeitung' ? 'active' : '' ?>">In Bearbeitung</a> |
    <a href="index.php?page=order&action=admin&status=abgelehnt" class="<?= $statusFilter === 'abgelehnt' ? 'active' : '' ?>">Abgelehnt</a> |
    <a href="index.php?page=order&action=admin&status=abgeschlossen" class="<?= $statusFilter === 'abgeschlossen' ? 'active' : '' ?>">Abgeschlossen</a>
  </nav>

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

  <?php if (empty($orders)): ?>
    <p style="text-align:center;">Keine Bestellungen vorhanden.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="cart-table" style="margin:auto;">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Datum</th>
          <th>Status</th>
          <th>Details</th>
          <th>Aktion</th>
        </tr>
      </thead>

      <?php foreach ($orders as $order): ?>
        <tr>
          <td>#<?= (int)$order['id'] ?></td>
          <td><?= (int)$order['user_id'] ?></td>
          <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
          <td><?= htmlspecialchars($order['status']) ?></td>
          <td>
            <?php $items = json_decode($order['admin_comment'], true); ?>
            <?php if (is_array($items)): ?>
              <?php foreach ($items as $item): ?>
                <div><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>x Größe <?= $item['size'] ?>)</div>
              <?php endforeach; ?>
            <?php endif; ?>
          </td>
          <td>
            <form action="index.php?page=order&action=updateStatus" method="post" style="display:flex;gap:0.5em;align-items:center;">
              <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
              <input type="hidden" name="redirect" value="<?= htmlspecialchars($statusFilter) ?>">
              <select name="status">
                <option value="neu" <?= $order['status'] === 'neu' ? 'selected' : '' ?>>neu</option>
                <option value="in_bearbeitung" <?= $order['status'] === 'in_bearbeitung' ? 'selected' : '' ?>>in Bearbeitung</option>
                <option value="abgelehnt" <?= $order['status'] === 'abgelehnt' ? 'selected' : '' ?>>abgelehnt</option>
                <option value="abgeschlossen" <?= $order['status'] === 'abgeschlossen' ? 'selected' : '' ?>>abgeschlossen</option>
                <option value="storniert" <?= $order['status'] === 'storniert' ? 'selected' : '' ?>>storniert</option>
              </select>
              <input type="text" name="reason" placeholder="Grund" value="<?= htmlspecialchars($order['rejection_reason'] ?? '') ?>" style="max-width:150px;">
            <button type="submit" class="btn-checkout">Speichern</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>

      </table>
    </div>
  <?php endif; ?>
</section>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>