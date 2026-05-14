<!-- Laith -->
<?php 
include __DIR__ . '/../layout/header.php'; 
?>

<?php /* datenschutzView.php */ ?>
<section class="datenschutz-page">
  <div class="datenschutz-container">
    <!-- Hauptüberschrift -->
    <h1>Datenschutzerklärung</h1>

    <!-- Einleitung zur DSGVO-Rechtsgrundlage -->
    <p>
      Der Schutz deiner persönlichen Daten ist uns ein wichtiges Anliegen. 
      Wir verarbeiten deine Daten ausschließlich auf Grundlage der gesetzlichen Bestimmungen (DSGVO, TMG).
    </p>

    <!-- Abschnitt 1: Verantwortlicher für die Datenverarbeitung -->
    <h2>1. Verantwortlicher</h2>
    <p>
      SportX GmbH<br>
      Musterstraße 1<br>
      12345 Berlin<br>
      E-Mail: <a href="mailto:datenschutz@sportx.de">datenschutz@sportx.de</a>
    </p>

    <!-- Abschnitt 2: Zweck der Datenverarbeitung -->
    <h2>2. Erhebung und Verarbeitung personenbezogener Daten</h2>
    <p>
      Wir erheben, verarbeiten und nutzen personenbezogene Daten nur, soweit sie für die Begründung, 
      inhaltliche Ausgestaltung oder Änderung des Vertragsverhältnisses erforderlich sind.
    </p>

    <!-- Abschnitt 3: Weitergabe an Dritte -->
    <h2>3. Datenweitergabe</h2>
    <p>
      Deine Daten werden ohne deine ausdrückliche Zustimmung nicht an Dritte weitergegeben – 
      ausgenommen sind unsere Dienstleister im Rahmen der Bestellabwicklung (z. B. Zahlungsanbieter, Versanddienstleister).
    </p>

    <!-- Abschnitt 4: Verwendung von Cookies -->
    <h2>4. Cookies</h2>
    <p>
      Unsere Website verwendet Cookies, um dir ein besseres Nutzererlebnis zu bieten. 
      Du kannst die Verwendung von Cookies in deinem Browser deaktivieren.
    </p>

    <!-- Abschnitt 5: Nutzung der Daten für den Newsletter -->
    <h2>5. Newsletter</h2>
    <p>
      Wenn du dich für unseren Newsletter anmeldest, verwenden wir deine E-Mail-Adresse ausschließlich für diesen Zweck. 
      Du kannst dich jederzeit über den Abmeldelink im Newsletter abmelden.
    </p>

    <!-- Abschnitt 6: Rechte der betroffenen Person -->
    <h2>6. Deine Rechte</h2>
    <ul>
      Auskunft über deine gespeicherten Daten<br>
      Berichtigung unrichtiger Daten<br>
      Löschung oder Einschränkung der Verarbeitung<br>
      Widerspruch gegen die Verarbeitung<br>
      Datenübertragbarkeit
    </ul>

    <p>
      Bitte sende deine Anfrage schriftlich an 
      <a href="mailto:datenschutz@sportx.de">datenschutz@sportx.de</a>.
    </p>

    <!-- Abschnitt 7: Aufsichtsbehörde -->
    <h2>7. Kontakt zur Aufsichtsbehörde</h2>
    <p>
      Du hast das Recht, dich bei einer Datenschutzaufsichtsbehörde 
      über die Verarbeitung deiner personenbezogenen Daten zu beschweren.
    </p>
  </div>
</section>

<!-- Eingebettetes CSS für die Datenschutzerklärungsseite -->
<style>
.datenschutz-page {
  max-width: 800px;                       /* maximale Breite */
  margin: 60px auto;                      /* zentrierte Position */
  padding: 20px;
  font-family: 'Montserrat', sans-serif; /* einheitliche Schriftart */
  color: var(--text-color);              /* dynamische Textfarbe */
}

.datenschutz-container h1 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: var(--accent-color);            /* Akzentfarbe für Titel */
}

.datenschutz-container h2 {
  font-size: 1.3rem;
  margin-top: 30px;
  margin-bottom: 10px;
}

.datenschutz-container p,
.datenschutz-container ul {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--text-color);
}

.datenschutz-container ul {
  padding-left: 20px;                    /* Einzug für Liste */
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>
