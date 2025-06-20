<?php include 'view/layout/header.php'; ?>

<main class="produkte">
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
    $name = $product['name'] ?? 'Produktname nicht verfügbar';
    $price = $product['price'] ?? 'Preis nicht verfügbar';
    $imageMain = $product['image_main'] ?? ($product['imagepath'] ?? 'img/placeholder.jpg');
    $images = $product['images'] ?? [$imageMain];
    $description = $product['description'] ?? 'Keine Beschreibung verfügbar';
    $sizes = $product['sizes'] ?? range(38, 46);
  ?>
  <section class="Eprodukt untereinander" data-product-index="<?= $index ?>">
    <h2 style="text-align:center; margin-bottom: 20px;">
      <?= count($productsToShow) === 2 ? "🛍️ Produkt " . ($index + 1) : "Produktdetails" ?>
    </h2>
    <div class="produkt-grid">
      <div class="image-wrapper">
        <div class="zoom-bg-container" id="zoomContainer">
          <img id="main-image" src="<?= htmlspecialchars($imageMain) ?>" alt="<?= htmlspecialchars($name) ?>" />
        </div>
        <div class="additional-images">
          <?php foreach ($images as $imgIndex => $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" onclick="changeImage('<?= htmlspecialchars($img) ?>', <?= $imgIndex ?>)" />
          <?php endforeach; ?>
        </div>
      </div>

      <div>
        <h1><?= htmlspecialchars($name) ?></h1>
        <p id="final-price">
          <span id="finalPriceValue" class="preis">€<?= htmlspecialchars($price) ?></span>
        </p>

        <label for="sizes">Größe:</label>
        <select id="sizes" class="size-dropdown">
          <option value="" disabled selected>-- Bitte auswählen --</option>
          <?php foreach ($sizes as $size): ?>
          <option value="<?= $size ?>"><?= $size ?></option>
          <?php endforeach; ?>
        </select>

        <label for="quantity">Menge:</label>
        <input type="number" id="quantity" value="1" min="1" class="size-dropdown" />
        <div class="button-reihe" data-iid="<?= (int)$product['iid'] ?>">
          <button class="btn-add-to-cart"
                  data-iid="<?= (int)$product['iid'] ?>"
                  data-name="<?= htmlspecialchars($product['name']) ?>"
                  data-price="<?= (float)$product['priceaValue'] ?>"
                  data-image="<?= htmlspecialchars($product['image_main']) ?>">
            🛒 In den Warenkorb
          </button>
          <button class="btn-add-to-watch"
                  data-iid="<?= (int)$product['iid'] ?>"
                  data-name="<?= htmlspecialchars($product['name']) ?>"
                  data-price="<?= (float)$product['priceaValue'] ?>"
                  data-image="<?= htmlspecialchars($product['image_main']) ?>">
            🤍 Merken
          </button>
        </div>

        <div class="produkt-info">
          <h3 id="toggle-info">
            <span class="toggle-icon">+</span> Produktinformationen
          </h3>
          <div id="description-full" class="hidden">
            <p><?= nl2br(htmlspecialchars($description)) ?></p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endforeach; ?>
</main>
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".Eprodukt").forEach(section => {
    const addButton = section.querySelector(".btn-add-to-cart");
    const sizeSelect = section.querySelector("select.size-dropdown");
    const quantityInput = section.querySelector("input[type='number']");

    if (!addButton || !sizeSelect || !quantityInput) return;

    addButton.addEventListener("click", () => {
      const size = sizeSelect.value;
      const quantity = parseInt(quantityInput.value || 1);

      if (!size) {
        alert("Bitte wähle eine Größe aus.");
        return;
      }

      const item = {
        id: addButton.dataset.iid,
        name: addButton.dataset.name,
        price: addButton.dataset.price,
        image: addButton.dataset.image,
        size: size,
        quantity: quantity
      };

      fetch("index.php?page=cart&action=add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(item)
      }).then(res => {
        if (res.ok) {
          alert("✅ Produkt wurde zum Warenkorb hinzugefügt.");
        } else {
          alert("❌ Fehler beim Hinzufügen des Produkts.");
        }
      });
    });
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
