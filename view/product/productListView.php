<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="main-container">
 <aside class="sidebar">
  <h2><?= $category === "Alle Produkte" ? "Alle Unterkategorien" : htmlspecialchars($category) ?></h2>
  <ul>
    <?php
    $submap = [
      "Sportbekleidung" => ["Trikots", "Socken", "Handschuhe", "Trainingsanzüge", "Trainingsjacken", "Trainingshosen", "T-Shirts", "Poloshirts"],
      "Fußballschuhe" => ["Stollen", "Kunstrasen", "Hallenschuhe"],
      "Zubehör" => ["Schienbeinschoner", "Fußbälle", "Sporttaschen"],
      "Sale %" => ["Sportbekleidung", "Fußballschuhe", "Zubehör"]
    ];

    // 🔍 Nur echte Unterkategorien bei "Alle Produkte"
    if ($category === "Alle Produkte") {
      // Alle Werte aus allen Arrays extrahieren (nur die "leaf nodes")
      $flatSubs = [];
      foreach ($submap as $key => $subs) {
        foreach ($subs as $s) {
          if (!isset($submap[$s])) {
            $flatSubs[] = $s;
          }
        }
      }
      $allSubs = array_unique($flatSubs);
      sort($allSubs);
    } else {
      $allSubs = $submap[$category] ?? [];
    }

    foreach ($allSubs as $sub):
      $active = (strcasecmp($subcategory, $sub) === 0) ? 'style="font-weight:bold;"' : '';
      $link = "index.php?page=product&action=list&category=" . urlencode($category) . "&subcategory=" . urlencode($sub);
      echo "<li><a href='$link' $active>" . htmlspecialchars($sub) . "</a></li>";
    endforeach;
    ?>
  </ul>
</aside>


  <main class="main-content">
    <h1><?= htmlspecialchars($displayTitle) ?></h1>
    <div class="filter-tools">
      <button type="button" class="filter-toggle" onclick="toggleFilterBar()">
        Filter ausblenden ▲
      </button>
      <button type="button" class="layout-toggle" onclick="toggleLayout()">☰ Liste anzeigen</button>
      <button type="button" class="reset-filter" onclick="resetFilter()">Zurücksetzen</button>
    </div>
    <section class="filterbar">
      <div class="filter-controls">
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
        <select id="filter-price" onchange="applyFilter()">
          <option value="">Alle Preise</option>
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
        <select id="sort-select" onchange="sortProducts(this.value)">
          <option value="default">Standardsortierung</option>
          <option value="price-asc">Preis aufsteigend ▲</option>
          <option value="price-desc">Preis absteigend ▼</option>
          <option value="name-asc">Name A–Z</option>
          <option value="name-desc">Name Z–A</option>
        </select>
      </div>
    </section>
    <ul class="einzelprodukt-grid" id="produktContainer">

      <?php if (empty($filteredProducts)): ?>
        <p>Keine Produkte in dieser Kategorie gefunden.</p>
      <?php else: ?>
        <?php foreach ($filteredProducts as $produkt): ?>
          <?php
          $preis = floatval(preg_replace('/[^0-9.]/', '', $produkt["price"]));
          $mannschaft = stripos($produkt["name"], "Bayern") !== false ? "Bayern" : (stripos($produkt["name"], "Dortmund") !== false ? "Dortmund" : "");
          $discount = $produkt["discount"] ?? 0;
          $salePrice = $discount > 0 ? $preis * (1 - $discount / 100) : $preis;
          ?>

          <?php
          $marke = $produkt["marke"] ?? "Unbekannt";
          $farbe = $produkt["farbe"] ?? "Unbekannt";
          $geschlecht = $produkt["geschlecht"] ?? "Unbekannt";
          ?>
          <li class="einzelprodukt"
            data-id="<?= (int)$produkt['iid'] ?>"
            data-marke="<?= htmlspecialchars($marke) ?>"
            data-farbe="<?= htmlspecialchars($farbe) ?>"
            data-preis="<?= $preis ?>"
            data-mannschaft="<?= $mannschaft ?>"
            data-geschlecht="<?= htmlspecialchars($geschlecht) ?>">

            <div class="image-wrapper">
              <img src="<?= htmlspecialchars($produkt["image_main"] ?? "") ?>"
                alt="<?= htmlspecialchars($produkt["name"]) ?>">
            </div>

            <div class="produkt-info">
              <h3><?= htmlspecialchars($produkt["name"]) ?></h3>
              <?php if ($discount > 0): ?>
                <?php $neu = $preis * (1 - $discount / 100); ?>
                <p>
                  <del class="old-price">
                    <?= number_format($preis, 2, ',', '.') ?>€ inkl. Mwst.
                  </del>
                  <span><?= number_format($neu, 2, ',', '.') ?>€ inkl. Mwst.</span>
                  <span class="rabatt">-<?= $discount ?>%</span>
                </p>
              <?php else: ?>
                <p><?= htmlspecialchars($produkt["price"]) ?>€
                  <span>inkl. Mwst.</span>
                </p>
              <?php endif; ?>


              <div class="button-row" data-iid="<?= (int)$produkt["iid"] ?>">
                <a href="index.php?page=product&action=detail&id=<?= (int)$produkt["iid"] ?>">
                  <button>Details</button>
                </a>

                <button class="btn-add-to-cart"
                  data-iid="<?= (int)$produkt['iid'] ?>"
                  data-name="<?= htmlspecialchars($produkt['name']) ?>"
                  data-price="<?= $salePrice ?>"
                  data-image="<?= htmlspecialchars($produkt["image_main"] ?? "") ?>">
                  🛒
                </button>

                <button class="btn-add-to-watch"
                  data-iid="<?= (int)$produkt['iid'] ?>"
                  data-name="<?= htmlspecialchars($produkt['name']) ?>"
                  data-price="<?= $salePrice ?>"
                  data-image="<?= htmlspecialchars($produkt["image_main"] ?? "") ?>">

                  🤍
                </button>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>


    <section class="pagination"></section>

  </main>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>