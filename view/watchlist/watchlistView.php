<?php 
//Hussein
// Fügt die Header-Datei ein (vermutlich HTML <head>, Navigation usw.)
include __DIR__ . '/../layout/header.php'; 
?>

<div class="produkte">
    <h1>🧡 Deine Merkliste</h1>

    <!-- Container für die Merkliste im Grid-Layout -->
    <div id="watchlist-container" class="einzelprodukt-grid">

        <?php if (empty($watchlistItems)): ?>
            <!-- Falls keine Produkte in der Merkliste sind, wird eine Nachricht angezeigt -->
            <p style="color: gray;">🤍 Noch keine Produkte auf deiner Merkliste.</p>
        <?php else: ?>
            <!-- Falls Produkte vorhanden sind, wird über jedes Produkt iteriert -->
            <?php foreach ($watchlistItems as $item): ?>

                <!-- Einzelne Produktkarte mit Produkt-ID als data-Attribut -->
                <div class="watchlist-card" data-id="<?= (int)$item['product_id'] ?>">

                    <!-- Bildbereich des Produkts -->
                    <div class="image-wrapper">
                        <!-- Produktbild mit HTML-Escaping zur Sicherheit -->
                        <img src="<?= htmlspecialchars($item['image_main']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    </div>

                    <!-- Produktinformationen: Name, Preis, Aktionen -->
                    <div class="produkt-info">
                        <!-- Produktname sicher ausgegeben -->
                        <h3><?= htmlspecialchars($item['name']) ?></h3>

                        <!-- Preis formatiert mit 2 Nachkommastellen, Komma als Dezimaltrennzeichen -->
                        <p><?= number_format($item['price'], 2, ',', '.') ?> €</p>

                        <!-- Formular zum Entfernen des Produkts aus der Merkliste -->
                        <form action="index.php?page=watchlist&action=remove" method="post" style="display:inline;">
                            <!-- Produkt-ID als verstecktes Feld -->
                            <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                            <button type="submit">🗑️ Entfernen</button>
                        </form>

                        <!-- Link zur Detailseite des Produkts -->
                        <a href="index.php?page=product&action=detail&id=<?= (int)$item['product_id'] ?>">
                            <button>🔍 Anzeigen</button>
                        </a>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Button zum Leeren der gesamten Merkliste -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php?page=watchlist&action=clear" class="btn-delete-all">🧹 Alle löschen</a>
    </div>
</div>

<!-- Button zum Hochscrollen der Seite -->
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<?php 
// Fügt die Footer-Datei ein (vermutlich schließt HTML-Tags und lädt Skripte)
include __DIR__ . '/../layout/footer.php'; 
?>