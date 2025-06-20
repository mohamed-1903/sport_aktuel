<?php
// controller/returnController.php
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
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $reason  = trim($_POST['reason'] ?? '');

        if ($orderId <= 0) {
            $error = 'Bitte eine gültige Bestellung auswählen.';
        } elseif ($reason === '') {
            $error = 'Bitte einen Grund angeben.';
        } else {
            requestReturn($orderId, $_SESSION['user_id'], $reason);
            header("Location: index.php?page=return&action=success");
            exit;
        }

        require 'view/return/returnFormView.php';
        break;

    case 'success':
        require 'view/return/returnSuccessView.php';
        break;

    case 'form':
    default:
        $orderId = $_GET['order_id'] ?? '';
        require 'view/return/returnFormView.php';
        break;
}
