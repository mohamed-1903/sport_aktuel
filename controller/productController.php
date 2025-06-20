<?php
// controller/productController.php
require_once 'model/productModel.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'detail':
        $id = $_GET['id'] ?? null;
        $id2 = $_GET['id2'] ?? null;

        if (!$id && !$id2) {
            echo "Parameter 'id' oder 'id2' fehlen!";
            exit;
        }

        $productsToShow = [];

        if ($id && !$id2) {
            $product = getProductById($id);
            if (!$product) {
                echo "Produkt nicht gefunden.";
                exit;
            }
            $product['iid'] = $product['id'];
            $product['priceValue'] = $product['price'];
            $productsToShow[] = $product;
        } elseif ($id && $id2) {
            $p1 = getProductById($id);
            $p2 = getProductById($id2);
            if (!$p1 || !$p2) {
                echo "Eines oder beide Produkte nicht gefunden.";
                exit;
            }
            foreach ([$p1, $p2] as &$p) {
                $p['iid'] = $p['id'];
                $p['priceValue'] = $p['price'];
            }
            $productsToShow = [$p1, $p2];
        }

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
