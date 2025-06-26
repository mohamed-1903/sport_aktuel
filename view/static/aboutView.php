<?php include __DIR__ . '/../layout/header.php'; ?>

<?php /* ueberUnsView.php */ ?>
<section class="ueberuns-page">
  <div class="ueberuns-container">
    <h1>Über uns</h1>

    <p class="intro">
      SportX steht für Leidenschaft, Fairness und Teamgeist. Wir sind mehr als nur ein Sportshop – wir sind eine Community für Fußballfans und Sportbegeisterte.
    </p>

    <h2>Unsere Mission</h2>
    <p>
      Unsere Vision ist es, Sport für alle zugänglich zu machen – egal ob Hobbykicker oder Profisportler. Wir bieten hochwertige Produkte, exzellenten Service und faire Preise. Dabei setzen wir auf Nachhaltigkeit, moderne Technologien und ein starkes Miteinander.
    </p>

    <h2>Was uns auszeichnet</h2>
    <ul>
      <li>Leidenschaft für Sport und Bewegung</li>
      <li>Top-Auswahl an Sportartikeln führender Marken</li>
      <li>Faire Preise & transparente Angebote</li>
      <li>Kundenservice auf Augenhöhe</li>
      <li>Fokus auf Nachhaltigkeit und soziale Verantwortung</li>
    </ul>

    <h2>Das Team hinter SportX</h2>
    <p>
      Unser interdisziplinäres Team besteht aus jungen kreativen Köpfen, Sportlern, Entwicklern und Beratern. Gemeinsam arbeiten wir daran, SportX zur ersten Adresse für Sportbedarf im Netz zu machen.
    </p>

    <h2>Du hast Fragen?</h2>
    <p>
      Wir freuen uns über dein Feedback und deine Ideen. Schreibe uns gerne an <a href="mailto:info@sportx.de">info@sportx.de</a> oder besuche unsere <a href="index.php?page=static&action=kontakt">Kontaktseite</a>.
    </p>
  </div>
</section>

<style>
.ueberuns-page {
  max-width: 800px;
  margin: 60px auto;
  padding: 20px;
  font-family: 'Montserrat', sans-serif;
  color: var(--text-color);
}

.ueberuns-container h1 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: var(--accent-color);
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

.ueberuns-container ul {
  padding-left: 20px;
  list-style: none;
}

.ueberuns-container ul li::before {
  content: "✔ ";
  color: var(--accent-color);
  margin-right: 5px;
}

.ueberuns-container .intro {
  margin-bottom: 30px;
  font-style: italic;
  color: var(--text-muted, #777);
}
</style>


<?php include __DIR__ . '/../layout/footer.php'; ?>
