<!-- Laith -->

<?php 
include __DIR__ . '/../layout/header.php'; 
?>

<main class="content">
    <!-- Überschrift und Einleitungstext für die Kontaktseite -->
    <h2>Kontakt</h2>
    <p>
      Du hast Fragen zu deiner Bestellung, unserem Sortiment oder möchtest einfach Feedback geben? 
      Schreib uns!
    </p> 
    <br>

    <!-- Kontaktformular -->
    <form action="#" method="post"> <!-- Hinweis: action="#" bedeutet keine echte Verarbeitung -->
        <!-- Eingabefeld für den Namen -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br><br>

        <!-- Eingabefeld für die E-Mail-Adresse -->
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <!-- Eingabefeld für die Nachricht (mehrzeiliges Textfeld) -->
        <label for="message">Nachricht:</label>
        <textarea id="message" name="message" rows="6" required></textarea>
        <br><br>

        <!-- Absende-Button -->
        <button type="submit">Absenden</button>
    </form>
</main>

<?php 
include __DIR__ . '/../layout/footer.php'; 
?>