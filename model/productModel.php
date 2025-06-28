
 <?php
    // model/productModel.php
    require_once 'model/db.php';


    function mapProductRow(array $row): array
    {
        foreach (['sizes', 'images'] as $field) {
            if (isset($row[$field]) && is_string($row[$field])) {
                $decoded = json_decode($row[$field], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row[$field] = $decoded;
                }
            }
        }

        // Preis synchronisieren, falls priceValue nicht vorhanden ist
        if (!isset($row['priceValue']) && isset($row['price'])) {
            $row['priceValue'] = $row['price'];
        }

        return $row;
    }


    function getAllProducts(): array
    {
        global $db;
        $stmt = $db->query("SELECT * FROM products");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map('mapProductRow', $rows);
    }

    function getProductById($id): ?array
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ? mapProductRow($product) : null;
    }

    function getProductsByCategory($category): array
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM products WHERE category = ?");

        $stmt->execute([$category]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map('mapProductRow', $rows);
    }

    function getProductsByCategoryAndSub($category, $subcategory): array
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND subcategory = ?");

        $stmt->execute([$category, $subcategory]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map('mapProductRow', $rows);
    }

function addProduct(array $product): bool
{
    global $db;
        $stmt = $db->prepare("INSERT INTO products (name, price, category, subcategory, description, image_Main, sizes, marke, farbe, geschlecht)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $sizes = json_encode($product['sizes']);

        return $stmt->execute([
            $product['name'],
            $product['price'],
            $product['category'],
            $product['subcategory'],
            $product['description'],
            $product['imageMain'],
            $sizes,
            $product['marke'],
            $product['farbe'],
        $product['geschlecht']
    ]);
}

function updateProductDiscount(int $productId, int $discount): bool
{
    global $db;
    $stmt = $db->prepare('UPDATE products SET discount = ? WHERE id = ?');
    return $stmt->execute([$discount, $productId]);
}
