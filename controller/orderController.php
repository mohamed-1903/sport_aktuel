<?php
// controller/orderController.php
require_once 'model/orderModel.php';
require_once 'model/cartModel.php';

session_start();

$action = $_GET['action'] ?? 'checkout';

if (in_array($action, ['admin', 'updateStatus'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: index.php?page=auth&action=login&unauthorized=1");
        exit;
    }
} else {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=auth&action=login&redirect=order");
        exit;
    }
}

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
            clearCart($_SESSION['user_id']);
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
            $orderId = (int)$_POST['order_id'];
            cancelOrder($orderId, $_SESSION['user_id']);
        }
        header("Location: index.php?page=user&action=orders");
        exit;

    case 'admin':
        $statusFilter = $_GET['status'] ?? 'neu';
        $orders = getOrdersByStatus($statusFilter);
        require 'view/admin/manageOrdersView.php';
        break;

    case 'updateStatus':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
            $orderId = (int)$_POST['order_id'];
            $newStatus = $_POST['status'];
            updateOrderStatus($orderId, $newStatus);
            $redirect = $_POST['redirect'] ?? 'neu';
            header("Location: index.php?page=order&action=admin&status=" . urlencode($redirect));
            exit;
        }
        header("Location: index.php?page=order&action=admin");
        exit;
}
