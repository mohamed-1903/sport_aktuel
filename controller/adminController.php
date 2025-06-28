<?php
// controller/adminController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Sicherheitsabfrage (Admin-Berechtigung)
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php?page=auth&action=login&unauthorized=1");
    exit;
}


require_once 'model/userModel.php';
require_once 'model/productModel.php';

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        require 'view/admin/adminDashboardView.php';
        break;

    case 'manageUsers':
        $allUsers = getAllUsers();
        require 'view/admin/manageUsersView.php';
        break;


    case 'deleteUser':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = (int)$_POST['user_id'];
            if ($userId !== 1) { // Admin darf sich selbst nicht löschen
                deleteUserById($userId);
            }
        }
        header("Location: index.php?page=admin&action=manageUsers");
        exit;

    case 'toggleUserStatus':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = (int)$_POST['user_id'];
            if ($userId !== 1) {
                toggleUserStatus($userId);
            }
        }
        header("Location: index.php?page=admin&action=manageUsers");
        exit;

    case 'manageProducts':
        $allProducts = getAllProducts();
        require 'view/admin/manageProductsView.php';
        break;

    case 'updateDiscount':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pid = (int)($_POST['product_id'] ?? 0);
            $discount = max(0, min(90, (int)($_POST['discount'] ?? 0)));
            if ($pid) {
                updateProductDiscount($pid, $discount);
            }
        }
        header('Location: index.php?page=admin&action=manageProducts');
        exit;


    case 'deleteProduct':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $pid = (int)$_POST['product_id'];
            if ($pid) {
                deleteProduct($pid);
            }
        }
        header('Location: index.php?page=admin&action=manageProducts');
        exit;
    case 'addProduct':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = [
                'name' => $_POST['name'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'category' => $_POST['category'] ?? '',
                'subcategory' => $_POST['subcategory'] ?? '',
                'description' => $_POST['description'] ?? '',
                'imageMain' => $_POST['imageMain'] ?? '',
                'sizes' => isset($_POST['sizes']) ? explode(',', $_POST['sizes']) : [],
                'marke' => $_POST['marke'] ?? '',
                'farbe' => $_POST['farbe'] ?? '',
                'geschlecht' => $_POST['geschlecht'] ?? ''
            ];
            addProduct($product);
            header("Location: index.php?page=admin&action=dashboard");
            exit;
        }
        require 'view/admin/addProductView.php';
        break;

    default:
        require 'view/admin/adminDashboardView.php';
        break;
}
