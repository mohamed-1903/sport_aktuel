<?php
// controller/userController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'profile';

switch ($action) {
    case 'orders':
        require 'view/user/userOrdersView.php';
        break;

    case 'profile':
    default:
        require 'view/user/userProfileView.php';
        break;
}
