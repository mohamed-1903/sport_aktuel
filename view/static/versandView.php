
<?php include 'view/layout/header.php'; ?>

<?php /* versandView.php */ ?>
<section class="versand-page">
  <div class="versand-container">
    <h1>Versand & Lieferung</h1>

    <p>Wir liefern deine Bestellung schnell und zuverlässig direkt zu dir nach Hause. Hier findest du alle Informationen rund um unsere Versandoptionen, Lieferzeiten und Versandkosten.</p>

    <h2>Versanddienstleister</h2>
    <p>Unsere Produkte werden mit unseren zuverlässigen Partnern <strong>DHL</strong> und <strong>DPD</strong> versendet.</p>

    <h2>Lieferzeiten</h2>
    <ul>
      <strong>Deutschland:</strong> 2–4 Werktage<br>
      <strong>EU-Ausland:</strong> 4–7 Werktage<br>
      <strong>Internationale Lieferung:</strong> bis zu 14 Werktage<br>
    </ul>

    <h2>Versandkosten</h2>
    <ul>
      <strong>Deutschland:</strong> 4,95 € (ab 50 € Bestellwert kostenlos)<br>
      <strong>EU-Ausland:</strong> 9,95 €<br>
      <strong>Weltweit:</strong> 19,95 €
    </ul>

    <h2>Sendungsverfolgung</h2>
    <p>Sobald dein Paket unser Lager verlässt, erhältst du eine E-Mail mit deiner Sendungsnummer zur Verfolgung deiner Bestellung.</p>

    <h2>Lieferadresse</h2>
    <p>Bitte stelle sicher, dass deine Adresse vollständig und korrekt angegeben ist, um eine reibungslose Lieferung zu gewährleisten.</p>
  </div>
</section>

<style>
.versand-page {
  max-width: 800px;
  margin: 60px auto;
  padding: 20px;
  font-family: 'Montserrat', sans-serif;
  color: var(--text-color);
}

.versand-container h1 {
  font-size: 2rem;
  margin-bottom: 20px;
  color: var(--accent-color);
}

.versand-container h2 {
  font-size: 1.3rem;
  margin-top: 30px;
  margin-bottom: 10px;
}

.versand-container p,
.versand-container ul {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--text-color);
}

.versand-container ul {
  padding-left: 20px;
}
</style>


<?php include 'view/layout/footer.php'; ?>