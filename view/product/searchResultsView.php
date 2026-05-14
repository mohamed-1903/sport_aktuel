<!-- Hussein -->

<?php 
include __DIR__ . '/../layout/header.php'; 
?>

<main class="main-content">
  <!-- Suchbegriff als Überschrift anzeigen -->
  <h1>Suchergebnisse für "<?= htmlspecialchars($searchQuery) ?>"</h1>

  <!-- Container für Produkt-Ergebnisse im Grid -->
  <ul class="einzelprodukt-grid" id="produktContainer">
    <?php if (empty($searchResults)): ?>
      <!-- Kein Treffer gefunden -->
      <p>Keine Produkte gefunden.</p>
    <?php else: ?>
      <!-- Produktliste -->
      <?php foreach ($searchResults as $produkt): ?>
        <?php
          // Preis als float bereinigen (nur Zahlen und Punkt)
          $preis = floatval(preg_replace('/[^0-9.]/', '', $produkt["price"]));

          // Mannschaftszuordnung per Name-Inhalt
          $mannschaft = stripos($produkt["name"], "Bayern") !== false ? "Bayern" :
                        (stripos($produkt["name"], "Dortmund") !== false ? "Dortmund" : "");
        ?>

        <!-- Einzelnes Produkt als Listenelement -->
        <li class="einzelprodukt"
          data-marke="<?= htmlspecialchars($produkt['marke'] ?? '') ?>"
          data-farbe="<?= htmlspecialchars($produkt['farbe'] ?? '') ?>"
          data-preis="<?= $preis ?>"
          data-mannschaft="<?= $mannschaft ?>"
          data-geschlecht="<?= htmlspecialchars($produkt['geschlecht'] ?? '') ?>">

          <section class="produkt-wrapper">
            <!-- Produktbild -->
            <section class="image-wrapper">
              <img src="<?= htmlspecialchars($produkt['image_main'] ?? '') ?>" alt="<?= htmlspecialchars($produkt['name']) ?>">
            </section>

            <!-- Produktinfos: Name und Preis -->
            <section class="produkt-info">
              <h3><?= htmlspecialchars($produkt['name']) ?></h3>

              <?php
                // Rabattlogik: berechne reduzierten Preis, falls Rabatt vorhanden
                $d = $produkt['discount'] ?? 0;
                $base = (float)$produkt['price'];
                $salePrice = $d > 0 ? $base * (1 - $d / 100) : $base;
              ?>

              <?php if ($d > 0): ?>
                <!-- Preis mit Rabatt -->
                <p>
                  <del class="old-price"><?= number_format($base, 2, ',', '.') ?>€ inkl. Mwst.</del>
                  <span><?= number_format($salePrice, 2, ',', '.') ?>€ inkl. Mwst.</span>
                  <span class="rabatt">-<?= $d ?>%</span>
                </p>
              <?php else: ?>
                <!-- Regulärer Preis -->
                <p><?= htmlspecialchars($produkt['price']) ?>€ <span>inkl. Mwst.</span></p>
              <?php endif; ?>
            </section>
          </section>

          <!-- Aktionsbuttons: Detail, Warenkorb, Merkliste -->
          <section class="button-row" data-iid="<?= (int)$produkt['iid'] ?>">
            <!-- Produkt-Detailseite -->
            <a href="index.php?page=product&action=detail&id=<?= (int)$produkt['iid'] ?>">
              <button>Details</button>
            </a>

            <!-- Button: Zum Warenkorb hinzufügen -->
            <button class="btn-add-to-cart"
              data-iid="<?= (int)$produkt['iid'] ?>"
              data-name="<?= htmlspecialchars($produkt['name']) ?>"
              data-price="<?= $salePrice ?>"
              data-image="<?= htmlspecialchars($produkt['image_main'] ?? '') ?>">
              🛒
            </button>

            <!-- Button: Zur Merkliste hinzufügen -->
            <button class="btn-add-to-watch"
              data-iid="<?= (int)$produkt['iid'] ?>"
              data-name="<?= htmlspecialchars($produkt['name']) ?>"
              data-price="<?= $salePrice ?>"
              data-image="<?= htmlspecialchars($produkt['image_main'] ?? '') ?>">
              🤍
            </button>
          </section>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
</main>

<!-- Button zum Hochscrollen -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<!-- JavaScript für Produktinteraktion und UI -->
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<script src="js/watchlist.js"></script>
<script src="js/warenkorb.js"></script>

<?php 
include __DIR__ . '/../layout/footer.php'; 
?>