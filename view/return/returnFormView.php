<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="form-wrapper" style="text-align: center; padding: 2em;">
  <h1>📦 Retoure beantragen</h1>
  <?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form action="index.php?page=return&action=submit" method="post" style="margin-top: 1em;">
    <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderId) ?>">
    <label for="reason">Grund für die Rückgabe:</label><br>
    <textarea id="reason" name="reason" rows="4" cols="50" required></textarea><br><br>
    <button type="submit" class="btn-checkout">Anfrage senden</button>
  </form>
  <a href="index.php?page=user&action=orders"><button class="btn-zurueck-startseite">Zurück zu meinen Bestellungen</button></a>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>

