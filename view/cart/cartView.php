<?php
// controller/cartController.php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$userId = $_SESSION['user_id'] ?? null;

require_once 'model/cartModel.php';
require_once 'model/productModel.php';

$action = $_GET['action'] ?? 'view';


if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?page=auth&action=login&redirect=cart");
  exit;
}

include __DIR__ . '/../layout/header.php';
$cartItems = getCartItems($_SESSION['user_id']);
$total = 0;
?>

<main class="cart-wrapper">
  <!-- 🛒 Linke Seite – Artikeltabelle -->
  <section class="cart-main">
    <h2>Warenkorb</h2>
    <div class="button-rows">
      <a href="index.php">
        <button class="btn-zurueck-startseite">Zurück zur Startseite</button>
        <button class="btn-delete-all" onclick="clearList()">🧹 Alle löschen</button>
      </a>
    </div>

    <table class="cart-table">
      <thead>
        <tr>
          <th>Produkt</th>
          <th>Anzahl</th>
          <th>Stückpreis</th>
          <th>Summe</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="cart-table-body">
        <?php if (empty($cartItems)): ?>
          <tr>
            <td colspan="6">Dein Warenkorb ist leer.</td>
          </tr>
          <?php else:
          foreach ($cartItems as $item):
            $base = $item['price'];
            $rabattPreis = $base * (1 - ($item['discount'] ?? 0) / 100);
            $einzelpreis = $rabattPreis + (($item['gift'] ?? 0) ? 2 : 0) + ($item['custom_fee'] ?? 0);
            $sum = $einzelpreis * $item['quantity'];
            $total += $sum;
          ?>
            <tr>
              <td>
                <img src="<?= htmlspecialchars($item['image_main']) ?>" width="60" />
                <?= htmlspecialchars($item['name']) ?><br>
                <small>Größe: <?= htmlspecialchars($item['size']) ?></small><br>
                <?php if (!empty($item['custom_name']) || !empty($item['custom_number'])): ?>
                  <small>Personalisierung: <?= htmlspecialchars($item['custom_name']) ?> <?= htmlspecialchars($item['custom_number']) ?></small><br>
                <?php endif; ?>
                <?php if (!empty($item['gift'])): ?>
                  <small>🎁 Geschenkverpackung</small><br>
                <?php endif; ?>
                <?php if (!empty($item['discount'])): ?>
                  <small>
                    🎟️ Rabatt<?= !empty($item['discount_code']) ? ' (' . htmlspecialchars($item['discount_code']) . ')' : '' ?>:
                    <?= (int)$item['discount'] ?>%
                  </small>
                <?php endif; ?>
              </td>
              <td>
                <form action="index.php?page=cart&action=update" method="post">
                  <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                  <input type="hidden" name="size" value="<?= htmlspecialchars($item['size']) ?>">
                  <input
                    type="number"
                    name="quantity"
                    class="qty-input"
                    data-id="<?= (int)$item['product_id'] ?>"
                    data-size="<?= htmlspecialchars($item['size']) ?>"
                    data-price="<?= number_format($einzelpreis, 2, '.', '') ?>"
                    value="<?= (int)$item['quantity'] ?>"
                    min="1" />
                </form>
              </td>

              <td><?= number_format($einzelpreis, 2, ',', '.') ?> €</td>
              <td><?= number_format($sum, 2, ',', '.') ?> €</td>
              <td>
                <form action="index.php?page=cart&action=remove" method="post" style="display:inline">
                  <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
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
    <p>Zwischensumme: <span id="zwischensumme"><?= number_format($total, 2, ',', '.') ?> €</span></p>
    <p>Versandkosten: <span>0,00 €</span></p>
    <hr>
    <p>Gesamtnettosumme: <span id="nettosumme"><?= number_format($netto, 2, ',', '.') ?> €</span></p>
    <p>zzgl. 19% MwSt.: <span id="mwstbetrag"><?= number_format($mwst, 2, ',', '.') ?> €</span></p>
    <p><strong>Gesamtsumme: <span id="gesamtsumme"><?= number_format($total, 2, ',', '.') ?> €</span></strong></p>
    <br> 
    <a href="index.php?page=order&action=checkout">
      <button class="btn-checkout">WEITER ZUR KASSE</button>
    </a>
  </aside>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>