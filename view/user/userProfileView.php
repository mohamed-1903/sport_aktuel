<!-- Mohamed -->

<?php
// Startet eine Session, falls noch keine existiert
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Prüft, ob der Benutzer eingeloggt ist (Session-Variable gesetzt)
if (!isset($_SESSION['user_id'])) {
  // Wenn nicht eingeloggt, wird zur Login-Seite weitergeleitet
  // Mit redirect-Parameter, um nach Login zurück zur Profilseite zu gelangen
  header("Location: index.php?page=auth&action=login&redirect=user&action=profile");
  exit;
}

// Header wird eingebunden (enthält meist HTML-Kopfbereich, Navigation usw.)
include __DIR__ . '/../layout/header.php';
?>

<!-- Hauptbereich für das Benutzerprofil -->
<main class="form-wrapper" style="text-align: center; padding: 3em 1em;">
  <h1>👤 Mein Profil</h1>

  <!-- Anzeige von Benutzernamen (sicher mit htmlspecialchars gegen XSS) -->
  <p><strong>Benutzername:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>

  <!-- Anzeige der User-ID (gecastet auf int zur Sicherheit) -->
  <p><strong>User-ID:</strong> <?= (int)$_SESSION['user_id'] ?></p>

  <!-- Zwei Buttons: Abmelden und Zurück zur Startseite -->
  <div class="button-row" style="margin-top: 2em;">
    <!-- Logout-Link -->
    <a href="index.php?page=auth&action=logout">
      <button class="btn-checkout">Abmelden</button>
    </a>

    <!-- Zurück zur Startseite -->
    <a href="index.php">
      <button class="btn-zurueck-startseite">Zur Startseite</button>
    </a>
  </div>
</main>

<!-- Button zum Hochscrollen der Seite -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php 
// Footer-Datei wird eingebunden (enthält typischerweise schließende Tags oder Scripts)
include __DIR__ . '/../layout/footer.php'; 
?>
