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
            $parentId = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $stars = isset($_POST['stars']) ? (int)$_POST['stars'] : 0;
            $displayName = trim($_POST['display_name'] ?? '');
            $comment = trim($_POST['comment'] ?? '');
            if (!$userId || $productId <= 0 || $stars < 1 || $stars > 5) {
                header('Location: index.php?page=product&action=detail&id=' . $productId);
                exit;
            }
            $imagePaths = [];
            if (!empty($_FILES['images']['name'][0])) {
                $dir = 'uploads/ratings';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $count = min(count($_FILES['images']['name']), 5);
                for ($i = 0; $i < $count; $i++) {
                    if (is_uploaded_file($_FILES['images']['tmp_name'][$i])) {
                        $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                        $filename = uniqid('rating_', true) . '.' . $ext;
                        move_uploaded_file($_FILES['images']['tmp_name'][$i], "$dir/$filename");
                        $imagePaths[] = "$dir/$filename";
                    }
                }
            }
            addRating($productId, $userId, $displayName ?: ($_SESSION['username'] ?? ''), $stars, $comment, $imagePaths, $parentId);

            $_SESSION['message'] = 'Danke für deine Bewertung!';

            header('Location: index.php?page=product&action=detail&id=' . $productId);
            exit;
        }
        break;
    case 'deleteRating':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ratingId = isset($_POST['rating_id']) ? (int)$_POST['rating_id'] : 0;
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $userId = $_SESSION['user_id'] ?? 0;
            $isAdmin = !empty($_SESSION['is_admin']);
            if ($ratingId > 0 && $productId > 0) {
                deleteRating($ratingId, $userId, $isAdmin);
            }
            header('Location: index.php?page=product&action=detail&id=' . $productId);
            exit;
        }
        break;
    case 'likeRating':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ratingId = isset($_POST['rating_id']) ? (int)$_POST['rating_id'] : 0;
            header('Content-Type: application/json');
            echo json_encode(likeRating($ratingId));
            exit;
        }
        break;
    case 'dislikeRating':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ratingId = isset($_POST['rating_id']) ? (int)$_POST['rating_id'] : 0;
            header('Content-Type: application/json');
            echo json_encode(dislikeRating($ratingId));
            exit;
        }
        break;
    default:
        http_response_code(404);
        echo 'Unbekannte Aktion';
}
