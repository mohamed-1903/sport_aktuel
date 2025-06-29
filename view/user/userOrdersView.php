<!-- Laith -->

<?php
// Starte die Session, falls sie noch nicht läuft
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Falls der Nutzer nicht eingeloggt ist, leite zur Login-Seite weiter
// Mit redirect-Parametern, um nach Login zurück auf die Bestellseite zu kommen
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?page=auth&action=login&redirect=user&action=orders");
  exit;
}

// Binde das Order Model ein, um auf die Bestelldaten zuzugreifen
require_once 'model/orderModel.php';

// Hole alle Bestellungen des aktuell eingeloggten Users
$orders = getOrdersByUser($_SESSION['user_id']);

// Binde das Layout-Header-Template ein (HTML <head>, Navigation etc.)
include __DIR__ . '/../layout/header.php';
?>

<section class="main-content">
  <!-- Wenn es eine Session-Nachricht gibt, zeige diese als "Toast" Popup -->
  <?php if (!empty($_SESSION['message'])): ?>
    <div class="toast-popup show" id="toastMessage">
      <?= htmlspecialchars($_SESSION['message']) ?>
      <button type="button" class="close-toast" onclick="this.parentElement.classList.remove('show')">&times;</button>
    </div>
    <script>
      // JavaScript zum automatischen Ausblenden des Toasts nach 3 Sekunden
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

  <!-- Wenn keine Bestellungen vorhanden sind -->
  <?php if (empty($orders)): ?>
    <p>Du hast noch keine Bestellungen.</p>
  <?php else: ?>
    <?php
    // Statuslabels mit passenden Icons und Texten
    //Emojys von Chatgpt
    $labels = [
      'neu' => '🕓 Neu - noch stornierbar',
      'in_bearbeitung' => '🔧 In Bearbeitung',
      'abgeschlossen' => '✅ Abgeschlossen',
      'abgelehnt' => '❌ Abgelehnt',
      'storniert' => '🚫 Storniert',
    ];
    ?>

    <!-- Tabelle zur Anzeige der Bestellungen -->
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
      
      <!-- Durchlauf aller Bestellungen -->
      <?php foreach ($orders as $order): ?>
        <tr>
          <!-- Bestellnummer -->
          <td>#<?= (int)$order['id'] ?></td>

          <!-- Datum im deutschen Format -->
          <td><?= date("d.m.Y H:i", strtotime($order['created_at'])) ?></td>

          <!-- Statusanzeige anhand der Mapping-Tabelle -->
          <td>
            <?php $statusKey = $order['status']; ?>
            <?= htmlspecialchars($labels[$statusKey] ?? $statusKey) ?>
          </td>

          <!-- Artikelauflistung aus JSON-Kommentar (von Admin oder System generiert) -->
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

          <!-- Grund für Ablehnung, falls vorhanden -->
          <td><?= htmlspecialchars($order['rejection_reason'] ?? '') ?></td>

          <!-- Aktion: Nur wenn Status „neu“ ist, darf storniert werden -->
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

<!-- Button zum Zurückscrollen -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<!-- Footer einbinden -->
<?php include __DIR__ . '/../layout/footer.php'; ?>