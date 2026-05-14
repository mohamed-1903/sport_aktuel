<!-- Laith -->
<?php 
include 'view/layout/header.php'; 
?>

<!-- Startseite: Willkommensbereich mit Hintergrundbild -->
<section class="welcome" style="background-image: url('img/hintergrund.png');">
    <!-- Overlay für dunklere Darstellung des Hintergrunds -->
    <div class="welcome-overlay"></div>

    <!-- Zentrale Begrüßung und Call-to-Action -->
    <div class="welcome-content">
        <img src="img/logo.png" alt="SportX Logo" class="welcome-logo">
        <h1>Willkommen bei SportX!</h1>
        <p>Dein Shop für alles rund um Fußball.</p>
        <!-- Button führt zur Produktliste -->
        <a href="index.php?page=product&action=productListView" class="cta-button">Jetzt starten</a>
    </div>
</section>

<?php
// Lade die Produktdaten aus einer JSON-Datei
$produkte = json_decode(file_get_contents('data/products.json'), true)['products'] ?? [];

// Hilfsfunktion: Liefert das erste verfügbare Bild für ein Produkt
function getFirstImage($produkt)
{
    if (!empty($produkt['images'])) {
        if (is_array($produkt['images'])) {
            return $produkt['images'][0]; // Direktes Array
        }
        $decoded = json_decode($produkt['images'], true); // Versuch, JSON zu dekodieren
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
            return $decoded[0]; // Falls gültiges JSON
        }
    }
    // Fallback: Nutze andere Felder oder Placeholder
    return $produkt['imageMain'] ?? ($produkt['image_main'] ?? 'img/placeholder.jpg');
}

// Sortiere Produkte nach Preiswert absteigend (Top-Produkte)
$topProdukte = $produkte;
usort($topProdukte, function ($a, $b) {
    return ($b['priceValue'] ?? 0) <=> ($a['priceValue'] ?? 0);
});
$topProdukte = array_slice($topProdukte, 0, 4); // Top 4 Produkte
?>

<!-- Abschnitt: Highlight-Produkte -->
<section class="produkte">
    <h2>&#128293; Highlights der Woche</h2>
    <div class="einzelprodukt-grid">
        <!-- Zeige die ersten 5 Produkte aus der Liste -->
        <?php foreach (array_slice($produkte, 0, 5) as $produkt): ?>
            <div class="produkt">
                <!-- Produktbild mit HTML-Escaping -->
                <img src="<?= htmlspecialchars($produkt["imageMain"]) ?>" alt="<?= htmlspecialchars($produkt["name"]) ?>">

                <!-- Produktname -->
                <h3><?= htmlspecialchars($produkt["name"]) ?></h3>

                <!-- Preis -->
                <p><?= htmlspecialchars($produkt["price"]) ?></p>

                <!-- Button zum jeweiligen Produkt-Detail -->
                <button type="button" onclick="location.href='index.php?page=product&action=detail&id=<?= (int)$produkt['iid'] ?>'">
                    Zum Produkt
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Button zum Zurückscrollen -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php 
include __DIR__ . '/../layout/footer.php'; 
?>