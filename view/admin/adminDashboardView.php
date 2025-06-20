<?php include 'view/layout/header.php'; ?>

<main class="form-wrapper" style="text-align: center; padding: 3em 1em;">
  <h1>🛠️ Admin-Dashboard</h1>

  <p>Willkommen im Administrationsbereich. Hier kannst du Benutzer und Produkte verwalten.</p>

  <div class="button-row" style="margin-top: 2em; display: flex; justify-content: center; gap: 2em; flex-wrap: wrap;">
    <a href="index.php?page=admin&action=manageUsers">
      <button class="btn-checkout">👥 Benutzer verwalten</button>
    </a>
    <a href="index.php?page=admin&action=addProduct">
      <button class="btn-checkout">➕ Produkt hinzufügen</button>
    </a>
    <a href="index.php?page=order&action=admin">
      <button class="btn-checkout">📦 Bestellungen einsehen</button>
    </a>
  </div>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<?php include 'view/layout/footer.php'; ?>
