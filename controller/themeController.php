<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$action = $_GET['action'] ?? 'toggle';

switch ($action) {
    case 'toggle':
        $current = $_SESSION['theme'] ?? 'dark';
        $new = $current === 'light' ? 'dark' : 'light';
        $_SESSION['theme'] = $new;
        header('Content-Type: application/json');
        echo json_encode(['theme' => $new]);
        break;
    case 'set':
        $theme = $_POST['theme'] ?? 'dark';
        $_SESSION['theme'] = $theme === 'light' ? 'light' : 'dark';
        header('Content-Type: application/json');
        echo json_encode(['theme' => $_SESSION['theme']]);
        break;
    case 'get':
        header('Content-Type: application/json');
        echo json_encode(['theme' => $_SESSION['theme'] ?? 'dark']);
        break;
    default:
        header('Location: index.php');
        break;
}
