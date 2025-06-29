<!-- Mohamed -->
<?php 
// Fügt den Header-Bereich der Seite ein (z. B. Navigation, Styles, Session-Handling, etc.)
include __DIR__ . '/../layout/header.php'; 
?>

<main class="form-wrapper" style="text-align: center; padding: 3em 1em;">
    <!-- Hauptüberschrift: Hinweis über erfolgreiches Logout -->
    <h1>✅ Erfolgreich ausgeloggt</h1>

    <!-- Danksagung an den Benutzer -->
    <p>Danke für deinen Besuch bei <strong>SportX</strong>!</p>

    <!-- Button zur Startseite -->
    <a href="index.php">
        <button class="btn-zurueck-startseite">Zurück zur Startseite</button>
    </a>

    <!-- Button zum erneuten Login -->
    <a href="index.php?page=auth&action=login">
        <button class="btn-checkout">Erneut einloggen</button>
    </a>
</main>

<!-- Button zum Hochscrollen der Seite -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php 
// Fügt den Footer-Bereich der Seite ein (z. B. rechtliche Hinweise, Skripte, etc.)
include __DIR__ . '/../layout/footer.php'; 
?>