<!-- Laith -->
<?php 
include 'view/layout/header.php'; 
?>

<?php /* faqView.php */ ?>
<section class="faq-page">
    <div class="faq-container">
        <!-- Seitentitel -->
        <h1>Häufige Fragen (FAQ)</h1>

        <!-- FAQ-Eintrag 1 -->
        <div class="faq-item">
            <button class="faq-question">Wie kann ich eine Bestellung aufgeben?</button>
            <div class="faq-answer">
                <p>Lege einfach deine gewünschten Produkte in den Warenkorb und folge dem Bestellprozess. Du erhältst eine Bestätigung per E-Mail.</p>
            </div>
        </div>

        <!-- FAQ-Eintrag 2 -->
        <div class="faq-item">
            <button class="faq-question">Wie lange dauert der Versand?</button>
            <div class="faq-answer">
                <p>Die Lieferzeit innerhalb Deutschlands beträgt 2-4 Werktage. Wir versenden mit DHL und DPD.</p>
            </div>
        </div>

        <!-- FAQ-Eintrag 3 -->
        <div class="faq-item">
            <button class="faq-question">Kann ich meine Bestellung ändern oder stornieren?</button>
            <div class="faq-answer">
                <p>Solange deine Bestellung noch nicht versendet wurde, können wir Änderungen vornehmen. Kontaktiere uns schnellstmöglich über unser <a href="index.php?page=kontakt">Kontaktformular</a>.</p>
            </div>
        </div>

        <!-- FAQ-Eintrag 4 -->
        <div class="faq-item">
            <button class="faq-question">Erhalte ich eine Sendungsverfolgung?</button>
            <div class="faq-answer">
                <p>Ja, sobald deine Bestellung versendet wurde, erhältst du per E-Mail einen Link zur Sendungsverfolgung.</p>
            </div>
        </div>

        <!-- FAQ-Eintrag 5 -->
        <div class="faq-item">
            <button class="faq-question">Was passiert, wenn ein Artikel nicht verfügbar ist?</button>
            <div class="faq-answer">
                <p>Wenn ein Artikel ausverkauft ist, informieren wir dich direkt nach deiner Bestellung und bieten Alternativen oder eine Rückerstattung an.</p>
            </div>
        </div>

        <!-- FAQ-Eintrag 6 -->
        <div class="faq-item">
            <button class="faq-question">Wie kann ich mein Kundenkonto löschen?</button>
            <div class="faq-answer">
                <p>Kontaktiere unseren Support unter <a href="mailto:support@sportx.de">support@sportx.de</a> mit dem Betreff „Konto löschen“. Wir bearbeiten deine Anfrage umgehend.</p>
            </div>
        </div>

    </div> <!-- Ende .faq-container -->
</section>

<!-- Eingebettetes CSS für FAQ-Seite -->
<style>
    .faq-page {
        max-width: 800px;
        margin: 60px auto;
        padding: 20px;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-color);
    }

    .faq-container h1 {
        font-size: 2rem;
        margin-bottom: 30px;
        color: var(--accent-color);
        text-align: center;
    }

    .faq-item {
        margin-bottom: 20px;
        border-bottom: 1px solid #ccc;
        padding: 10px 0;
    }

    .faq-question {
        width: 100%;
        background: none;
        border: none;
        text-align: left;
        font-size: 1.1rem;
        font-weight: bold;
        padding: 15px 0;
        cursor: pointer;
        color: var(--text-color);
    }

    .faq-answer {
        display: none; /* standardmäßig versteckt */
        padding-bottom: 15px;
        color: var(--text-color);
    }

    .faq-answer p {
        margin: 0;
        line-height: 1.6;
        text-align: left;
        word-break: break-word;
    }

    .faq-answer a {
        color: var(--accent-color);
        word-break: break-word;
    }

    .faq-item.active .faq-answer {
        display: block; /* sichtbar, wenn aktiv */
    }
</style>

<!-- JavaScript: Klappt bei Klick die jeweilige Antwort auf oder zu -->
<script>
    document.querySelectorAll(".faq-question").forEach(button => {
        button.addEventListener("click", () => {
            const item = button.parentElement;
            item.classList.toggle("active");
        });
    });
</script>

<?php 
include 'view/layout/footer.php'; 
?>