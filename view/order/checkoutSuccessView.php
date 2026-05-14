<!-- Mohamed -->

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="form-wrapper" style="text-align: center; padding: 3em 1em;">
  <!-- Erfolgsnachricht nach abgeschlossener Bestellung -->
  <h1>🎉 Bestellung erfolgreich</h1>
  <p>Vielen Dank für deine Bestellung bei <strong>SportX</strong>!</p>
  <p>Wir haben deine Bestellung erhalten und bearbeiten sie so schnell wie möglich.</p>

  <?php if (!empty($discountPercent)): ?>
    <!-- Falls ein Rabatt angewendet wurde, zeige Hinweis -->
    <p style="color: green;">Du hast einen Treue-Rabatt von <?= (int)$discountPercent ?>% erhalten!</p>
  <?php endif; ?>

  <br>
  <!-- Button zur Startseite -->
  <a href="index.php"><button class="btn-zurueck-startseite">Zur Startseite</button></a>
</main>

<!-- Scroll-to-Top Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>