<?php include 'view/layout/header.php'; ?>

<main class="produkte">
  <!-- 🔍 Zoom Modal -->
  <div id="zoomModal" class="zoom-modal hidden">
    <div class="zoom-modal-content" id="zoomModalContent">
      <span class="zoom-close" onclick="closeZoomModal()">×</span>
      <img id="zoom-image" src="" alt="Zoomed" />
      <div class="zoom-controls">
        <button onclick="prevZoomImage()">←</button>
        <button onclick="zoomIn()">+</button>
        <button onclick="zoomOut()">−</button>
        <button onclick="resetZoom()">⟳</button>
        <button onclick="nextZoomImage()">→</button>
      </div>
    </div>
  </div>
  <?php foreach ($productsToShow as $index => $product):
    // 🧩 Produktdaten extrahieren mit Fallbacks
    $name = $product['name'] ?? 'Produktname nicht verfügbar';
    $price = isset($product['priceValue']) && is_numeric($product['priceValue']) ? $product['priceValue'] : 0;
    $imageMain = $product['image_main'] ?? ($product['imagepath'] ?? 'img/placeholder.jpg');
    $images = $product['images'] ?? [$imageMain];
    $description = $product['description'] ?? 'Keine Beschreibung verfügbar';
    $sizes = $product['sizes'] ?? range(38, 46);
  ?>
    <!-- 📦 Einzelnes Produkt-Layout -->
    <section class="Eprodukt untereinander" data-product-index="<?= $index ?>">
      <h2 style="text-align:center; margin-bottom: 20px;">
        <?= count($productsToShow) > 1 ? "🛍️ Produkt " . ($index + 1) : "Produktdetails" ?>
        <?php if (count($productsToShow) > 1): ?>
          <button class="remove-product" data-remove-index="<?= $index ?>">❌</button>
        <?php endif; ?>
      </h2>
      <div class="produkt-grid">
        <!-- 📸 Bilderbereich -->
        <div class="image-wrapper">
          <div class="zoom-bg-container" id="zoomContainer-<?= $index ?>">
            <img id="main-image-<?= $index ?>" src="<?= htmlspecialchars($imageMain) ?>" alt="<?= htmlspecialchars($name) ?>" />
          </div>
          <div class="additional-images">
            <?php foreach ($images as $imgIndex => $img): ?>
              <img src="<?= htmlspecialchars($img) ?>" onclick="changeImage('<?= htmlspecialchars($img) ?>', <?= $imgIndex ?>)" />
            <?php endforeach; ?>
          </div>
        </div>

        <!-- 🛒 Produktdetails & Optionen -->
        <div>
          <h1><?= htmlspecialchars($name) ?></h1>
          <p id="original-price-<?= $index ?>" class="price-old" style="display: none;"></p>
          <p id="final-price-<?= $index ?>">
            <?php if (isset($product['priceValue']) && is_numeric($product['priceValue'])): ?>
              <span id="finalPriceValue-<?= $index ?>" class="preis">
                <?= number_format((float)$product['priceValue'], 2, ',', '.') ?>€ inkl. Mwst.
              </span>
              <del id="basePrice-<?= $index ?>" class="preis" style="display:none;">
                <?= number_format((float)$product['priceValue'], 2, ',', '.') ?>€
              </del>
            <?php else: ?>
              <span class="preis">Preis nicht verfügbar</span>
            <?php endif; ?>
            <span id="discountLabel-<?= $index ?>" class="rabatt" style="display:none;">-20%</span>
          </p>


          <!-- 👕 Größenauswahl -->
          <label for="size-<?= $index ?>">Größe:</label>
          <select id="size-<?= $index ?>" class="size-dropdown">
            <option value="" disabled selected>-- Bitte auswählen --</option>
            <?php foreach ($sizes as $size): ?>
              <option value="<?= $size ?>"><?= $size ?></option>
            <?php endforeach; ?>
          </select>

          <!-- 🔢 Mengenauswahl -->
          <label for="quantity-<?= $index ?>">Menge:</label>
          <input type="number" id="quantity-<?= $index ?>" value="1" min="1" class="size-dropdown" />

          <!-- 🧺 Aktionen -->
          <div class="button-reihe" data-iid="<?= (int)$product['id'] ?>">
            <?php
            $iid = isset($product['iid']) ? (int)$product['iid'] : 0;
            $name = $product['name'] ?? 'Unbekanntes Produkt';
            $price = isset($product['priceValue']) ? (float)$product['priceValue'] : 0.00;
            $image = $product['image_main'] ?? 'img/placeholder.jpg';
            ?>

            <!-- 🛒 In den Warenkorb -->
            <button
              class="btn-add-to-cart"
              data-iid="<?= $iid ?>"
              data-name="<?= htmlspecialchars($name) ?>"
              data-price="<?= $price ?>"
              data-image="<?= htmlspecialchars($image) ?>">
              🛒
            </button>

            <!-- ❤️ Zur Merkliste -->
            <button
              class="btn-add-to-watch"
              data-iid="<?= $iid ?>"
              data-name="<?= htmlspecialchars($name) ?>"
              data-price="<?= $price ?>"
              data-image="<?= htmlspecialchars($image) ?>">
              ❤️
            </button>


          </div>
          <!-- 📄 Produktbeschreibung -->
          <div class="produkt-info">
            <h3 id="toggle-info-<?= $index ?>">
              <span class="toggle-icon">+</span> Produktinformationen
            </h3>
            <div id="description-full-<?= $index ?>" class="hidden">
              <p><?= nl2br(htmlspecialchars($description)) ?></p>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endforeach; ?>

  <!-- 💰 Steuerberechnung + Rabattcode -->
  <section class="preis-container">
    <label for="netto">Preis ohne Steuern (€):</label>
    <input class="size-dropdown" type="number" id="netto">
    <button onclick="zeigePreis()">Berechne Bruttopreis</button>
    <div id="priceResults">
      <p id="bruttoErgebnis"></p>
    </div>

    <div class="button-rows">
      <!-- 🎟 Rabattcode -->
      <label for="pin-<?= $index ?>">Rabatt-PIN eingeben:</label>
      <input type="text" id="pin-<?= $index ?>" maxlength="5" placeholder="5-stellig" />
      <p id="rabatt-info-<?= $index ?>"></p>

      <!-- 🎁 Geschenkoption -->
      <div class="gift-wrap">
        <label>
          <input type="checkbox" id="giftWrap-<?= $index ?>" />
          🎁 Geschenkverpackung (+ 2 €)
        </label>
      </div>

      <!-- 🔄 Zurücksetzen -->
      <button onclick="resetFields()">Felder zurücksetzen</button>
    </div>
  </section>
  <div class="compare-section">
    <label for="compareInput">Produkt zum Vergleichen auswählen:</label>
    <input id="compareInput" list="compareOptions" placeholder="Name eingeben">
    <datalist id="compareOptions">
      <?php foreach ($allProducts as $p): ?>
        <?php if ($p['id'] != $currentId): ?>
          <option data-id="<?= (int)$p['id'] ?>" value="<?= htmlspecialchars($p['name']) ?>"></option>
        <?php endif; ?>
      <?php endforeach; ?>
    </datalist>
    <button id="compareBtn">Produkt vergleichen</button>
  </div>
  <!-- 🧾 Dynamische Sammelliste / Warenkorb -->
  <section id="sammelliste">
    <h2>🗂️ Dein Warenkorb</h2>
    <ul id="sammelliste-items"></ul>
  </section>

  <!-- 🧠 Ähnliche Produkte statisch -->
  <section class="produkte">
    <h2>Ähnliche Produkte</h2>
    <div class="produkt-grid">
      <?php for ($i = 1; $i <= 3; $i++): ?>
        <div class="Eprodukt">
          <img src="nike-shoe.jpg" alt="Ähnliches Produkt <?= $i ?>" />
          <h3>Nike Produkt <?= $i ?></h3>
          <p>€<?= 199.99 - ($i - 1) * 20 ?></p>
          <button>Details</button>
        </div>
      <?php endfor; ?>
    </div>
  </section>
</main>
<script>
  document.getElementById('compareBtn').addEventListener('click', () => {
    const input = document.getElementById('compareInput').value.trim();
    const options = document.querySelectorAll('#compareOptions option');
    let secondId = null;
    options.forEach(opt => {
      if (opt.value === input) secondId = opt.dataset.id;
    });
    if (!secondId) {
      alert('Produkt nicht gefunden');
      return;
    }
    const existingIds = <?= json_encode(array_column($productsToShow, 'id')) ?>;
    const allIds = existingIds.concat(secondId);
    const params = allIds
      .map((v, i) => `id${i === 0 ? '' : i + 1}=${v}`)
      .join('&');
    window.location.href = `index.php?page=product&action=detail&${params}`;
  });

  document.querySelectorAll('.remove-product').forEach(btn => {
    btn.addEventListener('click', () => {
      const idx = parseInt(btn.dataset.removeIndex, 10);
      const ids = <?= json_encode(array_column($productsToShow, 'id')) ?>;
      ids.splice(idx, 1);
      if (ids.length === 0) {
        window.location.href = 'index.php?page=product&action=list';
        return;
      }
      const params = ids
        .map((v, i) => `id${i === 0 ? '' : i + 1}=${v}`)
        .join('&');
      window.location.href = `index.php?page=product&action=detail&${params}`;
    });
  });
</script>
<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<script src="js/watchlist.js"></script>
<script src="js/warenkorb.js"></script>

<?php include 'view/layout/footer.php'; ?>
