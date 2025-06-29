<?php
//Laith

// controller/userController.php
// Verwalten der Benutzeransicht

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'profile';

switch ($action) {
    case 'orders':
        // Zeigt die Bestellübersicht des Nutzers
        require 'view/user/userOrdersView.php';
        break;

    case 'profile':
    default:
        // Profilseite des Nutzers anzeigen
        require 'view/user/userProfileView.php';
        break;
}