<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'model/ratingModel.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'addRating':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? null;
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $stars = isset($_POST['stars']) ? (int)$_POST['stars'] : 0;
            $comment = trim($_POST['comment'] ?? '');
            if (!$userId || $productId <= 0 || $stars < 1 || $stars > 5) {
                header('Location: index.php?page=product&action=detail&id=' . $productId);
                exit;
            }
            $imagePath = null;
            if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $dir = 'uploads/ratings';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('rating_', true) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], "$dir/$filename");
                $imagePath = "$dir/$filename";
            }
            addRating($productId, $userId, $stars, $comment, $imagePath);
            header('Location: index.php?page=product&action=detail&id=' . $productId);
            exit;
        }
        break;
    default:
        http_response_code(404);
        echo 'Unbekannte Aktion';
}
