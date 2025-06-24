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

    case 'admin':
        if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header("Location: index.php?page=auth&action=login&unauthorized=1");
            exit;
        }

        $allowedStatuses = ['neu', 'in_bearbeitung', 'abgelehnt', 'abgeschlossen', 'storniert'];
        $statusFilter = $_GET['status'] ?? 'neu';
        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'neu';
        }

        $orders = getOrdersByStatus($statusFilter);
        require 'view/admin/manageOrdersView.php';
        break;

    case 'updateStatus':
        if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header("Location: index.php?page=auth&action=login&unauthorized=1");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $allowed = ['neu', 'in_bearbeitung', 'abgelehnt', 'abgeschlossen', 'storniert'];

            if ($orderId && in_array($status, $allowed, true)) {
                updateOrderStatus($orderId, $status);
            }

            $redirect = $_POST['redirect'] ?? 'neu';
            header("Location: index.php?page=order&action=admin&status=" . urlencode($redirect));
            exit;
        }
        break;
}
