<!-- Hussein -->

<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Hauptbereich für Produkt-Erstellung -->
<main class="form-wrapper" style="padding: 2em; max-width: 700px; margin: auto; margin-top: 2em;">
  <h1 style="text-align: center;">➕ Produkt hinzufügen</h1>

  <!-- Formular zum Hinzufügen eines Produkts -->
  <form action="index.php?page=admin&action=addProduct" method="POST" class="product-form" style="display: flex; flex-direction: column; gap: 1em;">

    <!-- Produktname -->
    <label>Name:
      <input type="text" name="name" required>
    </label>

    <!-- Preisangabe in Dezimalform -->
    <label>Preis (z.B. 89.99):
      <input type="number" step="0.01" name="price" required>
    </label>

    <!-- Pflichtfeld Kategorie -->
    <label>Kategorie:
      <input type="text" name="category" required>
    </label>

    <!-- Optional: Unterkategorie -->
    <label>Unterkategorie:
      <input type="text" name="subcategory">
    </label>

    <!-- Marke des Produkts -->
    <label>Marke:
      <input type="text" name="marke">
    </label>

    <!-- Farbe des Produkts -->
    <label>Farbe:
      <input type="text" name="farbe">
    </label>

    <!-- Zielgruppe des Produkts -->
    <label>Geschlecht:
      <select name="geschlecht">
        <option value="">-- wählen --</option>
        <option value="Herren">Herren</option>
        <option value="Damen">Damen</option>
        <option value="Unisex">Unisex</option>
      </select>
    </label>

    <!-- Hauptbildpfad (URL oder Pfad innerhalb des Projekts) -->
    <label>Bildpfad (z.B. img/bild.jpg):
      <input type="text" name="imageMain">
    </label>

    <!-- Größen als kommaseparierte Liste -->
    <label>Verfügbare Größen (kommagetrennt, z.B. 38,39,40 oder S,M,L):
      <input type="text" name="sizes">
    </label>

    <!-- Produktbeschreibung -->
    <label>Beschreibung:
      <textarea name="description" rows="5" style="width: 100%;"></textarea>
    </label>

    <!-- Absenden des Formulars -->
    <button type="submit" class="btn-checkout">✅ Produkt speichern</button>

    <!-- Zurück zum Admin-Dashboard -->
    <a href="index.php?page=admin&action=dashboard">
      <button type="button" class="btn-zurueck-startseite">Zurück zum Dashboard</button>
    </a>
  </form>
</main>

<!-- Scroll-to-top Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>