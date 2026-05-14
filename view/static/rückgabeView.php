<!-- Laith -->
<?php 
include 'view/layout/header.php'; 
?>

<?php /* rueckgabeView.php */ ?>
<section class="rueckgabe-page">
  <div class="rueckgabe-container">
    <!-- Hauptüberschrift -->
    <h1>Rückgabe</h1>

    <!-- Einführungstext zur Rückgabe -->
    <p>
      Deine Zufriedenheit ist uns wichtig. Falls du mit einem Produkt nicht zufrieden bist 
      oder es nicht deinen Erwartungen entspricht, kannst du es selbstverständlich zurückgeben.
    </p>

    <!-- Rückgabefrist erklärt -->
    <h2>Rückgabefrist</h2>
    <p>
      Du hast das Recht, Artikel innerhalb von <strong>14 Tagen</strong> nach Erhalt ohne 
      Angabe von Gründen zurückzugeben.
    </p>

    <!-- Voraussetzungen für Rückgaben -->
    <h2>Voraussetzungen für die Rückgabe</h2>
    <ul>
      <li>Der Artikel muss ungetragen, ungewaschen und im Originalzustand sein.</li>
      <li>Originalverpackung und Etiketten sollten möglichst mitgeschickt werden.</li>
      <li>Individuell personalisierte Artikel sind vom Umtausch ausgeschlossen.</li>
    </ul>

    <!-- Rücksendeprozess Schritt für Schritt erklärt -->
    <h2>So funktioniert die Rücksendung</h2>
    <p>
      1. Sende eine kurze E-Mail mit deiner Bestellnummer und dem Rückgabegrund an 
      <a href="mailto:support@sportx.de">support@sportx.de</a>.
    </p>
    <p>
      2. Du erhältst von uns die Rücksendeadresse und weitere Hinweise zur Rücksendung.
    </p>
    <p>
      3. Verpacke die Artikel sicher und sende sie frankiert an uns zurück.
    </p>

    <!-- Infos zur Rückerstattung -->
    <h2>Rückerstattung</h2>
    <p>
      Sobald deine Rücksendung bei uns eingegangen und geprüft wurde, erstatten wir dir 
      den Kaufbetrag innerhalb von <strong>5-7 Werktagen</strong> über die ursprünglich 
      gewählte Zahlungsmethode.
    </p>

    <!-- Kontaktmöglichkeit bei Fragen -->
    <h2>Fragen?</h2>
    <p>
      Unser Kundenservice hilft dir gerne weiter. Du erreichst uns unter 
      <a href="mailto:support@sportx.de">support@sportx.de</a>.
    </p>
  </div>
</section>

<!-- Eingebettetes CSS zur Gestaltung der Rückgabe-Seite -->
<style>
.rueckgabe-page {
  max-width: 800px;                  /* Maximale Breite */
  margin: 60px auto;                 /* Zentrierung mit Abstand */
  padding: 20px;
  font-family: 'Montserrat', sans-serif;
  color: var(--text-color);         /* Textfarbe aus CSS-Variable */
}

.rueckgabe-container h1 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: var(--accent-color);       /* Akzentfarbe aus CSS-Variable */
}

.rueckgabe-container h2 {
  font-size: 1.3rem;
  margin-top: 30px;
  margin-bottom: 10px;
}

.rueckgabe-container p,
.rueckgabe-container ul {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--text-color);
}

.rueckgabe-container ul {
  padding-left: 20px;               /* Einzug für Listenpunkte */
}
</style>

<?php 
include 'view/layout/footer.php'; 
?>