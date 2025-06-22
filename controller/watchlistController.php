<?php
// controller/watchlistController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'model/watchlistModel.php';

$userId = $_SESSION['user_id'] ?? null;
$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = $data['id'] ?? $data['product_id'] ?? null;
            if ($id !== null) {
                addToWatchlist($userId, (int)$id);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'ok']);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error']);
            }
            exit;
        }
        break;
    case 'remove':
        $id = $_POST['id'] ?? ($_GET['id'] ?? null);
        if ($userId && $id !== null) {
            require_once 'model/productModel.php';
            $product = getProductById((int)$id);
            if ($product) {
                $_SESSION['watch_removed'] = [
                    'name' => $product['name'],
                    'image' => $product['image_main'],
                    'id' => (int)$id
                ];
            }
            removeFromWatchlist($userId, (int)$id);
        }
        header('Location: index.php?page=watchlist&action=view');
        exit;
    case 'toggle':
        header('Content-Type: application/json');
        if (!$userId) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Nicht eingeloggt']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['product_id'] ?? null;
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Fehlende ID']);
            exit;
        }
        if (isInWatchlist($userId, (int)$id)) {
            removeFromWatchlist($userId, (int)$id);
            echo json_encode(['status' => 'ok', 'in_watchlist' => false]);
        } else {
            addToWatchlist($userId, (int)$id);
            echo json_encode(['status' => 'ok', 'in_watchlist' => true]);
        }
        exit;
    case 'toggleBulk':
        header('Content-Type: application/json');
        if (!$userId) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Nicht eingeloggt']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['product_ids'] ?? null;
        if (!is_array($ids)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Fehlende IDs']);
            exit;
        }
        $result = toggleWatchlistBulk($userId, $ids);
        echo json_encode(['status' => 'ok', 'results' => $result, 'count' => countWatchlistItems($userId)]);
        exit;
    case 'sync':
        header('Content-Type: application/json');
        if (!$userId) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Nicht eingeloggt']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['watchlist'] ?? $data['ids'] ?? null;
        if (!is_array($ids)) {
            http_response_code(400);
            echo json_encode(['status' => 'error']);
            exit;
        }
        setWatchlistItems($userId, $ids);
        echo json_encode(['status' => 'ok']);
        exit;
    case 'json':
        header('Content-Type: application/json');
        if (!$userId) {
            echo json_encode([]);
            exit;
        }
        echo json_encode(getWatchlistItems($userId));
        exit;
    case 'count':
        header('Content-Type: application/json');
        if (!$userId) {
            echo json_encode(['count' => 0]);
            exit;
        }
        echo json_encode(['count' => countWatchlistItems($userId)]);
        exit;
    case 'clear':
        if ($userId) {
            clearWatchlist($userId);
        }
        header('Location: index.php?page=watchlist&action=view');
        exit;
    case 'view':
    default:
        if (!$userId) {
            header('Location: index.php?page=auth&action=login&redirect=watchlist');
            exit;
        }
        $watchlistItems = getWatchlistItems($userId);
        require 'view/watchlist/watchlistView.php';
        break;
}
