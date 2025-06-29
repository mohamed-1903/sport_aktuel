<?php
// import_products.php – Automatischer Import von products.json in die Datenbank,
// nur wenn sich die JSON-Datei geändert hat.
// Stellt die Verbindung zur Datenbank her

require_once __DIR__ . '/../model/db.php'; // Datenbankverbindung
// Prüft, ob die Spalte 'discount' existiert; legt sie andernfalls an

try {
    $db->query("SELECT discount FROM products LIMIT 1");
} catch (PDOException $e) {
    // Spalte anlegen, falls sie fehlt
    $db->exec("ALTER TABLE products ADD COLUMN discount INT DEFAULT 0");
}
// Pfade für die Produktdatei und den Zeitstempel der letzten Aktualisierung

define('PRODUCT_JSON', __DIR__ . '/../data/products.json');
define('LAST_IMPORT_FILE', __DIR__ . '/../data/.last_import_time');

// Abbruch, falls die JSON-Datei nicht vorhanden ist
if (!file_exists(PRODUCT_JSON)) {
    die("❌ Datei products.json nicht gefunden.");
}
// Zeitstempel der Datei und des letzten Imports vergleichen

// Prüfen, ob products.json neuer ist als der letzte Import
$currentModified = filemtime(PRODUCT_JSON);
$lastImport = file_exists(LAST_IMPORT_FILE) ? (int)file_get_contents(LAST_IMPORT_FILE) : 0;
// Wenn keine Änderung vorliegt, Import beenden

if ($currentModified <= $lastImport) {
    return; // Keine Änderung – Import wird übersprungen
}

// products.json einlesen und in ein Array umwandeln
$json = file_get_contents(PRODUCT_JSON);
$data = json_decode($json, true);

// Abbruch, wenn das JSON-Format unerwartet ist
if (!$data || !isset($data['products'])) {
    die("❌ Fehler beim Laden oder Parsen von products.json");
}

// Vorbereitende Reinigungen: abhängige Tabellen zurücksetzen
$db->exec("DELETE FROM ratings");
$db->exec("DELETE FROM products");

// SQL-Vorlage für das Einfügen neuer Produkte vorbereiten
$insertStmt = $db->prepare("
    INSERT INTO products
        (id, name, description, price, price_text, image_main, marke, farbe, geschlecht, category, subcategory, sizes, images, discount)
    VALUES
        (:id, :name, :description, :price, :price_text, :image_main, :marke, :farbe, :geschlecht, :category, :subcategory, :sizes, :images, :discount)

");

// Produkte aus dem Array in die Datenbank schreiben
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
        ':discount'     => $product['discount'] ?? 0,
    ]);

}

// Zeitstempel speichern
file_put_contents(LAST_IMPORT_FILE, $currentModified);
// Redundanzprüfung: keine doppelten Schritte festgestellt
