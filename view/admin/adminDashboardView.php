<!-- Hussein -->

<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Hauptinhalt: Admin-Dashboard -->
<main class="form-wrapper" style="text-align: center; padding: 3em 1em;">
  <h1>🛠️ Admin-Dashboard</h1>

  <!-- Begrüßungstext -->
  <p>Willkommen im Administrationsbereich. Hier kannst du Benutzer und Produkte verwalten.</p>

  <!-- Navigationsbuttons für Admin-Funktionen -->
  <div class="button-row" style="margin-top: 2em; display: flex; justify-content: center; gap: 2em; flex-wrap: wrap;">

    <!-- Button zur Benutzerverwaltung -->
    <a href="index.php?page=admin&action=manageUsers">
      <button class="btn-checkout">👥 Benutzer verwalten</button>
    </a>

    <!-- Button zur Produktanlage -->
    <a href="index.php?page=admin&action=addProduct">
      <button class="btn-checkout">➕ Produkt hinzufügen</button>
    </a>

    <!-- Button zur Bestellübersicht -->
    <a href="index.php?page=order&action=admin">
      <button class="btn-checkout">📦 Bestellungen einsehen</button>
    </a>

    <!-- Button zur Produktverwaltung -->
    <a href="index.php?page=admin&action=manageProducts">
      <button class="btn-checkout">🛍️ Produkte verwalten</button>
    </a>
  </div>
</main>

<!-- Scroll-to-Top Button -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php include __DIR__ . '/../layout/footer.php'; ?>