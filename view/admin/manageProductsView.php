<!-- Mohamed -->
<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Container für die Produktverwaltung -->
<section class="form-wrapper" style="padding:2em; max-width:10000px; margin: 20px;">
  <h1 style="text-align:center;">🛍️ Produkte verwalten</h1>

  <!-- Falls keine Produkte vorhanden sind -->
  <?php if (empty($allProducts)): ?>
    <p style="text-align:center;">Keine Produkte gefunden.</p>
  <?php else: ?>
    <div class="table-responsive">
      <!-- Tabelle mit Produktinformationen -->
      <table class="cart-table">
        <thead>
          <tr>
            <th>Id-#</th> <!-- Produkt-ID -->
            <th>Name</th> <!-- Produktname -->
            <th>Preis</th> <!-- Produktpreis -->
            <th>Rabatt (%)</th> <!-- Aktueller Rabatt -->
            <th>Aktionen</th> <!-- Admin-Funktionen -->
          </tr>
        </thead>
        <tbody>
          <!-- Durchlauf aller Produkte -->
          <?php foreach ($allProducts as $prod): ?>
            <tr>
              <td><?= (int)$prod['id'] ?></td>
              <td><?= htmlspecialchars($prod['name']) ?></td>
              <td><?= number_format((float)$prod['price'], 2, ',', '.') ?> €</td>
              <td><?= (int)($prod['discount'] ?? 0) ?></td>
              <td>
                <!-- Rabatt entfernen oder setzen -->
                <?php if (($prod['discount'] ?? 0) > 0): ?>
                  <!-- Rabatt entfernen -->
                  <form action="index.php?page=admin&action=updateDiscount" method="post" style="display:flex; gap:0.5em;">
                    <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                    <input type="hidden" name="discount" value="0">
                    <button type="submit" class="btn-checkout">Rabatt Entfernen</button>
                  </form>
                <?php else: ?>
                  <!-- Rabatt setzen -->
                  <form action="index.php?page=admin&action=updateDiscount" method="post" style="display:flex; gap:0.5em;">
                    <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                    <input type="number" name="discount" value="0" min="0" max="90" style="width:70px;">
                    <button type="submit" class="btn-checkout">Rabatt Speichern</button>
                  </form>
                <?php endif; ?>

                <!-- Produkt löschen mit Bestätigung -->
                <form action="index.php?page=admin&action=deleteProduct" method="post" onsubmit="return confirm('Produkt wirklich löschen?');" style="margin-top:0.5em;">
                  <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                  <button type="submit" class="btn-delete-all">Produkt Löschen</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- Zurück-Button -->
  <a href="index.php?page=admin&action=dashboard">
    <button class="btn-zurueck-startseite" type="button">Zurück</button>
  </a>
</section>

<!-- Scroll-to-top Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>
