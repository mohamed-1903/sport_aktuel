<?php
// controller/productController.php
require_once 'model/productModel.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'detail':
        // Alle Parameter, die mit "id" oder "iid" beginnen, einsammeln
        $ids = [];
        foreach ($_GET as $key => $value) {
            if (preg_match('/^id(\d*)$/', $key) || preg_match('/^iid(\d*)$/', $key)) {
                $values = is_array($value) ? $value : explode(',', $value);
                foreach ($values as $val) {
                    if (is_numeric($val)) {
                        $ids[] = (int)$val;
                    }
                }
            }
        }
        $ids = array_values(array_unique($ids));

        if (empty($ids)) {
            echo "Parameter 'id' fehlt!";
            exit;
        }

        $productsToShow = [];
        foreach ($ids as $pid) {
            $product = getProductById($pid);
            if ($product) {
                $product['iid'] = $product['id'];
                $product['priceValue'] = $product['price'];
                $productsToShow[] = $product;
            }
        }

        if (empty($productsToShow)) {
            echo "Kein Produkt gefunden.";
            exit;
        }

        $allProducts = getAllProducts();
        $currentId = $productsToShow[0]['id'];

        require 'view/product/productDetailView.php';
        break;

    case 'list':
    default:
        $category = $_GET['category'] ?? '';
        $subcategory = $_GET['subcategory'] ?? '';
        $saleOnly = isset($_GET['sale']) && $_GET['sale'] === '1';

        if (!$category && $subcategory) {
            $map = [
                "Trikots" => "Sportbekleidung",
                "Socken" => "Sportbekleidung",
                "Handschuhe" => "Sportbekleidung",
                "Trainingsanzüge" => "Sportbekleidung",
                "Stollen" => "Fußballschuhe",
                "Kunstrasen" => "Fußballschuhe",
                "Hallenschuhe" => "Fußballschuhe",
                "Schienbeinschoner" => "Zubehör",
                "Fußbälle" => "Zubehör",
                "Sporttaschen" => "Zubehör"
            ];
            $category = $map[$subcategory] ?? '';
        }

        if (!$category) {
            echo "<h2>⚠️ Keine Kategorie angegeben.</h2>";
            exit;
        }

        $products = getAllProducts();

        $filteredProducts = array_filter($products, function ($p) use ($category, $subcategory, $saleOnly) {
            $matchCat = strcasecmp($p['category'], $category) === 0;
            $matchSub = !$subcategory || strcasecmp($p['subcategory'], $subcategory) === 0;
            $matchSale = !$saleOnly || (!empty($p['discount']) && $p['discount'] > 0);
            return $matchCat && $matchSub && $matchSale;
        });

        // Standardisiere Datenstruktur für View
        foreach ($filteredProducts as &$p) {
            $p['iid'] = $p['id'];
            $p['priceValue'] = $p['price'];
        }

        $displayTitle = $subcategory ?: $category;
        require 'view/product/productListView.php';
        break;
}
