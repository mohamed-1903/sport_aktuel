<?php
// controller/returnController.php
// Kümmert sich um Rücksendeanfragen
require_once 'model/returnModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=auth&action=login&redirect=return");
    exit;
}

$action = $_GET['action'] ?? 'form';

switch ($action) {
    case 'submit':
        // Rücksendeanfrage abschicken
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $reason = trim($_POST['reason'] ?? '');
        if ($orderId > 0 && $reason !== '') {
            requestReturn($orderId, $_SESSION['user_id'], $reason);
            header("Location: index.php?page=return&action=success");
            exit;
        }
        $error = 'Bitte einen Grund angeben.';
        $orderId = $orderId ?: ($_GET['order_id'] ?? '');
        require 'view/return/returnFormView.php';
        break;

    case 'success':
        // Erfolgreiche Anfrage anzeigen
        require 'view/return/returnSuccessView.php';
        break;

    case 'form':
    default:
        // Formular für Rücksendeanfrage laden
        $orderId = $_GET['order_id'] ?? '';
        require 'view/return/returnFormView.php';
        break;
}
