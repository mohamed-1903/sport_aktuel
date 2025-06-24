
<?php include __DIR__ . '/../layout/header.php'; ?>
<section class="welcome">
    <img src="img/logo.png" alt="SportX Logo" class="welcome-logo">
    <h1>Willkommen bei SportX!</h1>
    <p>Dein Shop für alles rund um Fußball.</p>
    <a href="index.php?page=product&action=list" class="cta-button">Jetzt starten</a>
</section>
<?php
$produkte = json_decode(file_get_contents('data/products.json'), true)['products'] ?? [];

function getFirstImage($produkt) {
    if (!empty($produkt['images'])) {
        if (is_array($produkt['images'])) {
            return $produkt['images'][0];
        }
        $decoded = json_decode($produkt['images'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
            return $decoded[0];
        }
    }
    return $produkt['imageMain'] ?? ($produkt['image_main'] ?? 'img/placeholder.jpg');
}

$topProdukte = $produkte;
usort($topProdukte, function($a, $b) {
    return ($b['priceValue'] ?? 0) <=> ($a['priceValue'] ?? 0);
});
$topProdukte = array_slice($topProdukte, 0, 4);
?>
<section class="beliebte">
    <h2>⭐ Beliebteste Produkte</h2>
    <div class="einzelprodukt-grid">
        <?php foreach ($topProdukte as $produkt): ?>
            <div class="produkt">
                <img src="<?= htmlspecialchars(getFirstImage($produkt)) ?>" alt="<?= htmlspecialchars($produkt['name']) ?>">
                <h3><?= htmlspecialchars($produkt['name']) ?></h3>
                <p><?= htmlspecialchars($produkt['price']) ?></p>
                <button type="button" onclick="location.href='index.php?page=product&action=detail&id=<?= (int)$produkt['iid'] ?>'">Zum Produkt</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<section class="produkte">
    <h2>&#128293; Highlights der Woche</h2>
    <div class="einzelprodukt-grid">
        <?php foreach ($produkte as $produkt): ?>
            <div class="produkt">
                <img src="<?= htmlspecialchars(getFirstImage($produkt)) ?>" alt="<?= htmlspecialchars($produkt['name']) ?>">
                <h3><?= htmlspecialchars($produkt['name']) ?></h3>
                <p><?= htmlspecialchars($produkt['price']) ?></p>
                <button type="button" onclick="location.href='index.php?page=product&action=detail&id=<?= (int)$produkt['iid'] ?>'">Zum Produkt</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include __DIR__ . '/../layout/footer.php'; ?>

