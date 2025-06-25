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
      <?php $summe = 0; ?>
      <?php foreach ($cartItems as $item): ?>
        <?php $gesamt = $item['price'] * $item['quantity']; $summe += $gesamt; ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= htmlspecialchars($item['size']) ?></td>
          <td><?= (int)$item['quantity'] ?></td>
          <td><?= number_format($item['price'], 2, ',', '.') ?> €</td>
          <td><?= number_format($gesamt, 2, ',', '.') ?> €</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php
    $rabattBetrag = 0;
    if (!empty($discountPercent)) {
      $rabattBetrag = $summe * ($discountPercent / 100);
    }
    $endbetrag = $summe - $rabattBetrag;
  ?>

  <h3 style="margin-top: 1em;">🧾 Gesamtbetrag: <?= number_format($summe, 2, ',', '.') ?> €</h3>
  <?php if (!empty($discountPercent)): ?>
    <p style="color: green;">🎉 Rabatt (<?= (int)$discountPercent ?>%): -<?= number_format($rabattBetrag, 2, ',', '.') ?> €</p>
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

