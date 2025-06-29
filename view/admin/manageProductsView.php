<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="form-wrapper" style="padding:2em; max-width:10000px; margin: 20px;">

  <h1 style="text-align:center;">🛍️ Produkte verwalten</h1>
  <?php if (empty($allProducts)): ?>
    <p style="text-align:center;">Keine Produkte gefunden.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="cart-table">
      <thead>
        <tr>
          <th>Id-#</th>
          <th>Name</th>
          <th>Preis</th>
          <th>Rabatt (%)</th>
          <th>Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allProducts as $prod): ?>
          <tr>
            <td><?= (int)$prod['id'] ?></td>
            <td><?= htmlspecialchars($prod['name']) ?></td>
            <td><?= number_format((float)$prod['price'], 2, ',', '.') ?> €</td>
            <td><?= (int)($prod['discount'] ?? 0) ?></td>
            <td>
              <?php if (($prod['discount'] ?? 0) > 0): ?>
                <form action="index.php?page=admin&action=updateDiscount" method="post" style="display:flex; gap:0.5em;">
                  <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                  <input type="hidden" name="discount" value="0">
                  <button type="submit" class="btn-checkout">Rabatt Entfernen</button>
                </form>
              <?php else: ?>
                <form action="index.php?page=admin&action=updateDiscount" method="post" style="display:flex; gap:0.5em;">
                  <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                  <input type="number" name="discount" value="0" min="0" max="90" style="width:70px;">
                  <button type="submit" class="btn-checkout">Rabatt Speichern</button>
                </form>
              <?php endif; ?>


              <form action="index.php?page=admin&action=deleteProduct" method="post" onsubmit="return confirm('Produkt wirklich löschen?');" style="margin-top:0.5em;">
                <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                <button type="submit" class="btn-delete-all"> Produkt Löschen</button>
              </form>

            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      </table>
    </div>
  <?php endif; ?>
  <a href="index.php?page=admin&action=dashboard"><button class="btn-zurueck-startseite" type="button">Zurück</button></a>
</section>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>
