<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="main-container">
  <aside class="sidebar">
    <h2><?= htmlspecialchars($category) ?></h2>
    <ul>
      <?php
      $submap = [
        "Sportbekleidung" => ["Trikots", "Socken", "Handschuhe", "Trainingsanzüge", "Trainingsjacken", "Trainingshosen", "T-Shirts", "Poloshirts"],
        "Fußballschuhe" => ["Stollen", "Kunstrasen", "Hallenschuhe"],
        "Zubehör" => ["Schienbeinschoner", "Fußbälle", "Sporttaschen"],
        "Sale %" => ["Sportbekleidung", "Fußballschuhe", "Zubehör"]
      ];
      foreach ($submap[$category] ?? [] as $sub):
        $active = (strcasecmp($subcategory, $sub) === 0) ? 'style="font-weight:bold;"' : '';
        $link = "index.php?page=product&action=list&category=" . urlencode($category) . "&subcategory=" . urlencode($sub);
        echo "<li><a href='$link' $active>" . htmlspecialchars($sub) . "</a></li>";
      endforeach;
      ?>
    </ul>
  </aside>

  <main class="main-content">
    <h1><?= htmlspecialchars($displayTitle) ?></h1>

    <section class="filterbar">
      <select id="filter-marke" onchange="applyFilter()">
        <option value="">Alle Marken</option>
        <option value="Nike">Nike</option>
        <option value="Puma">Puma</option>
        <option value="Adidas">Adidas</option>
      </select>
      <select id="filter-farbe" onchange="applyFilter()">
        <option value="">Alle Farben</option>
        <option value="Schwarz">Schwarz</option>
        <option value="Weiß">Weiß</option>
        <option value="Blau">Blau</option>
        <option value="Rot">Rot</option>
      </select>
      <select id="filter-preis" onchange="applyFilter()">
        <option value="">Kein Limit</option>
        <option value="50">Bis 50 €</option>
        <option value="100">Bis 100 €</option>
      </select>
      <select id="filter-mannschaft" onchange="applyFilter()">
        <option value="">Alle Mannschaften</option>
        <option value="Bayern">Bayern</option>
        <option value="Dortmund">Dortmund</option>
      </select>
      <select id="filter-geschlecht" onchange="applyFilter()">
        <option value="">Alle Geschlechter</option>
        <option value="Herren">Herren</option>
        <option value="Damen">Damen</option>
        <option value="Unisex">Unisex</option>
      </select>
      <button type="button" class="reset-filter" onclick="resetFilter()">Zurücksetzen</button>
      <select id="sort-select" onchange="sortProducts(this.value)">
        <option value="asc">Preis ▲</option>
        <option value="desc">Preis ▼</option>
      </select>
      <button type="button" class="layout-toggle" onclick="toggleLayout()">Layout wechseln</button>
    </section>

    <section class="einzelprodukt-grid" id="produktContainer">
      <?php if (empty($filteredProducts)): ?>
        <p>Keine Produkte in dieser Kategorie gefunden.</p>
      <?php else: ?>
        <?php foreach ($filteredProducts as $produkt): ?>
          <?php
          $preis = floatval(preg_replace('/[^0-9.]/', '', $produkt["price"]));
          $mannschaft = stripos($produkt["name"], "Bayern") !== false ? "Bayern" : (stripos($produkt["name"], "Dortmund") !== false ? "Dortmund" : "");
          $discount = $produkt["discount"] ?? 0;
          ?>
          <?php
          $marke = $produkt["marke"] ?? "Unbekannt";
          $farbe = $produkt["farbe"] ?? "Unbekannt";
          $geschlecht = $produkt["geschlecht"] ?? "Unbekannt";
          ?>
          <section class="einzelprodukt"
            data-marke="<?= htmlspecialchars($marke) ?>"
            data-farbe="<?= htmlspecialchars($farbe) ?>"
            data-preis="<?= $preis ?>"
            data-mannschaft="<?= $mannschaft ?>"
            data-geschlecht="<?= htmlspecialchars($geschlecht) ?>">

            <section class="produkt-wrapper">
              <section class="image-wrapper">
                <img src="<?= htmlspecialchars($produkt["image_main"] ?? "")
                          ?>" alt="<?= htmlspecialchars($produkt["name"]) ?>">
              </section>

              <section class="produkt-info">
                <h3><?= htmlspecialchars($produkt["name"]) ?></h3>
                <p><?= htmlspecialchars($produkt["price"]) ?>€ <span>inkl. Mwst.</span>
                  <?php if ($discount > 0): ?>
                    <span class="rabatt">-<?= $discount ?>%</span>
                  <?php endif; ?>
                </p>
              </section>
            </section>

            <section class="button-row" data-iid="<?= (int)$produkt["iid"] ?>">
              <a href="index.php?page=product&action=detail&id=<?= (int)$produkt["iid"] ?>">
                <button>Details</button>
              </a>

              <button class="btn-add-to-cart"
                data-iid="<?= (int)$produkt['iid'] ?>"
                data-name="<?= htmlspecialchars($produkt['name']) ?>"
                data-price="<?= (float)$produkt['price'] ?? 0 ?>"
                data-image="<?= htmlspecialchars($produkt["image_main"] ?? "")
                            ?>">
                🛒
              </button>

              <button class="btn-add-to-watch"
                data-iid="<?= (int)$produkt['iid'] ?>"
                data-name="<?= htmlspecialchars($produkt['name']) ?>"
                data-price="<?= (float)$produkt['price'] ?? 0 ?>"
                data-image="<?= htmlspecialchars($produkt["image_main"] ?? "")
                            ?>">
                🤍
              </button>
            </section>
          </section>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <section class="pagination">
      <button>&laquo;</button>
      <button class="active">1</button>
      <button>2</button>
      <button>&raquo;</button>
    </section>
  </main>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>
