<?php
require_once '../model/db.php'; // sicherstellen, dass $db (PDO) vorhanden ist

$json = file_get_contents('../data/products.json');
$data = json_decode($json, true);

if (!$data || !isset($data['products'])) {
    die("❌ Fehler beim Laden oder Parsen von products.json");
}

$insertStmt = $db->prepare("
    INSERT INTO products 
        (id, name, description, price, price_text, image_main, marke, farbe, geschlecht, category, subcategory, sizes, images)
    VALUES
        (:id, :name, :description, :price, :price_text, :image_main, :marke, :farbe, :geschlecht, :category, :subcategory, :sizes, :images)
");
// Erst Ratings entfernen, um Foreign-Key-Fehler zu vermeiden
$db->exec("DELETE FROM ratings");
$db->exec("DELETE FROM products");

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


echo "✅ Import abgeschlossen.";
