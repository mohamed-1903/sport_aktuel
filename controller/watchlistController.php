<?php
// controller/watchlistController.php

require_once 'model/watchlistModel.php';

$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_array($data) && isset($data['id'], $data['name'], $data['price'], $data['image'])) {
                addToWatchlist([
                    'id' => (int)$data['id'],
                    'name' => trim($data['name']),
                    'price' => (float)$data['price'],
                    'image' => trim($data['image'])
                ]);
                http_response_code(200);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
            }
            exit;
        }
        break;
    case 'remove':
        $id = $_GET['id'] ?? null;
        if ($id !== null) {
            removeFromWatchlist((int)$id);
        }
        header("Location: index.php?page=watchlist&action=view");
        exit;
    case 'sync':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_array($data) && isset($data['watchlist']) && is_array($data['watchlist'])) {
                setWatchlistItems($data['watchlist']);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            }
            exit;
        }
        break;
    case 'view':
    default:
        $watchlistItems = getWatchlistItems();
        require 'view/watchlist/watchlistView.php';
        break;
}