<?php
// controller/orderController.php
// Steuerung des gesamten Bestellprozesses
require_once 'model/orderModel.php';
require_once 'model/cartModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=auth&action=login&redirect=order");
    exit;
}


$action = $_GET['action'] ?? 'checkout';

switch ($action) {
    case 'checkout':
        // Anzeige der Zusammenfassung vor dem Kauf
        $cartItems = getCartItems($_SESSION['user_id']);
        if (empty($cartItems)) {
            header("Location: index.php?page=cart&action=view");
            exit;
        }

        $orderCount = countOrdersByUser($_SESSION['user_id']);
        $next = $orderCount + 1;
        $discountPercent = 0;
        if ($next % 20 === 0) {
            $discountPercent = 20;
        } elseif ($next % 10 === 0) {
            $discountPercent = 10;
        }

        require 'view/order/checkoutView.php';
        break;

    case 'submit':
        // Bestellung abschicken
        $cartItems = getCartItems($_SESSION['user_id']);
        if (empty($cartItems)) {
            header("Location: index.php?page=cart&action=view");
            exit;
        }

        $orderCount = countOrdersByUser($_SESSION['user_id']);
        $next = $orderCount + 1;
        $discountPercent = 0;
        if ($next % 20 === 0) {
            $discountPercent = 20;
        } elseif ($next % 10 === 0) {
            $discountPercent = 10;
        }
        $_SESSION['last_discount_percent'] = $discountPercent;

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
        // Erfolgsseite nach Bestellung
        $discountPercent = $_SESSION['last_discount_percent'] ?? 0;
        unset($_SESSION['last_discount_percent']);
        require 'view/order/checkoutSuccessView.php';
        break;

    case 'cancel':
        // Bestellung stornieren, solange sie neu ist
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
        // Admin-Übersicht aller Bestellungen
        if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header("Location: index.php?page=auth&action=login&unauthorized=1");
            exit;
        }

        $allowedStatuses = ['neu', 'bestellt', 'versandt_nicht_erhalten', 'in_bearbeitung', 'abgelehnt', 'abgeschlossen', 'storniert'];
        $statusFilter = $_GET['status'] ?? 'neu';
        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'neu';
        }

        $orders = getOrdersByStatus($statusFilter);
        require 'view/admin/manageOrdersView.php';
        break;

    case 'updateStatus':
        // Status einer Bestellung ändern
        if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header("Location: index.php?page=auth&action=login&unauthorized=1");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $reason = $_POST['reason'] ?? null;
            $allowed = ['neu', 'bestellt', 'versandt_nicht_erhalten', 'in_bearbeitung', 'abgelehnt', 'abgeschlossen', 'storniert'];

            if ($status === 'abgelehnt' && trim((string)$reason) === '') {
                $_SESSION['message'] = 'Bitte gib einen Ablehnungsgrund an.';
            } elseif ($orderId && in_array($status, $allowed, true)) {
                updateOrderStatus($orderId, $status, $reason);
                $_SESSION['message'] = 'Status aktualisiert.';
            }

            $redirect = $_POST['redirect'] ?? 'neu';
            header("Location: index.php?page=order&action=admin&status=" . urlencode($redirect));
            exit;
        }
        break;
}
