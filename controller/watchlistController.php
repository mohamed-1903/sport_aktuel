<?php
// Hussein

// controller/watchlistController.php
// Steuert die serverseitige Merkliste
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'model/watchlistModel.php';

// ID des angemeldeten Nutzers ermitteln
$userId = $_SESSION['user_id'] ?? null;
$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'add':
        // Einzelnes Produkt zur Merkliste hinzufügen
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
        // Produkt aus der Merkliste entfernen
        $id = $_POST['id'] ?? ($_GET['id'] ?? null);
        if ($userId && $id !== null) {
            removeFromWatchlist($userId, (int)$id);
        }
        header('Location: index.php?page=watchlist&action=view');
        exit;
    case 'toggle':
        // Merkliste-Eintrag umschalten (hinzufügen/entfernen)
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
        // Mehrere Produkte gleichzeitig toggeln
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
        // Lokale Merkliste mit Server abgleichen
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
        // Aktuelle Merkliste als JSON ausgeben
        header('Content-Type: application/json');
        if (!$userId) {
            echo json_encode([]);
            exit;
        }
        echo json_encode(getWatchlistItems($userId));
        exit;
    case 'count':
        // Anzahl der gemerkten Produkte liefern
        header('Content-Type: application/json');
        if (!$userId) {
            echo json_encode(['count' => 0]);
            exit;
        }
        echo json_encode(['count' => countWatchlistItems($userId)]);
        exit;
    case 'clear':
        // Merkliste komplett leeren
        if ($userId) {
            clearWatchlist($userId);
        }
        header('Location: index.php?page=watchlist&action=view');
        exit;
    case 'view':
    default:
        // Merkliste des Nutzers anzeigen
        if (!$userId) {
            header('Location: index.php?page=auth&action=login&redirect=watchlist');
            exit;
        }
        $watchlistItems = getWatchlistItems($userId);
        require 'view/watchlist/watchlistView.php';
        break;
}