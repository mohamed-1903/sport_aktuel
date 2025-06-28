<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gutschein'])) {
  $code = strtoupper(trim($_POST['gutschein']));
  $validCodes = ['SPORT20' => 20, 'SP_20' => 20];

  if (array_key_exists($code, $validCodes)) {
    $_SESSION['discount_code'] = $code;
    $_SESSION['discount_percent'] = $validCodes[$code];
    $gutscheinMessage = "✔ Gutscheincode '$code' angewendet ({$validCodes[$code]}%).";
  } else {
    unset($_SESSION['discount_code'], $_SESSION['discount_percent']);
    $gutscheinMessage = "❌ Ungültiger Gutscheincode.";
  }
}
?>


<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="form-wrapper" style="text-align: center; padding: 2em 1em;">
  <h1>🛒 Bestellung überprüfen</h1>

  <?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <table class="cart-table" style="margin: auto;">
    <thead>
      <tr>
        <th>Produkt</th>
        <th>Größe</th>
        <th>Menge</th>
        <th>Preis</th>
        <th>Gesamt</th>
      </tr>
    </thead>
    <tbody>
      <?php $summe = 0; $rabattBetrag = 0; $discountPercent = $_SESSION['discount_percent'] ?? null; ?>
      <?php foreach ($cartItems as $item): ?>
        <?php
          $base = $item['price'];
          $itemDiscount = (int)($item['discount'] ?? 0);
          $einzelpreis = $base * (1 - $itemDiscount / 100);
          $einzelpreis += (!empty($item['gift']) ? 2 : 0) + ($item['custom_fee'] ?? 0);
          $gesamt = $einzelpreis * $item['quantity'];
          $couponRabatt = 0;
          if ($discountPercent && $itemDiscount === 0) {
            $couponRabatt = $gesamt * ($discountPercent / 100);
          }
          $summe += $gesamt;
          $rabattBetrag += $couponRabatt;
        ?>
        <tr>
          <td>
            <?= htmlspecialchars($item['name']) ?><br>
            <small>Größe: <?= htmlspecialchars($item['size']) ?></small><br>
            <?php if (!empty($item['custom_name']) || !empty($item['custom_number'])): ?>
              <small>Personalisierung: <?= htmlspecialchars($item['custom_name']) ?> <?= htmlspecialchars($item['custom_number']) ?></small><br>
            <?php endif; ?>
            <?php if (!empty($item['gift'])): ?>
              <small>🎁 Geschenkverpackung</small><br>
            <?php endif; ?>
            <?php if ($itemDiscount > 0): ?>
              <small>
                🎟️ Rabatt<?= !empty($item['discount_code']) ? ' (' . htmlspecialchars($item['discount_code']) . ')' : '' ?>:
                <?= $itemDiscount ?>%
              </small>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($item['size']) ?></td>
          <td><?= (int)$item['quantity'] ?></td>
          <td><?= number_format($einzelpreis, 2, ',', '.') ?> €</td>
          <td><?= number_format($gesamt - $couponRabatt, 2, ',', '.') ?> €</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php
    $endbetrag = $summe - $rabattBetrag;
  ?>

  <form method="post">
    <input type="text" name="gutschein" placeholder="Gutscheincode eingeben" />
    <button type="submit">Einlösen</button>
  </form>

  <?php if (isset($gutscheinMessage)): ?>
    <p style="color: <?= strpos($gutscheinMessage, '✔') === 0 ? 'green' : 'red' ?>;">
      <?= $gutscheinMessage ?>
    </p>
  <?php endif; ?>


  <h3 style="margin-top: 1em;">🧾 Gesamtbetrag: <?= number_format($summe, 2, ',', '.') ?> €</h3>

  <?php if ($rabattBetrag > 0): ?>
    <p style="color: green;">🎉 Gutscheinrabatt (<?= (int)$discountPercent ?>%): -<?= number_format($rabattBetrag, 2, ',', '.') ?> €</p>
    <h3>Neuer Betrag: <?= number_format($endbetrag, 2, ',', '.') ?> €</h3>
  <?php endif; ?>


  <form action="index.php?page=order&action=submit" method="post" style="margin-top: 2em;">
    <button type="submit" class="btn-checkout">Jetzt bestellen</button>
  </form>

  <a href="index.php?page=cart&action=view">
    <button class="btn-zurueck-startseite">Zurück zum Warenkorb</button>
  </a>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>