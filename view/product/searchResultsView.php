<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="main-content">
  <h1>Suchergebnisse für "<?= htmlspecialchars($searchQuery) ?>"</h1>
  <ul class="einzelprodukt-grid" id="produktContainer">
    <?php if (empty($searchResults)): ?>
      <p>Keine Produkte gefunden.</p>
    <?php else: ?>
      <?php foreach ($searchResults as $produkt): ?>
        <?php
          $preis = floatval(preg_replace('/[^0-9.]/', '', $produkt["price"]));
          $mannschaft = stripos($produkt["name"], "Bayern") !== false ? "Bayern" : (stripos($produkt["name"], "Dortmund") !== false ? "Dortmund" : "");
        ?>
        <li class="einzelprodukt"
          data-marke="<?= htmlspecialchars($produkt['marke'] ?? '') ?>"
          data-farbe="<?= htmlspecialchars($produkt['farbe'] ?? '') ?>"
          data-preis="<?= $preis ?>"
          data-mannschaft="<?= $mannschaft ?>"
          data-geschlecht="<?= htmlspecialchars($produkt['geschlecht'] ?? '') ?>">
          <section class="produkt-wrapper">
            <section class="image-wrapper">
              <img src="<?= htmlspecialchars($produkt['image_main'] ?? '') ?>" alt="<?= htmlspecialchars($produkt['name']) ?>">
            </section>
            <section class="produkt-info">
              <h3><?= htmlspecialchars($produkt['name']) ?></h3>
              <?php $d = $produkt['discount'] ?? 0; $base = (float)$produkt['price']; $salePrice = $d > 0 ? $base * (1 - $d / 100) : $base; ?>
              <?php if ($d > 0): $neu = $salePrice; ?>
                <p>
                  <del class="old-price"><?= number_format($base, 2, ',', '.') ?>€</del>
                  <span><?= number_format($neu, 2, ',', '.') ?>€</span>
                  <span>inkl. Mwst.</span>
                  <span class="rabatt">-<?= $d ?>%</span>
                </p>
              <?php else: ?>
                <p><?= htmlspecialchars($produkt['price']) ?>€ <span>inkl. Mwst.</span></p>
              <?php endif; ?>
            </section>
          </section>
          <section class="button-row" data-iid="<?= (int)$produkt['iid'] ?>">
            <a href="index.php?page=product&action=detail&id=<?= (int)$produkt['iid'] ?>">
              <button>Details</button>
            </a>
            <button class="btn-add-to-cart"
              data-iid="<?= (int)$produkt['iid'] ?>"
              data-name="<?= htmlspecialchars($produkt['name']) ?>"
              data-price="<?= $salePrice ?>"
              data-image="<?= htmlspecialchars($produkt['image_main'] ?? '') ?>">
              🛒
            </button>
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
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<script src="js/watchlist.js"></script>
<script src="js/warenkorb.js"></script>
<?php include __DIR__ . '/../layout/footer.php'; ?>
