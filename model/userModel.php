<?php
// model/userModel.php
require_once 'model/db.php';

function getUserByEmail(string $email): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

// Neuen Benutzer anhand des Benutzernamens ermitteln
function getUserByUsername(string $username): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function loginUser(string $email, string $password): ?array {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin']; // wichtig!
        return $user;
    }
    return null;
}

function registerUser(string $username, string $email, string $password): array {
    global $db;

    if (getUserByUsername($username)) {
        return ['success' => false, 'error' => 'Benutzername bereits vergeben.'];
    }

    if (getUserByEmail($email)) {
        return ['success' => false, 'error' => 'E-Mail bereits vergeben.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $success = $stmt->execute([$username, $email, $hash]);
    return ['success' => $success];
}
function deleteUserById(int $id): bool {
    global $db;

    // Löschen nur erlauben, wenn der Benutzer keine Bestellungen hat
    if (userHasOrders($id)) {
        return false;
    }

    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}
function getAllUsers(): array {
    global $db;
    $stmt = $db->query("SELECT * FROM users ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function userHasOrders($userId): bool {
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() > 0;
}
