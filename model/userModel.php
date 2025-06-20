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

function registerUser(string $username, string $email, string $password): bool {
    global $db;
    if (getUserByEmail($email)) {
        return false; // Benutzer existiert bereits
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $email, $hash]);
}
function deleteUserById(int $id): bool {
    global $db;
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
    if (userHasOrders($userId)) {
    return false; // oder: Fehlermeldung „Benutzer hat noch offene Bestellungen.“
}

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
