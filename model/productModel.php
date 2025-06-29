
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

    $stmt = $db->prepare(
        "INSERT INTO products (name, price, category, subcategory, description, image_main, sizes, marke, farbe, geschlecht)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

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

function deleteProduct(int $productId): bool
{
    global $db;
    $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
    return $stmt->execute([$productId]);
}

function computeSimilarProducts(array $product, array $allProducts, int $limit = 2): array
{
    $id          = $product['id'] ?? 0;
    $category    = strtolower(trim($product['category'] ?? ''));
    $subcategory = strtolower(trim($product['subcategory'] ?? ''));
    $brand       = strtolower(trim($product['marke'] ?? ''));

    $others = array_filter($allProducts, static function ($p) use ($id) {
        return ($p['id'] ?? null) != $id;
    });

    $steps = [
        static function ($p) use ($category, $subcategory, $brand) {
            return strtolower($p['category'] ?? '') === $category
                && strtolower($p['subcategory'] ?? '') === $subcategory
                && strtolower($p['marke'] ?? '') === $brand;
        },
        static function ($p) use ($category, $subcategory) {
            return strtolower($p['category'] ?? '') === $category
                && strtolower($p['subcategory'] ?? '') === $subcategory;
        },
        static function ($p) use ($category, $brand) {
            return strtolower($p['category'] ?? '') === $category
                && strtolower($p['marke'] ?? '') === $brand;
        },
        static function ($p) use ($category) {
            return strtolower($p['category'] ?? '') === $category;
        },
        static function ($p) use ($brand) {
            return strtolower($p['marke'] ?? '') === $brand;
        },
    ];

    $result = [];
    $seen   = [];

    foreach ($steps as $filter) {
        foreach ($others as $op) {
            if (!isset($seen[$op['id']]) && $filter($op)) {
                $result[]          = $op;
                $seen[$op['id']] = true;
                if (count($result) >= $limit) {
                    break 2;
                }
            }
        }
    }

    if (count($result) < $limit) {
        $remaining = array_values(array_filter($others, static function ($p) use ($seen) {
            return !isset($seen[$p['id']]);
        }));
        shuffle($remaining);
        $result = array_merge($result, array_slice($remaining, 0, $limit - count($result)));
    }

    return array_slice($result, 0, $limit);
}
