<?php
// controller/orderController.php
require_once 'model/orderModel.php';
require_once 'model/cartModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=auth&action=login&redirect=order");
    exit;
}


$action = $_GET['action'] ?? 'checkout';

switch ($action) {
    case 'checkout':
        $cartItems = getCartItems($_SESSION['user_id']);
        if (empty($cartItems)) {
            header("Location: index.php?page=cart&action=view");
            exit;
        }
        require 'view/order/checkoutView.php';
        break;

    case 'submit':
        $cartItems = getCartItems($_SESSION['user_id']);
        if (empty($cartItems)) {
            header("Location: index.php?page=cart&action=view");
            exit;
        }

        $success = saveOrder($_SESSION['user_id'], $cartItems);

        if ($success) {
            clearCart();
            header("Location: index.php?page=order&action=success");
            exit;
        } else {
            $error = "Bestellung konnte nicht gespeichert werden.";
            require 'view/order/checkoutView.php';
        }
        break;

    case 'success':
        require 'view/order/checkoutSuccessView.php';
        break;

    case 'cancel':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int) ($_GET['id'] ?? 0);
            $userId = $_SESSION['user_id'] ?? null;

            if ($orderId && $userId) {
                cancelOrderIfNew($orderId, $userId);
                $_SESSION['message'] = '✅ Bestellung wurde erfolgreich storniert.';
            }

            // Kein harter Redirect, sondern Soft-Reload:
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
        break;
}