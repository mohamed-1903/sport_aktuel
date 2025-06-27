<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="form-wrapper" style="padding: 2em; max-width: 700px; margin: auto;">
  <h1 style="text-align: center;">➕ Produkt hinzufügen</h1>

  <form action="index.php?page=admin&action=addProduct" method="POST" class="product-form" style="display: flex; flex-direction: column; gap: 1em;">
    <label>Name:
      <input type="text" name="name" required>
    </label>

    <label>Preis (z.B. 89.99):
      <input type="number" step="0.01" name="price" required>
    </label>

    <label>Kategorie:
      <input type="text" name="category" required>
    </label>

    <label>Unterkategorie:
      <input type="text" name="subcategory">
    </label>

    <label>Marke:
      <input type="text" name="marke">
    </label>

    <label>Farbe:
      <input type="text" name="farbe">
    </label>

    <label>Geschlecht:
      <select name="geschlecht">
        <option value="">-- wählen --</option>
        <option value="Herren">Herren</option>
        <option value="Damen">Damen</option>
        <option value="Unisex">Unisex</option>
      </select>
    </label>

    <label>Bildpfad (z.B. img/bild.jpg):
      <input type="text" name="imageMain">
    </label>

    <label>Verfügbare Größen (kommagetrennt, z.B. 38,39,40 oder S,M,L):
      <input type="text" name="sizes">
    </label>

    <label>Beschreibung:
      <textarea name="description" rows="5" style="width: 100%;"></textarea>
    </label>

    <button type="submit" class="btn-checkout">✅ Produkt speichern</button>
    <a href="index.php?page=admin&action=dashboard">
      <button type="button" class="btn-zurueck-startseite">Zurück zum Dashboard</button>
    </a>
  </form>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>

