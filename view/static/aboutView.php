<!-- Laith -->
<?php 
// Binde den allgemeinen Seiten-Header ein (z. B. Navigation, Head, Meta etc.)
include __DIR__ . '/../layout/header.php'; 
?>

<?php /* ueberUnsView.php */ ?>
<section class="ueberuns-page">
  <div class="ueberuns-container">
    <!-- Hauptüberschrift der Seite -->
    <h1>Über uns</h1>

    <!-- Einleitung mit Mission und Positionierung -->
    <p class="intro">
      SportX steht für Leidenschaft, Fairness und Teamgeist. Wir sind mehr als nur ein Sportshop - 
      wir sind eine Community für Fußballfans und Sportbegeisterte.
    </p>

    <!-- Abschnitt: Mission -->
    <h2>Unsere Mission</h2>
    <p>
      Unsere Vision ist es, Sport für alle zugänglich zu machen - egal ob Hobbykicker oder Profisportler. 
      Wir bieten hochwertige Produkte, exzellenten Service und faire Preise. 
      Dabei setzen wir auf Nachhaltigkeit, moderne Technologien und ein starkes Miteinander.
    </p>

    <!-- Abschnitt: Stärken des Unternehmens -->
    <h2>Was uns auszeichnet</h2>
    <ul>
      <li>Leidenschaft für Sport und Bewegung</li>
      <li>Top-Auswahl an Sportartikeln führender Marken</li>
      <li>Faire Preise & transparente Angebote</li>
      <li>Kundenservice auf Augenhöhe</li>
      <li>Fokus auf Nachhaltigkeit und soziale Verantwortung</li>
    </ul>

    <!-- Abschnitt: Das Team -->
    <h2>Das Team hinter SportX</h2>
    <p>
      Unser interdisziplinäres Team besteht aus jungen kreativen Köpfen, Sportlern, Entwicklern und Beratern. 
      Gemeinsam arbeiten wir daran, SportX zur ersten Adresse für Sportbedarf im Netz zu machen.
    </p>

    <!-- Abschnitt: Kontaktmöglichkeit -->
    <h2>Du hast Fragen?</h2>
    <p>
      Wir freuen uns über dein Feedback und deine Ideen. Schreibe uns gerne an 
      <a href="mailto:info@sportx.de">info@sportx.de</a> oder besuche unsere 
      <a href="index.php?page=static&action=kontakt">Kontaktseite</a>.
    </p>
  </div>
</section>

<!-- Eingebettetes CSS für die „Über uns“-Seite -->
<style>
.ueberuns-page {
  max-width: 800px;                         /* Breite begrenzen */
  margin: 60px auto;                        /* zentrierte Positionierung */
  padding: 20px;
  font-family: 'Montserrat', sans-serif;   /* einheitliche Schrift */
  color: var(--text-color);                /* Farbvariable */
}

.ueberuns-container h1 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: var(--accent-color);              /* Akzentfarbe für Hauptüberschrift */
}

.ueberuns-container h2 {
  font-size: 1.3rem;
  margin-top: 30px;
  margin-bottom: 10px;
}

.ueberuns-container p,
.ueberuns-container ul {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--text-color);
}

/* Liste im Stärken-Abschnitt */
.ueberuns-container ul {
  padding-left: 20px;
  list-style: none;                        /* Entferne Standard-Aufzählung */
}

/* Häkchen-Icon vor jedem Listenpunkt */
.ueberuns-container ul li::before {
  content: "✔ ";
  color: var(--accent-color);              /* Häkchen in Akzentfarbe */
  margin-right: 5px;
}

/* Formatierung für Einleitungstext */
.ueberuns-container .intro {
  margin-bottom: 30px;
  font-style: italic;
  color: var(--text-muted, #777);          /* Optionaler Fallback */
}
</style>

<?php 
// Binde den Seiten-Footer ein (z. B. Scripts, schließende Tags etc.)
include __DIR__ . '/../layout/footer.php'; 
?>
