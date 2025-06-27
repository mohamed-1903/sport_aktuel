<?php include __DIR__ . '/../layout/header.php'; ?>

<?php /* impressumView.php */ ?>
<section class="impressum-page">
  <div class="impressum-container">
    <h1>Impressum</h1>

    <p class="intro">
      Angaben gemäß § 5 TMG (Telemediengesetz).
    </p>

    <h2>Angaben zum Anbieter</h2>
    <p>
      SportX GmbH<br>
      Musterstraße 1<br>
      12345 Berlin<br>
      Deutschland
    </p>

    <h2>Vertreten durch</h2>
    <p>Hussein Alsumat (Geschäftsführer)<br>Laith Almasri: (Teamleiter)<br>Mohamed Raouf: (Anwendungsentwickler)</p>

    </p>

    <h2>Kontakt</h2>
    <p>
      Telefon: +49 (0)30 123456789<br>
      E-Mail: <a href="mailto:info@sportx.de">info@sportx.de</a>
    </p>

    <h2>Registereintrag</h2>
    <p>
      Eintragung im Handelsregister.<br>
      Registergericht: Amtsgericht Berlin-Charlottenburg<br>
      Registernummer: HRB 123456
    </p>

    <h2>Umsatzsteuer-ID</h2>
    <p>Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz: DE123456789</p>

    <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
    <p>
      Max Mustermann<br>
      Musterstraße 1<br>
      12345 Berlin
    </p>

    <h2>Haftungsausschluss</h2>
    <p>Trotz sorgfältiger inhaltlicher Kontrolle übernehmen wir keine Haftung für die Inhalte externer Links. Für den Inhalt verlinkter Seiten sind ausschließlich deren Betreiber verantwortlich.</p>

    <h2>Online-Streitbeilegung</h2>
    <p>Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a href="https://ec.europa.eu/consumers/odr">https://ec.europa.eu/consumers/odr</a></p>
  </div>
</section>

<style>
.impressum-page {
  max-width: 800px;
  margin: 60px auto;
  padding: 20px;
  font-family: 'Montserrat', sans-serif;
  color: var(--text-color);
}

.impressum-container h1 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: var(--accent-color);
}

.impressum-container h2 {
  font-size: 1.3rem;
  margin-top: 30px;
  margin-bottom: 10px;
}

.impressum-container p {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--text-color);
}

.impressum-container .intro {
  margin-bottom: 30px;
  font-style: italic;
  color: var(--text-muted, #777);
}
</style>


<?php include __DIR__ . '/../layout/footer.php'; ?>
