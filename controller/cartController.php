<?php
// controller/cartController.php

require_once 'model/cartModel.php';
require_once 'model/productModel.php';

$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            if (is_array($data) && isset($data['id'], $data['name'], $data['price'], $data['image'], $data['size'], $data['quantity'])) {
                addToCart([
                    'id' => (int)$data['id'],
                    'name' => trim($data['name']),
                    'price' => (float)$data['price'],
                    'image' => trim($data['image']),
                    'size' => trim($data['size']),
                    'quantity' => max(1, (int)$data['quantity'])
                ]);
                session_write_close();
                http_response_code(200);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
            }

            exit;
        }
        break;

    case 'remove':
        // Support sowohl GET- als auch POST-Anfragen, damit der
        // Entfernen-Button als Formular genutzt werden kann
        $id = $_POST['id'] ?? ($_GET['id'] ?? null);
        $size = $_POST['size'] ?? ($_GET['size'] ?? null);
        if ($id !== null && $size !== null) {
            removeFromCart((int)$id, trim($size));
            session_write_close();
        }
        header("Location: index.php?page=cart&action=view");
        exit;

    case 'update':
        $id = $_POST['id'] ?? null;
        $size = $_POST['size'] ?? null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
           if ($id !== null && $size !== null && $quantity > 0) {
            updateCartQuantity((int)$id, trim($size), $quantity);
            session_write_close();
        }
        header("Location: index.php?page=cart&action=view");
        exit;

    case 'view':
    default:
        $cartItems = getCartItems();
        require 'view/cart/cartView.php';
        break;
}
