<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php?page=auth&action=login&redirect=user&action=profile");
  exit;
}

include 'view/layout/header.php';
?>

<main class="form-wrapper" style="text-align: center; padding: 3em 1em;">
  <h1>👤 Mein Profil</h1>

  <p><strong>Benutzername:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
  <p><strong>User-ID:</strong> <?= (int)$_SESSION['user_id'] ?></p>

  <div class="button-row" style="margin-top: 2em;">
    <a href="index.php?page=auth&action=logout">
      <button class="btn-checkout">Abmelden</button>
    </a>
    <a href="index.php">
      <button class="btn-zurueck-startseite">Zur Startseite</button>
    </a>
  </div>
</main>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/style_modification.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>
<?php include 'view/layout/footer.php'; ?>
