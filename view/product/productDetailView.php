<?php include __DIR__ . '/../layout/header.php'; ?>
<?php if (!empty($_SESSION['message'])): ?>
  <div class="toast-popup show" id="toastMessage">
    <?= htmlspecialchars($_SESSION['message']) ?>
  </div>
  <script>
    const toast = document.getElementById('toastMessage');
    if (toast) {
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
      }, 3000);
    }
  </script>
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>


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
    $backImage = $images[1] ?? $imageMain;
    $description = $product['description'] ?? 'Keine Beschreibung verfügbar';
    $sizes = $product['sizes'] ?? range(38, 46);
  ?>
    <!-- 📦 Einzelnes Produkt-Layout -->
    <section class="Eprodukt untereinander" data-product-index="<?= $index ?>" data-base-price="<?= isset($product['priceValue']) ? (float)$product['priceValue'] : 0 ?>">
      <h2 style="text-align:center; margin-bottom: 20px;">
        <?= count($productsToShow) > 1 ? "🛍️ Produkt " . ($index + 1) : "Produktdetails" ?>
        <?php if (count($productsToShow) > 1): ?>
          <button class="remove-product" data-remove-index="<?= $index ?>">❌</button>
        <?php endif; ?>
      </h2>
      <div class="detail-grid">
        <!-- 📸 Bilderbereich -->
        <div class="image-wrapper">
          <div class="zoom-bg-container" id="zoomContainer-<?= $index ?>">
            <img id="main-image-<?= $index ?>" src="<?= htmlspecialchars($imageMain) ?>" alt="<?= htmlspecialchars($name) ?>" />
          </div>

          <div class="additional-images">

            <?php foreach ($images as $imgIndex => $img): ?>
              <img src="<?= htmlspecialchars($img) ?>"
                onclick="changeImage(this.closest('.Eprodukt'), '<?= htmlspecialchars($img) ?>', <?= $imgIndex ?>)" />
            <?php endforeach; ?>
          </div>
        </div>

        <!-- 🛒 Produktdetails & Optionen -->
        <div>
          <h1 class="product-name"><?= htmlspecialchars($name) ?></h1>

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

          <?php if (stripos($product['subcategory'] ?? '', 'Trikots') !== false): ?>
            <button type="button" class="btn-show-custom" id="customBtn-<?= $index ?>">Produkt individualisieren</button>
            <div class="custom-toggle-wrap">
              <label class="custom-switch">
                <input type="checkbox" id="customToggle-<?= $index ?>" />
                <span class="slider"></span>
              </label>
              <span>Individualisierung</span>
            </div>
          <?php endif; ?>

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
            <button onclick="resetFields(this.closest('.Eprodukt'))">Felder zurücksetzen</button>
          </div>
        </div>

        <?php if (stripos($product['subcategory'] ?? '', 'Trikots') !== false): ?>
          <div class="option-custom hidden" id="customSection-<?= $index ?>">
            <div class="customization">
              <label for="player-<?= $index ?>">Spieler wählen:</label>
              <select id="player-<?= $index ?>" class="size-dropdown player-select"></select>
              <label for="customName-<?= $index ?>">Name:</label>
              <input type="text" id="customName-<?= $index ?>" class="size-dropdown custom-name" maxlength="20" />
              <label for="customNumber-<?= $index ?>">Nummer:</label>
              <input type="number" id="customNumber-<?= $index ?>" class="size-dropdown custom-number" min="0" max="99" />
              <div class="badges">
                <label><input type="checkbox" id="badgeBL-<?= $index ?>" class="badge-bl"> Bundesliga-Badge</label>
                <label><input type="checkbox" id="badgeCL-<?= $index ?>" class="badge-cl"> Champions-League-Badge</label>
              </div>
              <div class="jersey-preview" id="jerseyPreview-<?= $index ?>">
                <img src="<?= htmlspecialchars($backImage) ?>" alt="Rückenansicht" />
                <div class="overlay-name"></div>
                <div class="overlay-number"></div>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <div class="price-breakdown"></div>

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
        <div class="price-breakdown"></div>
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

  <?php foreach ($productsToShow as $index => $product):
    $ratings = getRatingsForProduct((int)$product['id']);
    $avgRating = getAverageRating((int)$product['id']);
  ?>
    <section class="reviews">
      <h3>Kundenbewertungen zu <?= htmlspecialchars($product['name']) ?></h3>
      <?php if ($avgRating): ?>
        <p>Durchschnittliche Bewertung: <?= number_format($avgRating, 1) ?>/5</p>
      <?php endif; ?>
      <?php foreach ($ratings as $r): ?>
        <div class="review">
          <strong><?= htmlspecialchars($r['username']) ?></strong>
          <span class="rating-stars" style="pointer-events:none;">
            <?php for ($s = 5; $s >= 1; $s--): ?>
              <label><?= $s <= $r['stars'] ? '★' : '☆' ?></label>
            <?php endfor; ?>
          </span>
          <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
          <?php if (!empty($r['image_path'])): ?>
            <img src="<?= htmlspecialchars($r['image_path']) ?>" alt="Bild zur Bewertung">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <?php if (isset($_SESSION['user_id'])): ?>
        <button type="button" class="open-review-modal btn-review" data-product-id="<?= (int)$product['id'] ?>">Bewertung schreiben</button>
      <?php else: ?>
        <p><a href="index.php?page=auth&action=login">Anmelden</a>, um eine Bewertung zu schreiben.</p>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>
</main>
<div id="ratingModal" class="review-modal hidden">
  <div class="review-modal-content">
    <h2>Bewertung abgeben</h2>
    <button type="button" class="review-close" onclick="closeRatingModal()">&times;</button>
    <form id="ratingForm" class="review-form" action="index.php?page=community&action=addRating" method="post" enctype="multipart/form-data">
      <input type="hidden" name="product_id" id="ratingProductId" value="">
      <div class="rating-stars">
        <?php for ($s = 5; $s >= 1; $s--): ?>
          <input type="radio" id="modal-star<?= $s ?>" name="stars" value="<?= $s ?>" <?= $s == 5 ? ' checked' : '' ?>>
          <label for="modal-star<?= $s ?>">★</label>
        <?php endfor; ?>
      </div>
      <textarea name="comment" required placeholder="Deine Meinung..."></textarea>
      <input type="file" name="image" accept="image/*">
      <button type="submit">Bewerten</button>
    </form>
  </div>
</div>
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

<?php include __DIR__ . '/../layout/footer.php'; ?>