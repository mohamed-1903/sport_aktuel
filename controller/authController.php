<?php
// controller/authController.php
require_once 'model/userModel.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $success = registerUser($username, $email, $password);

            if ($success) {
                header("Location: index.php?page=auth&action=login&registered=1");
                exit;
            } else {
                $error = "Registrierung fehlgeschlagen. Benutzer existiert vielleicht schon.";
            }
        }
        require 'view/auth/registerView.php';
        break;

    case 'logout':
        session_start();
        session_destroy();
        header("Location: index.php?page=auth&action=login&logout=1");
        exit;

case 'login':
default:
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = loginUser($email, $password);

        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
$_SESSION['is_admin'] = $user['is_admin'];if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
  header('Location: index.php?page=auth&action=login');
  exit;
}

            $redirect = $_GET['redirect'] ?? 'index';
            header("Location: index.php?page={$redirect}");
            exit;
        } else {
            $_SESSION['login_error'] = "Login fehlgeschlagen. Bitte überprüfe deine Zugangsdaten.";
            header("Location: index.php?page=auth&action=login");
            exit;
        }
    }
    require 'view/auth/loginView.php';
    break;

}
