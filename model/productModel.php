
 <?php
    // model/productModel.php
    require_once 'model/db.php';

    // Fallback: Produkte aus JSON laden, wenn keine Datenbankverbindung besteht
    function loadProductsFromJson(): array
    {
        static $products = null;
        if ($products !== null) {
            return $products;
        }

        $file = __DIR__ . '/../data/products.json';
        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['products'])) {
            return [];
        }

        $products = array_map('mapProductRow', $data['products']);
        return $products;
    }


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

        // JSON-Dateien verwenden "imageMain" anstelle von "image_main"
        if (isset($row['imageMain']) && !isset($row['image_main'])) {
            $row['image_main'] = $row['imageMain'];
        }

        // Einheitliche IDs bereitstellen ("id" und "iid" werden gegenseitig ergaenzt)
        if (isset($row['iid']) && !isset($row['id'])) {
            $row['id'] = $row['iid'];
        } elseif (isset($row['id']) && !isset($row['iid'])) {
            $row['iid'] = $row['id'];
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
        if ($db) {
            $stmt = $db->query("SELECT * FROM products");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map('mapProductRow', $rows);
        }

        return loadProductsFromJson();
    }

    function getProductById($id): ?array
    {
        global $db;
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ? mapProductRow($product) : null;
        }

        foreach (loadProductsFromJson() as $p) {
            if ((int)($p['id'] ?? $p['iid'] ?? 0) === (int)$id) {
                return $p;
            }
        }
        return null;
    }

    function getProductsByCategory($category): array
    {
        global $db;
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM products WHERE category = ?");
            $stmt->execute([$category]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map('mapProductRow', $rows);
        }

        return array_values(array_filter(loadProductsFromJson(), function ($p) use ($category) {
            return isset($p['category']) && strcasecmp($p['category'], $category) === 0;
        }));
    }

    function getProductsByCategoryAndSub($category, $subcategory): array
    {
        global $db;
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND subcategory = ?");
            $stmt->execute([$category, $subcategory]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map('mapProductRow', $rows);
        }

        return array_values(array_filter(loadProductsFromJson(), function ($p) use ($category, $subcategory) {
            return isset($p['category'], $p['subcategory']) &&
                   strcasecmp($p['category'], $category) === 0 &&
                   strcasecmp($p['subcategory'], $subcategory) === 0;
        }));
    }

    function addProduct(array $product): bool
    {
        global $db;
        if (!$db) {
            // Ohne Datenbank keine Persistenz
            return false;
        }

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
