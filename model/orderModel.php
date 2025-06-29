<?php
//Laith


// model/orderModel.php
require_once 'model/db.php';

// Dieses Model stellt Funktionen bereit, um Bestellungen in der Datenbank
// anzulegen und deren Status zu verwalten.

// Speichert eine neue Bestellung für den angegebenen Nutzer. Die einzelnen
// Warenkorbdaten werden als JSON im Feld admin_comment abgelegt.
function saveOrder(int $userId, array $cartItems): bool {
    global $db;

    $orderData = json_encode($cartItems);

    $stmt = $db->prepare("INSERT INTO orders (user_id, status, admin_comment, created_at) VALUES (?, 'neu', '', NOW())");
    if ($stmt->execute([$userId])) {
        $orderId = $db->lastInsertId();

        $stmtData = $db->prepare("UPDATE orders SET admin_comment = ? WHERE id = ?");
        return $stmtData->execute([$orderData, $orderId]);
    }

    return false;
}

// Setzt den Status einer Bestellung auf "storniert", sofern sie noch den
// Status "neu" besitzt. Diese Logik überschneidet sich mit cancelOrder().
function cancelOrderIfNew(int $orderId, int $userId): void {
    global $db;

    $stmt = $db->prepare("
        UPDATE orders 
        SET status = 'storniert', updated_at = NOW() 
        WHERE id = ? AND user_id = ? AND status = 'neu'
    ");
    $stmt->execute([$orderId, $userId]);
}


// Liefert alle Bestellungen eines Nutzers sortiert nach dem Erstellungsdatum.
function getOrdersByUser(int $userId): array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Gibt alle Bestellungen zurück (Admin-Ansicht).
function getAllOrders(): array {
    global $db;
    $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Filtert Bestellungen anhand eines Statuswertes.
function getOrdersByStatus(string $status): array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Liefert eine einzelne Bestellung anhand ihrer ID.
function getOrderById(int $orderId): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    return $order ?: null;
}

// Aktualisiert den Status einer Bestellung. Bei Ablehnung wird ein Grund gespeichert.
function updateOrderStatus(int $orderId, string $status, string $reason = null): bool {
    global $db;
    if ($status === 'abgelehnt') {
        $stmt = $db->prepare("UPDATE orders SET status = ?, rejection_reason = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $reason, $orderId]);
    }

    $stmt = $db->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$status, $orderId]);
}

// Alternative Stornofunktion mit Rueckmeldung, ob ein Datensatz geaendert wurde.
function cancelOrder(int $orderId, int $userId): bool {
    global $db;
    $stmt = $db->prepare("UPDATE orders SET status = 'storniert', updated_at = NOW() WHERE id = ? AND user_id = ? AND status = 'neu'");
    $stmt->execute([$orderId, $userId]);
    return $stmt->rowCount() > 0;
}

// Zählt alle nicht stornierten Bestellungen eines Nutzers.
function countOrdersByUser(int $userId): int {
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status != 'storniert'");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}
