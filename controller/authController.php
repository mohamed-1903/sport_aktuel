<?php
// controller/authController.php
require_once 'model/userModel.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'check':
        header('Content-Type: application/json');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];
        $username = $_POST['username'] ?? ($data['username'] ?? null);
        $email = $_POST['email'] ?? ($data['email'] ?? null);
        $response = [];
        if ($username !== null) {
            $response['usernameTaken'] = getUserByUsername($username) !== null;
        }
        if ($email !== null) {
            $response['emailTaken'] = getUserByEmail($email) !== null;
        }
        echo json_encode($response);
        exit;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = registerUser($username, $email, $password);

            if ($result['success']) {
                header("Location: index.php?page=auth&action=login&registered=1");
                exit;
            } else {
                $error = $result['error'] ?? 'Registrierung fehlgeschlagen.';
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
                $_SESSION['is_admin'] = $user['is_admin'];


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
