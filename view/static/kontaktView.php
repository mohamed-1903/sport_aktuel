<?php include 'view/layout/header.php'; ?>

<main class="content">
    <h2>Kontakt</h2>
    <p>Du hast Fragen zu deiner Bestellung, unserem Sortiment oder möchtest einfach Feedback geben? Schreib uns!</p>

    <form action="#" method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">E-Mail:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="message">Nachricht:</label><br>
        <textarea id="message" name="message" rows="6" required></textarea><br><br>

        <button type="submit">Absenden</button>
    </form>
</main>

<?php include 'view/layout/footer.php'; ?>
