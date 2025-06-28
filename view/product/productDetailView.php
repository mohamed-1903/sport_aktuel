<?php include __DIR__ . '/../layout/header.php'; ?>
<?php if (!empty($_SESSION['message'])): ?>
  <div class="toast-popup show" id="toastMessage">
    <?= htmlspecialchars($_SESSION['message']) ?>
    <button type="button" class="close-toast" onclick="this.parentElement.classList.remove('show')">&times;</button>
  </div>
  <script>
    const toast = document.getElementById('toastMessage');
    if (toast) {
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
          <div class="price-breakdown"></div>


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
            🛒 In den Warenkorb
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

    </section>
  <?php endforeach; ?>

  <button id="showCompareBtn" class="compare-toggle-btn" aria-label="Vergleich öffnen" type="button">+</button>
  <div id="compareSection" class="compare-section hidden">
    <label for="compareInput">Produkt zum Vergleichen auswählen:</label>
    <div class="search-wrapper compare-search">
      <input type="text" id="compareShadow" class="compare-shadow" readonly tabindex="-1" />
      <input id="compareInput" list="compareOptions" placeholder="Name eingeben" autocomplete="off">
      <ul id="compareSuggestions" class="autocomplete-liste"></ul>
    </div>
    <?php $selectedIds = array_column($productsToShow, 'id'); ?>
    <datalist id="compareOptions">
      <?php foreach ($allProducts as $p): ?>
        <?php if (!in_array($p['id'], $selectedIds)): ?>
          <option data-id="<?= (int)$p['id'] ?>" value="<?= htmlspecialchars($p['name']) ?>"></option>
        <?php endif; ?>
      <?php endforeach; ?>
    </datalist>
    <button id="compareBtn" class="btn-compare">⚖️ Vergleichen</button>
  </div>

  <!-- 🧠 Ähnliche Produkte statisch -->
  <section class="produkte similar-products">
    <h2>Ähnliche Produkte</h2>
    <div class="produkt-grid">
      <?php for ($i = 1; $i <= 3; $i++): ?>
        <div class="Eprodukt">
          <img src="nike-shoe.jpg" alt="Ähnliches Produkt <?= $i ?>" />
          <h3>Nike Produkt <?= $i ?></h3>
          <p>€<?= number_format(199.99 - ($i - 1) * 20, 2, ',', '.') ?></p>
          <button>Details</button>
        </div>
      <?php endfor; ?>
    </div>
  </section>

  <?php foreach ($productsToShow as $index => $product):
    $ratings = getRatingsForProduct((int)$product['id'], $_SESSION['user_id'] ?? null);
    $avgRating = getAverageRating((int)$product['id']);
  ?>
    <section class="reviews" aria-live="polite" aria-atomic="true">

      <h3>Kundenbewertungen zu <?= htmlspecialchars($product['name']) ?></h3>
      <?php if ($avgRating): ?>
        <p>Durchschnittliche Bewertung: <?= number_format($avgRating, 1) ?>/5</p>
      <?php endif; ?>
      <?php if (empty($ratings)): ?>
        <p class="no-reviews">Noch keine Bewertungen.</p>
      <?php endif; ?>
      <?php foreach ($ratings as $r): ?>
        <div class="review<?= !empty($r['parent_id']) ? ' reply' : '' ?>" data-review-id="<?= (int)$r['id'] ?>"<?php if (!empty($r['parent_id'])): ?> data-parent-id="<?= (int)$r['parent_id'] ?>"<?php endif; ?>>
          <div class="review-content">
            <?php if (!empty($r['parent_name'])): ?>
              <small class="reply-info">Antwort auf <?= htmlspecialchars($r['parent_name']) ?></small>
            <?php endif; ?>
            <strong><?= htmlspecialchars($r['display_name'] ?: $r['username']) ?></strong>
            <small class="rating-date">
              <?= date('d.m.Y H:i', strtotime($r['created_at'])) ?>
            </small>
            <span class="rating-stars" style="pointer-events:none;">
              <?php for ($s = 5; $s >= 1; $s--): ?>
                <label><?= $s <= $r['stars'] ? '★' : '☆' ?></label>
              <?php endfor; ?>
            </span>
            <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
            <?php if (!empty($r['image_paths'])): ?>
              <div class="review-images" data-images='<?= json_encode($r['image_paths']) ?>'>
                <?php foreach ($r['image_paths'] as $idx => $img): ?>
                  <img src="<?= htmlspecialchars($img) ?>" data-idx="<?= $idx ?>" alt="Bild zur Bewertung">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="review-actions" data-user-vote="<?= htmlspecialchars($r['user_vote'] ?? '') ?>">
              <button type="button" class="like-btn<?= ($r['user_vote'] ?? '') === 'like' ? ' active' : '' ?>" data-id="<?= (int)$r['id'] ?>" aria-label="Bewertung positiv bewerten">
                👍 <span><?= (int)$r['likes'] ?></span>
              </button>
              <button type="button" class="dislike-btn<?= ($r['user_vote'] ?? '') === 'dislike' ? ' active' : '' ?>" data-id="<?= (int)$r['id'] ?>" aria-label="Bewertung negativ bewerten">
                👎 <span><?= (int)$r['dislikes'] ?></span>
              </button>
            <?php if (isset($_SESSION['user_id'])): ?>
              <button type="button" class="reply-btn" data-id="<?= (int)$r['id'] ?>" data-product-id="<?= (int)$product['id'] ?>" aria-label="Auf diese Bewertung antworten">Antworten</button>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $r['user_id'] || !empty($_SESSION['is_admin']))): ?>
              <form class="delete-rating-form" method="post" action="index.php?page=community&action=deleteRating" onsubmit="return confirm('Bewertung löschen?');">
                <input type="hidden" name="rating_id" value="<?= (int)$r['id'] ?>">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <button type="submit" class="btn-delete-rating" aria-label="Bewertung löschen">Löschen</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>


    </section>
    <?php if (isset($_SESSION['user_id'])): ?>
      <button type="button" class="open-review-modal btn-review" data-product-id="<?= (int)$product['id'] ?>">Bewertung schreiben</button>
    <?php else: ?>
      <p><a href="index.php?page=auth&action=login">Anmelden</a>, um eine Bewertung zu schreiben.</p>
    <?php endif; ?>
  <?php endforeach; ?>
</main>
<div id="ratingModal" class="review-modal hidden" role="dialog" aria-modal="true" aria-labelledby="ratingTitle">
  <div class="review-modal-content">
    <h2 id="ratingTitle">Bewertung abgeben</h2>

    <button type="button" class="review-close" onclick="closeRatingModal()">&times;</button>
    <form id="ratingForm" class="review-form" action="index.php?page=community&action=addRating" method="post" enctype="multipart/form-data">
      <input type="hidden" name="product_id" id="ratingProductId" value="">
      <input type="hidden" name="parent_id" id="ratingParentId" value="">
      <div id="replyTarget" class="reply-target hidden"></div>
      <label for="displayName">Name:</label>
      <input type="text" name="display_name" id="displayName" placeholder="Dein Name" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" required>
      <fieldset class="rating-stars">
        <legend class="sr-only">Sternebewertung</legend>
        <?php for ($s = 5; $s >= 1; $s--): ?>
          <input type="radio" id="modal-star<?= $s ?>" name="stars" value="<?= $s ?>" <?= $s == 5 ? ' checked' : '' ?>>
          <label for="modal-star<?= $s ?>" aria-label="<?= $s ?> Sterne">★</label>
        <?php endfor; ?>
      </fieldset>
      <label for="ratingComment">Kommentar:</label>
      <textarea id="ratingComment" name="comment" required placeholder="Deine Meinung..."></textarea>
      <div class="suggestion-bar" id="suggestionBar">
        <?php foreach ($reviewSuggestions as $rating => $texts): ?>
          <div class="suggestions-set <?= $rating == 5 ? '' : 'hidden' ?>" data-rating="<?= (int)$rating ?>">
            <?php foreach ($texts as $text): ?>
              <button type="button" class="suggest-btn"><?= htmlspecialchars($text) ?></button>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <label for="ratingImages">Bilder hochladen:</label>
      <input type="file" name="images[]" id="ratingImages" accept="image/*" multiple aria-label="Bilder zur Bewertung hochladen">
      <p class="help-text">Maximal 5 Bilder</p>
      <div id="imagePreviewList" class="image-preview-list hidden"></div>
      <button type="submit">Bewerten</button>
    </form>
</div>
</div>
<script>
  window.isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
  window.currentUserId = <?= isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null' ?>;
  window.isAdmin = <?= !empty($_SESSION['is_admin']) ? 'true' : 'false' ?>;
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {

    let compareProducts = [];
    let compareFocus = -1;
    let selectedCompareId = null;
    const currentCompareIds = <?= json_encode(array_column($productsToShow, 'id')) ?>;

    const showCompareBtn = document.getElementById('showCompareBtn');
    const compareSection = document.getElementById('compareSection');
    const compareInput = document.getElementById('compareInput');
    const compareShadow = document.getElementById('compareShadow');
    const compareList = document.getElementById('compareSuggestions');

    if (showCompareBtn) {
      showCompareBtn.addEventListener('click', () => {
        compareSection.classList.remove('hidden');
        showCompareBtn.classList.add('hidden');
        compareInput.focus();
      });
    }

    fetch('data/products.json')
      .then(res => res.json())
      .then(data => {
        compareProducts = data.products || [];
      });

    function updateCompareList() {
      const val = compareInput.value.toLowerCase().trim();
      selectedCompareId = null;
      compareList.innerHTML = '';
      compareFocus = -1;
      if (val.length < 1) {
        compareList.style.display = 'none';
        compareShadow.value = '';
        return;
      }
      const matches = compareProducts
        .filter(p => !currentCompareIds.includes(p.iid))
        .filter(p => [p.name, p.marke, p.farbe, p.geschlecht, p.category, p.subcategory]
          .map(s => (s || '').toLowerCase())
          .join(' ')
          .includes(val)
        );
      if (matches.length) {
        const match = matches.find(p => (p.name || '').toLowerCase().startsWith(val));
        compareShadow.value = match?.name || '';
        matches.forEach(p => {
          const li = document.createElement('li');
          li.dataset.id = p.iid;
          const price = typeof p.priceValue !== 'undefined' ?
            parseFloat(p.priceValue).toFixed(2) + ' €' :
            p.price || 'Preis?';
          li.innerHTML = `<img src="${p.imageMain || ''}" alt="${p.name}" /><div><strong>${p.name}</strong><br><small>${price}</small></div>`;
          li.addEventListener('click', () => {
            compareInput.value = p.name;
            compareShadow.value = '';
            compareList.style.display = 'none';
            selectedCompareId = p.iid;
          });
          compareList.appendChild(li);
        });
        compareList.style.display = 'block';
      } else {
        compareList.innerHTML = `<li class="keine-treffer-box"><div class="keine-treffer-icon">🔍</div><div><strong>Keine Treffer</strong></div></li>`;
        compareList.style.display = 'block';
        compareShadow.value = '';
      }
    }

    function handleCompareNav(e) {
      const items = compareList.querySelectorAll('li');
      if (!items.length) return;
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        compareFocus = (compareFocus + 1) % items.length;
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        compareFocus = (compareFocus - 1 + items.length) % items.length;
      } else if (e.key === 'Enter' && compareFocus >= 0) {
        e.preventDefault();
        items[compareFocus].click();
      } else if (e.key === 'Tab' && compareShadow.value) {
        e.preventDefault();
        compareInput.value = compareShadow.value;
        compareShadow.value = '';
        compareList.style.display = 'none';
      } else if (e.key === 'Escape') {
        compareList.style.display = 'none';
        compareShadow.value = '';
      }
      items.forEach((li, i) => {
        li.classList.toggle('focused', i === compareFocus);
        if (i === compareFocus) li.scrollIntoView({
          block: 'nearest'
        });
      });
    }

    compareInput.addEventListener('input', updateCompareList);
    compareInput.addEventListener('keydown', handleCompareNav);

    document.getElementById('compareBtn').addEventListener('click', () => {
      const btn = document.getElementById('compareBtn');
      btn.classList.add('pulse-highlight');
      setTimeout(() => btn.classList.remove('pulse-highlight'), 1000);

      const inputVal = compareInput.value.trim();
      let secondId = selectedCompareId;
      if (!secondId) {
        const option = Array.from(document.querySelectorAll('#compareOptions option')).find(opt => opt.value === inputVal);
        if (option) secondId = option.dataset.id;
      }
      if (!secondId) {
        alert('Produkt nicht gefunden');
        return;
      }
      const allIds = currentCompareIds.concat(secondId);
      const params = allIds.map((v, i) => `id${i === 0 ? '' : i + 1}=${v}`).join('&');
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
  });
</script>
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>