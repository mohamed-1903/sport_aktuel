<?php
// controller/orderController.php
require_once 'model/orderModel.php';
require_once 'model/cartModel.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=auth&action=login&redirect=order");
    exit;
}

$action = $_GET['action'] ?? 'checkout';

switch ($action) {
    case 'checkout':
        $cartItems = getCartItems();
        if (empty($cartItems)) {
            header("Location: index.php?page=cart&action=view");
            exit;
        }
        require 'view/order/checkoutView.php';
        break;

    case 'submit':
        $cartItems = getCartItems();
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
}
