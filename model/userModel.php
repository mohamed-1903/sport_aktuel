<?php
//Laith


// model/userModel.php
// Die Funktionen sind bewusst getrennt, obwohl sie sich ähneln
// (z. B. getUserByEmail und getUserByUsername). Eine Generifizierung
// könnte Redundanz verringern, erhöht aber die Lesbarkeit hier kaum.
require_once 'model/db.php';

/**
 * Liefert einen Benutzer anhand seiner E-Mail-Adresse.
 *
 * @param string $email
 * @return array|null Benutzer-Datensatz oder null, wenn nicht vorhanden
 */
function getUserByEmail(string $email): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

/**
 * Holt einen Benutzer anhand des Benutzernamens.
 *
 * @param string $username
 * @return array|null
 */
function getUserByUsername(string $username): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

/**
 * Prüft Benutzerpasswort und startet die Session.
 * Gibt den Benutzer oder null zurück.
 */
function loginUser(string $email, string $password) {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['status'] === 'banned') {
            return ['banned' => true];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin']; // wichtig!
        return $user;
    }
    return null;
}

/**
 * Legt einen neuen Benutzer an, sofern Name und E-Mail einzigartig sind.
 *
 * @return array ['success' => bool, 'error' => string|null]
 */
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
/**
 * Entfernt einen Benutzer, wenn keine Bestellungen vorhanden sind.
 */
function deleteUserById(int $id): bool {
    global $db;

    // Löschen nur erlauben, wenn der Benutzer keine Bestellungen hat
    if (userHasOrders($id)) {
        return false;
    }

    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}
/**
 * Gibt eine Liste aller Benutzer zurück.
 */
function getAllUsers(): array {
    global $db;
    $stmt = $db->query("SELECT * FROM users ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Ermittelt, ob der Benutzer Bestellungen besitzt.
 */
function userHasOrders($userId): bool {
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Liefert den Benutzer mit der angegebenen ID.
 */
function getUserById(int $id): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

/**
 * Aktualisiert den Status eines Benutzers.
 */
function setUserStatus(int $id, string $status): bool {
    global $db;
    $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

/**
 * Schaltet den Benutzerstatus zwischen "banned" und "active" um.
 */
function toggleUserStatus(int $id): void {
    $user = getUserById($id);
    if ($user) {
        $newStatus = $user['status'] === 'banned' ? 'active' : 'banned';
        setUserStatus($id, $newStatus);
    }
}
