<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?page=auth&action=login&redirect=cart");
  exit;
}

include 'view/layout/header.php';
require_once 'model/cartModel.php';
$cartItems = getCartItems();
$total = 0;
?>

<main class="cart-wrapper">
  <!-- 🛒 Linke Seite – Artikeltabelle -->
  <section class="cart-main">
    <h2>Warenkorb</h2>
    <div class="button-row">
      <a href="index.php">
        <button class="btn-zurueck-startseite">Zurück zur Startseite</button>
        <button class="btn-delete-all" onclick="clearList()">🧹 Alle löschen</button>

      </a>
    </div>

    <table class="cart-table">
      <thead>
        <tr>
          <th>Produkt</th>
          <th>Größe</th>
          <th>Anzahl</th>
          <th>Stückpreis</th>
          <th>Summe</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($cartItems)): ?>
          <tr>
            <td colspan="6">Dein Warenkorb ist leer.</td>
          </tr>
          <?php else:
          foreach ($cartItems as $item):
            $sum = $item['quantity'] * $item['price'];
            $total += $sum;
          ?>
            <tr>
              <td>
                <img src="<?= htmlspecialchars($item['image']) ?>" width="60" />
                <?= htmlspecialchars($item['name']) ?>
              </td>
              <td><?= htmlspecialchars($item['size']) ?></td>
              <td>
                <form action="index.php?page=cart&action=update" method="post">
                  <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                  <input type="hidden" name="size" value="<?= htmlspecialchars($item['size']) ?>">
                  <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" />
                  <button type="submit">✔</button>
                </form>
              </td>
              <td><?= number_format($item['price'], 2, ',', '.') ?> €</td>
              <td><?= number_format($sum, 2, ',', '.') ?> €</td>
              <td>
                <form action="index.php?page=cart&action=remove" method="post" style="display:inline">
                  <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                  <input type="hidden" name="size" value="<?= htmlspecialchars($item['size']) ?>">
                  <button type="submit" class="remove-btn">❌</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <!-- ✅ Rechte Seite – Zusammenfassung -->
  <aside class="cart-summary">
    <h3>Zusammenfassung</h3>
    <?php $netto = $total / 1.19;
    $mwst = $total - $netto; ?>
    <p>Zwischensumme: <span><?= number_format($total, 2, ',', '.') ?> €</span></p>
    <p>Versandkosten: <span>0,00 €</span></p>
    <hr>
    <p>Gesamtnettosumme: <span><?= number_format($netto, 2, ',', '.') ?> €</span></p>
    <p>zzgl. 19% MwSt.: <span><?= number_format($mwst, 2, ',', '.') ?> €</span></p>
    <p><strong>Gesamtsumme: <span><?= number_format($total, 2, ',', '.') ?> €</span></strong></p>

    <input type="text" placeholder="Gutscheincode eingeben (optional)" class="gutschein-input" />
    <a href="index.php?page=order&action=checkout">
      <button class="btn-checkout">WEITER ZUR KASSE</button>
    </a>
    <button class="btn-amazon">Bezahlen mit Amazon</button>
    <button class="btn-paypal">Direkt zu PayPal</button>
  </aside>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/warenkorb.js"></script>
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<?php include 'view/layout/footer.php'; ?>