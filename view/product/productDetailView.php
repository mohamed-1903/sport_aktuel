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
    $price = $product['priceValue'] ?? 'Preis nicht verfügbar';
    $imageMain = $product['image_main'] ?? ($product['imagepath'] ?? 'img/placeholder.jpg');
    $images = $product['images'] ?? [$imageMain];
    $description = $product['description'] ?? 'Keine Beschreibung verfügbar';
    $sizes = $product['sizes'] ?? range(38, 46);
  ?>
    <!-- 📦 Einzelnes Produkt-Layout -->
    <section class="Eprodukt untereinander" data-product-index="<?= $index ?>">
      <h2 style="text-align:center; margin-bottom: 20px;">
        <?= count($productsToShow) === 2 ? "🛍️ Produkt " . ($index + 1) : "Produktdetails" ?>
      </h2>
      <div class="produkt-grid">
        <!-- 📸 Bilderbereich -->
        <div class="image-wrapper">
          <div class="zoom-bg-container" id="zoomContainer-<?= $index ?>">
            <img id="main-image-<?= $index ?>" src="<?= htmlspecialchars($imageMain) ?>" alt="<?= htmlspecialchars($name) ?>" />
          </div>

          <div class="additional-images">
            <?php foreach ($images as $imgIndex => $img): ?>
              <img src="<?= htmlspecialchars($img) ?>" />
            <?php endforeach; ?>
          </div>
        </div>

        <!-- 🛒 Produktdetails & Optionen -->
        <div>
          <h1><?= htmlspecialchars($name) ?></h1>
          <p id="original-price-<?= $index ?>" class="price-old" style="display: none;"></p>
          <p id="final-price-<?= $index ?>">
            <span id="finalPriceValue-<?= $index ?>" class="preis"><?= htmlspecialchars($price) ?></span>
            <del id="basePrice-<?= $index ?>" class="preis" style="display:none;"><?= htmlspecialchars($price) ?></del>
            <span id="discountLabel-<?= $index ?>" class="rabatt" style="display:none;">-20%</span>
          </p>

          <!-- 👕 Größenauswahl -->
          <label for="size">Größe:</label>
          <select id="size-<?= $index ?>" class="size-dropdown">
            <option value="" disabled selected>-- Bitte auswählen --</option>
            <?php foreach ($sizes as $size): ?>
              <option value="<?= $size ?>"><?= $size ?></option>
            <?php endforeach; ?>
          </select>

          <!-- 🔢 Mengenauswahl -->
          <label for="quantity">Menge:</label>
          <input type="number" id="quantity-<?= $index ?>" value="1" min="1" class="size-dropdown" />

          <!-- 🧺 Aktionen -->
          <div class="button-reihe" data-iid="<?= (int)$product['iid'] ?>">
            <button
              class="btn-add-to-cart"
              data-iid="<?= (int)$product['iid'] ?>"
              data-name="<?= htmlspecialchars($product['name']) ?>"
              data-price="<?= (float)$product['priceValue'] ?>"
              data-image="<?= htmlspecialchars($product['image_main']) ?>">
              🛒
            </button>

            <button
              class="btn-add-to-watch"
              data-iid="<?= (int)$product['iid'] ?>"
              data-name="<?= htmlspecialchars($product['name']) ?>"
              data-price="<?= (float)$product['priceValue'] ?>"
              data-image="<?= htmlspecialchars($product['image_main']) ?>">
              ❤️
            </button>
          </div>
          <!-- 📄 Produktbeschreibung ein-/ausklappbar -->
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
      <label for="pin">Rabatt-PIN eingeben:</label>
      <input type="text" id="pin" maxlength="5" placeholder="5-stellig" />
      <button onclick="checkPin()">Anwenden</button>
      <p id="rabatt-info"></p>

      <!-- 🎁 Geschenkoption -->
      <div class="gift-wrap">
        <label>
          <input type="checkbox" id="giftWrap" onchange="calculateAndDisplay()" />
          🎁 Geschenkverpackung (+ 2 €)
        </label>
      </div>

      <!-- 🔄 Zurücksetzen -->
      <button onclick="resetFields()">Felder zurücksetzen</button>
    </div>
  </section>

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

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<script src="js/watchlist.js"></script>
<script src="js/warenkorb.js"></script>

<?php include 'view/layout/footer.php'; ?>
