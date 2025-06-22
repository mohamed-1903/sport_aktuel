<?php
// controller/cartController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

require_once 'model/cartModel.php';
require_once 'model/productModel.php';

$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $expectJson = isset($_SERVER['CONTENT_TYPE']) &&
                strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
            if ($expectJson) {
                header('Content-Type: application/json');
            }

            if (!$userId) {
                http_response_code(403);
                echo json_encode(['error' => 'Nicht eingeloggt']);
                exit;
            }

            if (
                isset($_SERVER['CONTENT_TYPE']) &&
                strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
            ) {
                $data = json_decode(file_get_contents('php://input'), true);
            } else {
                $data = $_POST;
            }

            $id = $data['product_id'] ?? $data['id'] ?? null;
            $size = isset($data['size']) ? trim($data['size']) : null;
            $quantity = $data['quantity'] ?? $data['qty'] ?? null;
            if ($id !== null && $size !== null && $size !== '' && $quantity !== null) {
                try {
                    addToCart($userId, [
                        'id' => (int)$id,
                        'size' => trim($size),
                        'quantity' => max(1, (int)$quantity),
                        'discount' => isset($data['discount']) ? (int)$data['discount'] : 0,
                        'gift' => !empty($data['gift'])
                    ]);
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Datenbankfehler']);
                    exit;
                }
                session_write_close();

                if ($expectJson) {
                    echo json_encode(['status' => 'ok']);
                } else {
                    header('Location: index.php?page=cart');
                }
            } else {
                http_response_code(400);
                $msg = 'Invalid input';
                if ($size === null || $size === '') {
                    $msg = 'Groesse erforderlich';
                }
                echo json_encode(['error' => $msg]);
            }

            exit;
        }
        break;

    case 'remove':
        if (!$userId) {
            header("Location: index.php?page=auth&action=login&redirect=cart");
            exit;
        }

        $id = $_POST['id'] ?? ($_GET['id'] ?? null);
        $size = isset($_POST['size']) ? trim($_POST['size']) : (isset($_GET['size']) ? trim($_GET['size']) : null);
        if ($id !== null && $size !== null && $size !== '') {
            removeFromCart($userId, (int)$id, trim($size));
            session_write_close();
        }
        header("Location: index.php?page=cart&action=view");
        exit;
        break;

    case 'update':
        if (!$userId) {
            header("Location: index.php?page=auth&action=login&redirect=cart");
            exit;
        }

        $id = $_POST['id'] ?? null;
        $size = isset($_POST['size']) ? trim($_POST['size']) : null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($id !== null && $size !== null && $size !== '' && $quantity > 0) {
            updateCartQuantity($userId, (int)$id, trim($size), $quantity);
            session_write_close();
        }
        header("Location: index.php?page=cart&action=view");
        exit;
    case 'count':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['count' => 0]);
            exit;
        }

        echo json_encode(['count' => countCartItems($userId)]);
        exit;
        break;

    case 'clear':
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            clearCart($userId);
        }
        header('Location: index.php?page=cart');
        exit;
        break;

    case 'json':
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode([]);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(getCartItems($userId));
        exit;
        break;

    case 'toggle':
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Nicht eingeloggt']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['product_id'], $data['size'], $data['qty']) || trim($data['size']) === '') {
            $msg = trim($data['size']) === '' ? 'Groesse erforderlich' : 'Fehlende Felder';
            echo json_encode([
                'status' => 'error',
                'message' => $msg
            ]);
            exit;
        }


        $items = getCartItems($userId);
        $inCart = false;
        foreach ($items as $item) {
            if ($item['product_id'] == $data['product_id'] && $item['size'] == $data['size']) {
                removeFromCart($userId, $data['product_id'], $data['size']);
                echo json_encode(['status' => 'ok', 'in_cart' => false]);
                exit;
            }
        }

        try {
            addToCart($userId, [
                'id' => (int)$data['product_id'],
                'size' => trim($data['size']),
                'quantity' => (int)$data['qty'],
                'discount' => isset($data['discount']) ? (int)$data['discount'] : 0,
                'gift' => !empty($data['gift'])
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Datenbankfehler']);
            exit;
        }
        echo json_encode(['status' => 'ok', 'in_cart' => true]);
        exit;
        break;


    case 'view':
    default:
        if (!$userId) {
            header("Location: index.php?page=auth&action=login&redirect=cart");
            exit;
        }

        $cartItems = getCartItems($userId);
        require 'view/cart/cartView.php';
        break;
}
