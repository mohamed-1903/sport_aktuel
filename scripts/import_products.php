<?php
// import_products.php – Automatischer Import von products.json in die Datenbank, nur bei Änderungen

require_once __DIR__ . '/../model/db.php'; // Datenbankverbindung

define('PRODUCT_JSON', __DIR__ . '/../data/products.json');
define('LAST_IMPORT_FILE', __DIR__ . '/../data/.last_import_time');

if (!file_exists(PRODUCT_JSON)) {
    die("❌ Datei products.json nicht gefunden.");
}

// Prüfen, ob products.json neuer ist als der letzte Import
$currentModified = filemtime(PRODUCT_JSON);
$lastImport = file_exists(LAST_IMPORT_FILE) ? (int)file_get_contents(LAST_IMPORT_FILE) : 0;

if ($currentModified <= $lastImport) {
    return; // Keine Änderung – Import wird übersprungen
}

// JSON-Daten laden
$json = file_get_contents(PRODUCT_JSON);
$data = json_decode($json, true);

if (!$data || !isset($data['products'])) {
    die("❌ Fehler beim Laden oder Parsen von products.json");
}

// Zuerst abhängige Tabellen leeren (z. B. ratings)
$db->exec("DELETE FROM ratings");
$db->exec("DELETE FROM products");

// Vorbereitete SQL-Anweisung
$insertStmt = $db->prepare("
    INSERT INTO products 
        (id, name, description, price, price_text, image_main, marke, farbe, geschlecht, category, subcategory, sizes, images)
    VALUES
        (:id, :name, :description, :price, :price_text, :image_main, :marke, :farbe, :geschlecht, :category, :subcategory, :sizes, :images)
");

// Produkte einfügen
foreach ($data['products'] as $product) {
    $insertStmt->execute([
        ':id'           => $product['iid'],
        ':name'         => $product['name'],
        ':description'  => $product['description'] ?? null,
        ':price'        => $product['priceValue'],
        ':price_text'   => $product['price'],
        ':image_main'   => $product['imageMain'],
        ':marke'        => $product['marke'] ?? null,
        ':farbe'        => $product['farbe'] ?? null,
        ':geschlecht'   => $product['geschlecht'] ?? null,
        ':category'     => $product['category'],
        ':subcategory'  => $product['subcategory'],
        ':sizes'        => json_encode($product['sizes']),
        ':images'       => json_encode($product['images']),
    ]);
}

// Zeitstempel speichern
file_put_contents(LAST_IMPORT_FILE, $currentModified);

echo "✅ Import erfolgreich.";
