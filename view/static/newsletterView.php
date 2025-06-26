
<?php include __DIR__ . '/../layout/header.php'; ?>
<?php /* newsletterView.php */ ?>
<section class="newsletter-page">
  <div class="newsletter-container">
    <h1>Newsletter abonnieren</h1>
    <p>Melde dich jetzt zu unserem Newsletter an und bleibe informiert über exklusive Angebote, Produktneuheiten und aktuelle Aktionen bei SportX!</p>

    <form class="newsletter-form" method="post" action="index.php?page=newsletter&action=subscribe">
      <label for="newsletter-name">Dein Name</label>
      <input type="text" id="newsletter-name" name="name" required placeholder="Max Mustermann">

      <label for="newsletter-email">E-Mail-Adresse</label>
      <input type="email" id="newsletter-email" name="email" required placeholder="max@example.de">

      <label class="checkbox-label">
        <input type="checkbox" name="privacy" required>
        Ich habe die <a href="index.php?page=static&action=datenschutz">Datenschutzerklärung</a> gelesen und stimme zu.
      </label>

      <button type="submit" class="newsletter-button">Jetzt abonnieren</button>
    </form>

    <p class="disclaimer">Du kannst dich jederzeit mit einem Klick wieder abmelden. Wir versenden keinen Spam.</p>
  </div>
</section>

<style>
.newsletter-page {
  max-width: 600px;
  margin: 60px auto;
  padding: 20px;
  font-family: 'Montserrat', sans-serif;
  color: var(--text-color);
  background-color: var(--card-bg);
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.newsletter-container h1 {
  font-size: 2rem;
  margin-bottom: 10px;
  color: var(--accent-color);
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 20px;
}

.newsletter-form input[type="text"],
.newsletter-form input[type="email"] {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
  background-color: var(--input-bg);
  color: var(--text-color);
}

.newsletter-button {
  background-color: var(--accent-color);
  color: #000;
  padding: 12px;
  font-weight: bold;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.newsletter-button:hover {
  background-color: #e6c200;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
}

.disclaimer {
  font-size: 0.8rem;
  color: #888;
  margin-top: 20px;
  text-align: center;
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>